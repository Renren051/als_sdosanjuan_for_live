<?php

namespace Tests\Feature;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndToEndPilotJourneyTest extends TestCase
{
    protected User $admin;
    protected User $teacherJuan;
    protected User $teacherAlyssa;
    protected CommunityLearningCenter $clcWestCrame;
    protected CommunityLearningCenter $clcBatis;
    protected Module $sampleModule;

    protected function setUp(): void
    {
        @ini_set('memory_limit', '512M');
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::where('username', 'admin_als')->firstOrFail();
        $this->teacherJuan = User::where('username', 'teacher_juan')->firstOrFail();
        $this->teacherAlyssa = User::where('username', 'teacher_alyssa')->firstOrFail();

        $this->clcWestCrame = CommunityLearningCenter::where('clc_code', 'CLC-SJ-WCR-01')->firstOrFail();
        $this->clcBatis = CommunityLearningCenter::where('clc_code', 'CLC-SJ-BAT-01')->firstOrFail();

        $this->clcWestCrame->update(['assigned_teacher_id' => $this->teacherJuan->id]);
        $this->clcBatis->update(['assigned_teacher_id' => $this->teacherAlyssa->id]);

        // Ensure sample published module exists
        $strand = LearningStrand::first();
        if (!$strand) {
            $strand = LearningStrand::create([
                'strand_code' => 'LS1-CS',
                'title' => 'Communication Skills (English)',
                'description' => 'ALS Communication Skills Strand',
            ]);
        }

        $module = Module::first();
        if (!$module) {
            $module = Module::create([
                'learning_strand_id' => $strand->id,
                'module_number' => 'MOD-101',
                'title' => 'Effective Communication in the Community',
                'description' => 'Pilot ALS Learning Module',
                'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
                'level' => 'Junior High School',
                'file_path' => 'modules/sample_module.pdf',
                'file_size' => 204800,
                'is_published' => true,
            ]);
        }
        $this->sampleModule = $module;
    }

    /**
     * E2E Scenario 1: Representative Learner Journey from Online Registration
     * to Document Verification, CLC Placement, Mobile Offline Sync, and Official DepEd Reporting.
     */
    public function test_end_to_end_representative_learner_journey(): void
    {
        $uniqueSuffix = Str::random(6);
        $username = 'pilot_learner_' . $uniqueSuffix;
        $email = 'pilot_' . $uniqueSuffix . '@sdosanjuan.edu';
        $password = 'SecretPassword123!';

        // -------------------------------------------------------------
        // Step 1: Online Intake Registration via API/Wizard
        // -------------------------------------------------------------
        $regResponse = $this->postJson('/api/v1/auth/register', [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'first_name' => 'Eduardo',
            'last_name' => 'Dela Cruz',
            'middle_name' => 'Bautista',
            'birthdate' => '2005-08-20',
            'gender' => 'male',
            'civil_status' => 'single',
            'contact_number' => '09191234567',
            'street_address' => '789 N. Domingo St.',
            'barangay' => 'West Crame',
            'city' => 'San Juan City',
            'highest_education_attained' => 'Grade 8 Completer',
            'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
            'clc_id' => $this->clcWestCrame->id,
            'delivery_mode' => 'modular_offline',
            'is_4ps_beneficiary' => false,
            'is_ip' => false,
            'is_pwd' => false,
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'username',
                        'email',
                        'enrollment' => ['id', 'tracking_number', 'enrollment_status'],
                    ],
                ],
            ]);

        $newLearnerId = $regResponse->json('data.user.id');
        $enrollmentId = $regResponse->json('data.user.enrollment.id');
        $trackingNumber = $regResponse->json('data.user.enrollment.tracking_number');
        $this->assertNotEmpty($trackingNumber);

        $learner = User::findOrFail($newLearnerId);
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $this->assertEquals($trackingNumber, $enrollment->tracking_number);
        $this->assertEquals(Enrollment::STATUS_PENDING, $enrollment->enrollment_status);

        // -------------------------------------------------------------
        // Step 2: Document Upload by Learner
        // -------------------------------------------------------------
        $fakePsa = UploadedFile::fake()->create('psa_birth_cert.pdf', 150, 'application/pdf');
        $docResponse = $this->actingAs($learner, 'sanctum')->post('/api/v1/learner/documents/upload', [
            'document_type' => LearnerDocument::TYPE_PSA_BIRTH_CERT,
            'document_file' => $fakePsa,
        ]);
        $docResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('learner_documents', [
            'user_id' => $learner->id,
            'document_type' => LearnerDocument::TYPE_PSA_BIRTH_CERT,
            'verification_status' => LearnerDocument::STATUS_PENDING,
        ]);

        // -------------------------------------------------------------
        // Step 3: Staff Document Review, CLC Placement & Teacher Designation
        // -------------------------------------------------------------
        $doc = LearnerDocument::where('user_id', $learner->id)->firstOrFail();
        $doc->update([
            'verification_status' => LearnerDocument::STATUS_VERIFIED,
            'reviewed_by' => $this->teacherJuan->id,
            'reviewed_at' => now(),
        ]);

        $enrollment->update([
            'clc_id' => $this->clcWestCrame->id,
            'assigned_teacher_id' => $this->teacherJuan->id,
            'enrollment_status' => Enrollment::STATUS_ENROLLED,
            'enrolled_at' => now(),
        ]);

        $learner->update([
            'lrn' => '12' . rand(1000000000, 9999999999),
        ]);

        // -------------------------------------------------------------
        // Step 4: Mobile LMS API Login & Status Check
        // -------------------------------------------------------------
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'login' => $username,
            'password' => $password,
        ]);
        $loginResponse->assertOk()
            ->assertJsonPath('success', true);

        $mobileToken = $loginResponse->json('data.token');

        $statusResponse = $this->withHeader('Authorization', 'Bearer ' . $mobileToken)
            ->getJson('/api/v1/learner/enrollment-status');

        $statusResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'enrolled')
            ->assertJsonPath('data.clc.clc_name', $this->clcWestCrame->clc_name)
            ->assertJsonPath('data.assigned_teacher.name', $this->teacherJuan->full_name);

        // -------------------------------------------------------------
        // Step 5: Android Offline Telemetry Progress Sync
        // -------------------------------------------------------------
        $syncPayload = [
            'device_identifier' => 'samsung_sm_a125f_pilot',
            'items' => [
                [
                    'module_id' => $this->sampleModule->id,
                    'downloaded_at' => now()->subHours(5)->toISOString(),
                    'last_opened_at' => now()->subHours(1)->toISOString(),
                    'completion_status' => 'completed',
                    'score' => 95.0,
                ],
            ],
        ];

        $syncResponse = $this->withHeader('Authorization', 'Bearer ' . $mobileToken)
            ->postJson('/api/v1/learner/sync-progress', $syncPayload);

        $syncResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.synced_count', 1);

        $this->assertDatabaseHas('module_downloads_and_progress', [
            'user_id' => $learner->id,
            'module_id' => $this->sampleModule->id,
            'completion_status' => 'completed',
        ]);

        // -------------------------------------------------------------
        // Step 6: Official DepEd ALS Reporting Verification (Form 1 & Form 2)
        // -------------------------------------------------------------
        $form1PdfResponse = $this->actingAs($this->admin)
            ->get("/admin/reports/form-1/pdf?clc_id={$this->clcWestCrame->id}&year=2026");
        $form1PdfResponse->assertOk();
        $this->assertEquals('application/pdf', $form1PdfResponse->headers->get('Content-Type'));

        $form2PdfResponse = $this->actingAs($this->admin)
            ->get("/admin/reports/form-2/{$learner->id}/pdf");
        $form2PdfResponse->assertOk();
        $this->assertEquals('application/pdf', $form2PdfResponse->headers->get('Content-Type'));

        // Scoping Verification: Teacher Alyssa (unassigned) cannot access Form 2 PDF
        $unauthorizedResponse = $this->actingAs($this->teacherAlyssa)
            ->get("/admin/reports/form-2/{$learner->id}/pdf");
        $unauthorizedResponse->assertStatus(403);
    }

    /**
     * E2E Scenario 2: Walk-In Intake by Mobile Teacher at Community Learning Center.
     */
    public function test_end_to_end_walk_in_intake_by_mobile_teacher(): void
    {
        $uniqueSuffix = Str::random(4);
        $firstName = 'Rosa';
        $lastName = 'Batumbakal' . $uniqueSuffix;

        $response = $this->actingAs($this->teacherJuan)->post(route('admin.enrollments.walkIn'), [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birthdate' => '2003-11-12',
            'gender' => 'female',
            'civil_status' => 'single',
            'contact_number' => '09179876543',
            'street_address' => '456 Blumentritt St.',
            'barangay' => 'West Crame',
            'city' => 'San Juan City',
            'highest_education_attained' => 'Elementary Undergraduate',
            'program_track' => Enrollment::TRACK_BLP,
            'clc_id' => $this->clcWestCrame->id,
            'delivery_mode' => 'modular_offline',
            'enrollment_status' => Enrollment::STATUS_UNDER_REVIEW,
        ]);

        $response->assertRedirect();

        $learnerUser = User::whereHas('profile', function ($q) use ($firstName, $lastName) {
            $q->where('first_name', $firstName)->where('last_name', $lastName);
        })->first();

        $this->assertNotNull($learnerUser);
        $this->assertEquals(User::ROLE_LEARNER, $learnerUser->role);

        $enrollment = $learnerUser->latestEnrollment;
        $this->assertNotNull($enrollment);
        $this->assertEquals($this->clcWestCrame->id, $enrollment->clc_id);
        $this->assertEquals(Enrollment::TRACK_BLP, $enrollment->program_track);

        // Teacher Juan can view this walk-in enrollment
        $viewAllowed = $this->actingAs($this->teacherJuan)
            ->get(route('admin.enrollments.show', $enrollment->id));
        $viewAllowed->assertOk();

        // Teacher Alyssa cannot view this walk-in enrollment (403)
        $viewForbidden = $this->actingAs($this->teacherAlyssa)
            ->get(route('admin.enrollments.show', $enrollment->id));
        $viewForbidden->assertStatus(403);
    }

    /**
     * E2E Scenario 3: Telemetry Batch Sync Idempotency (Offline Duplicate Resilience).
     */
    public function test_telemetry_batch_sync_is_idempotent(): void
    {
        $learner = User::create([
            'username' => 'sync_learner_' . Str::random(6),
            'email' => 'sync_' . Str::random(6) . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $payload = [
            'device_identifier' => 'test_device_01',
            'items' => [
                [
                    'module_id' => $this->sampleModule->id,
                    'downloaded_at' => '2026-10-09T08:00:00Z',
                    'last_opened_at' => '2026-10-09T09:00:00Z',
                    'completion_status' => 'completed',
                    'score' => 88.0,
                ],
            ],
        ];

        // Send payload once
        $res1 = $this->actingAs($learner, 'sanctum')->postJson('/api/v1/learner/sync-progress', $payload);
        $res1->assertOk()->assertJsonPath('data.synced_count', 1);

        // Send identical payload again (network retry simulation)
        $res2 = $this->actingAs($learner, 'sanctum')->postJson('/api/v1/learner/sync-progress', $payload);
        $res2->assertOk()->assertJsonPath('data.synced_count', 1);

        // Verify exactly one record exists in module_progress
        $count = ModuleProgress::where('user_id', $learner->id)
            ->where('module_id', $this->sampleModule->id)
            ->count();

        $this->assertEquals(1, $count);
    }

    /**
     * E2E Scenario 4: Public Learning Resources Availability for Android Offline App.
     */
    public function test_public_curriculum_endpoints_are_accessible_for_offline_cache(): void
    {
        // Public strands catalog
        $strandsResponse = $this->getJson('/api/v1/strands');
        $strandsResponse->assertOk()
            ->assertJsonPath('success', true);

        // Public modules catalog
        $modulesResponse = $this->getJson('/api/v1/modules');
        $modulesResponse->assertOk()
            ->assertJsonPath('success', true);
    }
}
