<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class DepEdReportsValidationTest extends TestCase
{
    protected User $admin;
    protected User $teacherJuan;
    protected User $teacherAlyssa;
    protected User $learnerUser;
    protected CommunityLearningCenter $clcWestCrame;
    protected CommunityLearningCenter $clcBatis;

    protected function setUp(): void
    {
        @ini_set('memory_limit', '512M');
        parent::setUp();

        $this->admin = User::where('username', 'admin_als')->firstOrFail();
        $this->teacherJuan = User::where('username', 'teacher_juan')->firstOrFail();
        $this->teacherAlyssa = User::where('username', 'teacher_alyssa')->firstOrFail();

        $this->clcWestCrame = CommunityLearningCenter::where('clc_code', 'CLC-SJ-WCR-01')->firstOrFail();
        $this->clcBatis = CommunityLearningCenter::where('clc_code', 'CLC-SJ-BAT-01')->firstOrFail();

        // Ensure teacher assignments for scoping
        $this->clcWestCrame->update(['assigned_teacher_id' => $this->teacherJuan->id]);
        $this->clcBatis->update(['assigned_teacher_id' => $this->teacherAlyssa->id]);

        $learner = User::where('role', User::ROLE_LEARNER)->first();
        if (!$learner) {
            $learner = User::create([
                'username' => 'test_report_learner',
                'email' => 'report_learner@test.com',
                'password' => bcrypt('Password123!'),
                'role' => User::ROLE_LEARNER,
                'status' => User::STATUS_ACTIVE,
            ]);
        }
        $this->learnerUser = $learner;
    }

    /**
     * Helper to create a learner with profile, enrollment, and optional documents.
     */
    protected function createLearnerWithEnrollment(
        CommunityLearningCenter $clc,
        ?User $assignedTeacher,
        string $gender = 'male',
        string $track = Enrollment::TRACK_AE_JUNIOR_HIGH,
        string $status = Enrollment::STATUS_ENROLLED,
        string $calendarYear = '2026',
        bool $withDocuments = false
    ): User {
        $user = User::create([
            'username' => 'lrn_' . Str::random(8),
            'lrn' => '12' . rand(1000000000, 9999999999),
            'email' => 'lrn_' . Str::random(8) . '@example.com',
            'password' => bcrypt('Password123!'),
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $gender === 'male' ? 'Juan' : 'Maria',
            'last_name' => 'Dela Cruz ' . Str::random(4),
            'birthdate' => '2005-03-15',
            'gender' => $gender,
            'civil_status' => 'single',
            'contact_number' => '09181234567',
            'street_address' => '123 Pinaglabanan St.',
            'barangay' => $clc->barangay,
            'city' => 'San Juan City',
            'province' => 'Metro Manila',
            'highest_education_attained' => 'Grade 7 Completer',
        ]);

        $enrollment = Enrollment::create([
            'tracking_number' => 'TRK-REP-' . strtoupper(Str::random(6)),
            'user_id' => $user->id,
            'calendar_year' => $calendarYear,
            'program_track' => $track,
            'clc_id' => $clc->id,
            'assigned_teacher_id' => $assignedTeacher?->id,
            'enrollment_status' => $status,
            'delivery_mode' => 'modular_offline',
            'schedule_preference' => 'weekend',
        ]);

        if ($withDocuments) {
            LearnerDocument::create([
                'user_id' => $user->id,
                'enrollment_id' => $enrollment->id,
                'document_type' => LearnerDocument::TYPE_PSA_BIRTH_CERT,
                'file_path' => 'documents/test_psa.pdf',
                'file_name' => 'test_psa.pdf',
                'file_size' => 102400,
                'mime_type' => 'application/pdf',
                'verification_status' => LearnerDocument::STATUS_VERIFIED,
            ]);
            LearnerDocument::create([
                'user_id' => $user->id,
                'enrollment_id' => $enrollment->id,
                'document_type' => LearnerDocument::TYPE_BARANGAY_CLEARANCE,
                'file_path' => 'documents/test_brgy.pdf',
                'file_name' => 'test_brgy.pdf',
                'file_size' => 81920,
                'mime_type' => 'application/pdf',
                'verification_status' => LearnerDocument::STATUS_VERIFIED,
            ]);
        }

        return $user;
    }

    // ==========================================
    // CRITERION 4: Authorization & Security
    // ==========================================

    public function test_guests_cannot_access_form_1_or_form_2_web_or_pdf(): void
    {
        $this->get('/admin/reports/form-1')->assertRedirect('/login');
        $this->get('/admin/reports/form-1/pdf')->assertRedirect('/login');
        $this->get('/admin/reports/form-2')->assertRedirect('/login');
        $this->get("/admin/reports/form-2/{$this->learnerUser->id}/pdf")->assertRedirect('/login');
    }

    public function test_learners_cannot_access_admin_reports(): void
    {
        $response = $this->actingAs($this->learnerUser)->get('/admin/reports/form-1');
        $this->assertTrue(in_array($response->status(), [403, 302]));

        $responsePdf = $this->actingAs($this->learnerUser)->get('/admin/reports/form-1/pdf');
        $this->assertTrue(in_array($responsePdf->status(), [403, 302]));

        $response2 = $this->actingAs($this->learnerUser)->get('/admin/reports/form-2');
        $this->assertTrue(in_array($response2->status(), [403, 302]));
    }

    public function test_mobile_teacher_can_access_form_1_for_assigned_clc_but_forbidden_for_other_clc(): void
    {
        // Teacher Juan is assigned to West Crame (clcWestCrame), not Batis (clcBatis)
        $responseAllowed = $this->actingAs($this->teacherJuan)
            ->get("/admin/reports/form-1?clc_id={$this->clcWestCrame->id}");
        $responseAllowed->assertOk();

        // Accessing Batis CLC must return 403 Forbidden
        $responseForbidden = $this->actingAs($this->teacherJuan)
            ->get("/admin/reports/form-1?clc_id={$this->clcBatis->id}");
        $responseForbidden->assertStatus(403);

        // PDF export should also forbid accessing unassigned CLC
        $responsePdfForbidden = $this->actingAs($this->teacherJuan)
            ->get("/admin/reports/form-1/pdf?clc_id={$this->clcBatis->id}");
        $responsePdfForbidden->assertStatus(403);
    }

    public function test_mobile_teacher_can_access_form_2_for_assigned_learner_but_forbidden_for_other_learner(): void
    {
        $learnerJuan = $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan);
        $learnerAlyssa = $this->createLearnerWithEnrollment($this->clcBatis, $this->teacherAlyssa);

        // Teacher Juan can view learnerJuan's Form 2
        $responseAllowed = $this->actingAs($this->teacherJuan)
            ->get("/admin/reports/form-2?learner_id={$learnerJuan->id}");
        $responseAllowed->assertOk();

        // Teacher Juan cannot view learnerAlyssa's Form 2 (HTTP 403)
        $responseForbidden = $this->actingAs($this->teacherJuan)
            ->get("/admin/reports/form-2?learner_id={$learnerAlyssa->id}");
        $responseForbidden->assertStatus(403);

        // PDF stream forbidden check
        $responsePdfForbidden = $this->actingAs($this->teacherJuan)
            ->get("/admin/reports/form-2/{$learnerAlyssa->id}/pdf");
        $responsePdfForbidden->assertStatus(403);
    }

    // ==========================================
    // CRITERION 1 & 2: Filters, Periods & Statistical Summary
    // ==========================================

    public function test_form_1_calculates_accurate_deped_summary_breakdown_by_sex_and_track(): void
    {
        // Create controlled cohort for CY 2026 at West Crame
        // 2 Male BLP Enrolled
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED, '2026');
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED, '2026');

        // 1 Female BLP Enrolled
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'female', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED, '2026');

        // 1 Male A&E Elementary Enrolled
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_AE_ELEMENTARY, Enrollment::STATUS_ENROLLED, '2026');

        // 2 Female A&E Junior High Enrolled
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'female', Enrollment::TRACK_AE_JUNIOR_HIGH, Enrollment::STATUS_ENROLLED, '2026');
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'female', Enrollment::TRACK_AE_JUNIOR_HIGH, Enrollment::STATUS_ENROLLED, '2026');

        $response = $this->actingAs($this->admin)
            ->get("/admin/reports/form-1?clc_id={$this->clcWestCrame->id}&year=2026&status=enrolled");

        $response->assertOk();
        $response->assertViewHas('summary');
        $summary = $response->viewData('summary');

        $this->assertGreaterThanOrEqual(3, $summary['male']);
        $this->assertGreaterThanOrEqual(3, $summary['female']);
        $this->assertGreaterThanOrEqual(3, $summary['tracks']['BLP']['total']);
        $this->assertGreaterThanOrEqual(1, $summary['tracks']['A&E_Elementary']['total']);
        $this->assertGreaterThanOrEqual(2, $summary['tracks']['A&E_Junior_High']['total']);
    }

    public function test_form_1_filters_by_program_track_and_reporting_year(): void
    {
        $uniqueYear = '2025';
        $learnerBlp = $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'female', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED, $uniqueYear);
        $learnerJhs = $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_AE_JUNIOR_HIGH, Enrollment::STATUS_ENROLLED, $uniqueYear);

        // Filter track = BLP and year = 2025
        $responseBlp = $this->actingAs($this->admin)
            ->get("/admin/reports/form-1?clc_id={$this->clcWestCrame->id}&year={$uniqueYear}&track=" . Enrollment::TRACK_BLP);
        
        $responseBlp->assertOk();
        $enrollments = $responseBlp->viewData('enrollments');

        $this->assertTrue($enrollments->contains('user_id', $learnerBlp->id));
        $this->assertFalse($enrollments->contains('user_id', $learnerJhs->id));
    }

    public function test_form_1_filters_by_enrollment_status(): void
    {
        $learnerEnrolled = $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED);
        $learnerReview = $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'female', Enrollment::TRACK_BLP, Enrollment::STATUS_UNDER_REVIEW);

        $response = $this->actingAs($this->admin)
            ->get("/admin/reports/form-1?clc_id={$this->clcWestCrame->id}&status=" . Enrollment::STATUS_UNDER_REVIEW);

        $response->assertOk();
        $enrollments = $response->viewData('enrollments');

        $this->assertTrue($enrollments->contains('user_id', $learnerReview->id));
        $this->assertFalse($enrollments->contains('user_id', $learnerEnrolled->id));
    }

    // ==========================================
    // CRITERION 3: PDF Generation & Readability
    // ==========================================

    public function test_admin_can_generate_readable_form_1_pdf_stream(): void
    {
        $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED);

        $response = $this->actingAs($this->admin)
            ->get("/admin/reports/form-1/pdf?clc_id={$this->clcWestCrame->id}&year=2026");

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_admin_can_generate_readable_form_2_pdf_stream_with_documents(): void
    {
        $learner = $this->createLearnerWithEnrollment(
            $this->clcWestCrame,
            $this->teacherJuan,
            'female',
            Enrollment::TRACK_AE_JUNIOR_HIGH,
            Enrollment::STATUS_ENROLLED,
            '2026',
            true
        );

        $response = $this->actingAs($this->admin)
            ->get("/admin/reports/form-2/{$learner->id}/pdf");

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    // ==========================================
    // API Endpoints: JSON and PDF formats
    // ==========================================

    public function test_api_form_1_returns_json_and_pdf_formats_with_scoping(): void
    {
        // Admin JSON format
        $responseJson = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/admin/reports/form-1?format=json&clc_id={$this->clcWestCrame->id}&year=2026");

        $responseJson->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'calendar_year',
                'total_learners',
                'summary' => [
                    'male',
                    'female',
                    'total',
                    'tracks',
                    'status_breakdown',
                ],
            ]);

        // Teacher Alyssa cannot query West Crame in API (403)
        $responseAlyssaForbidden = $this->actingAs($this->teacherAlyssa, 'sanctum')
            ->getJson("/api/v1/admin/reports/form-1?clc_id={$this->clcWestCrame->id}");
        $responseAlyssaForbidden->assertStatus(403);

        // Admin PDF format stream
        $responsePdf = $this->actingAs($this->admin, 'sanctum')
            ->get("/api/v1/admin/reports/form-1?clc_id={$this->clcWestCrame->id}&year=2026");
        $responsePdf->assertOk();
        $this->assertEquals('application/pdf', $responsePdf->headers->get('Content-Type'));
    }

    public function test_api_form_2_returns_json_and_pdf_with_scoping(): void
    {
        $learner = $this->createLearnerWithEnrollment($this->clcWestCrame, $this->teacherJuan, 'male', Enrollment::TRACK_BLP, Enrollment::STATUS_ENROLLED, '2026', true);

        // Admin JSON format
        $responseJson = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/admin/reports/form-2/{$learner->id}?format=json");

        $responseJson->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('learner.id', $learner->id)
            ->assertJsonStructure([
                'success',
                'learner',
                'enrollment',
                'profile',
                'documents',
            ]);

        // Teacher Juan (assigned) can export PDF
        $responseTeacherPdf = $this->actingAs($this->teacherJuan, 'sanctum')
            ->get("/api/v1/admin/reports/form-2/{$learner->id}");
        $responseTeacherPdf->assertOk();
        $this->assertEquals('application/pdf', $responseTeacherPdf->headers->get('Content-Type'));

        // Teacher Alyssa (unassigned) receives 403
        $responseTeacherAlyssa = $this->actingAs($this->teacherAlyssa, 'sanctum')
            ->getJson("/api/v1/admin/reports/form-2/{$learner->id}?format=json");
        $responseTeacherAlyssa->assertStatus(403);
    }
}
