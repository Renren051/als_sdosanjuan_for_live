<?php

namespace Tests\Feature;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Tests\TestCase;

class EnrollmentQueueAndTransitionsTest extends TestCase
{
    protected User $admin;
    protected User $teacher1;
    protected User $teacher2;
    protected CommunityLearningCenter $clc1;
    protected CommunityLearningCenter $clc2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('username', 'admin_als')->firstOrFail();
        $this->teacher1 = User::where('username', 'teacher_juan')->firstOrFail();
        $this->teacher2 = User::where('username', 'teacher_alyssa')->firstOrFail();

        $this->clc1 = CommunityLearningCenter::where('clc_code', 'CLC-SJ-WCR-01')->firstOrFail();
        $this->clc2 = CommunityLearningCenter::where('clc_code', 'CLC-SJ-BAT-01')->firstOrFail();

        // Ensure CLC assigned teachers match for testing isolation
        $this->clc1->update(['assigned_teacher_id' => $this->teacher1->id]);
        $this->clc2->update(['assigned_teacher_id' => $this->teacher2->id]);
    }

    /**
     * Helper to create a test learner with complete profile and enrollment.
     */
    protected function createLearnerEnrollment(
        CommunityLearningCenter $clc,
        ?User $assignedTeacher = null,
        string $status = Enrollment::STATUS_PENDING
    ): Enrollment {
        $user = User::create([
            'username' => 'test_learner_' . Str::random(8),
            'email' => 'test_' . Str::random(8) . '@example.com',
            'password' => bcrypt('Password123!'),
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz ' . Str::random(4),
            'birthdate' => '2005-01-01',
            'gender' => 'male',
            'civil_status' => 'single',
            'contact_number' => '09123456789',
            'street_address' => '123 F. Manalo St.',
            'barangay' => 'West Crame',
            'city' => 'San Juan City',
            'highest_education_attained' => 'Grade 6 Graduate',
        ]);

        return Enrollment::create([
            'tracking_number' => 'TRK-2026-' . strtoupper(Str::random(6)),
            'user_id' => $user->id,
            'calendar_year' => '2026',
            'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
            'clc_id' => $clc->id,
            'assigned_teacher_id' => $assignedTeacher?->id,
            'enrollment_status' => $status,
            'delivery_mode' => 'modular_offline',
            'schedule_preference' => 'weekend',
        ]);
    }

    // =========================================================================
    // Acceptance Criterion 1: Authorized staff only can review or change records
    // =========================================================================

    public function test_division_admin_can_view_and_review_any_enrollment_record(): void
    {
        $enrollment = $this->createLearnerEnrollment($this->clc2, $this->teacher2);

        $response = $this->actingAs($this->admin)->get(route('admin.enrollments.show', $enrollment->id));

        $response->assertStatus(200)
            ->assertSee($enrollment->tracking_number)
            ->assertSee($enrollment->user->profile->first_name);
    }

    public function test_mobile_teacher_can_view_enrollments_within_their_assigned_scope(): void
    {
        $enrollmentTeacher1 = $this->createLearnerEnrollment($this->clc1, $this->teacher1);

        $response = $this->actingAs($this->teacher1)->get(route('admin.enrollments.show', $enrollmentTeacher1->id));

        $response->assertStatus(200)
            ->assertSee($enrollmentTeacher1->tracking_number);
    }

    public function test_mobile_teacher_cannot_access_enrollment_outside_their_scope(): void
    {
        // Enrollment belongs to CLC2 and teacher2
        $enrollmentTeacher2 = $this->createLearnerEnrollment($this->clc2, $this->teacher2);

        // Teacher 1 attempts to view teacher 2's enrollment
        $response = $this->actingAs($this->teacher1)->get(route('admin.enrollments.show', $enrollmentTeacher2->id));

        $response->assertStatus(403);
    }

    public function test_mobile_teacher_cannot_update_status_of_enrollment_outside_their_scope(): void
    {
        $enrollmentTeacher2 = $this->createLearnerEnrollment($this->clc2, $this->teacher2, Enrollment::STATUS_PENDING);

        $response = $this->actingAs($this->teacher1)->post(
            route('admin.enrollments.updateStatus', $enrollmentTeacher2->id),
            [
                'status' => Enrollment::STATUS_UNDER_REVIEW,
                'remarks' => 'Unauthorized update attempt',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_learner_cannot_access_staff_enrollment_endpoints(): void
    {
        $enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1);
        $learner = $enrollment->user;

        $response = $this->actingAs($learner)->get(route('admin.enrollments.index'));
        $response->assertStatus(403);

        $response2 = $this->actingAs($learner)->get(route('admin.enrollments.show', $enrollment->id));
        $response2->assertStatus(403);
    }

    // =========================================================================
    // Acceptance Criterion 2: Valid status transitions are enforced & recorded
    // =========================================================================

    public function test_valid_status_transitions_are_permitted(): void
    {
        $enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_PENDING);

        // 1. Pending -> Under Review
        $res1 = $this->actingAs($this->admin)->post(
            route('admin.enrollments.updateStatus', $enrollment->id),
            [
                'enrollment_status' => Enrollment::STATUS_UNDER_REVIEW,
                'remarks' => 'Reviewing documents and eligibility.',
            ]
        );
        $res1->assertSessionHasNoErrors();
        $this->assertEquals(Enrollment::STATUS_UNDER_REVIEW, $enrollment->fresh()->enrollment_status);

        // 2. Under Review -> Enrolled
        $res2 = $this->actingAs($this->admin)->post(
            route('admin.enrollments.updateStatus', $enrollment->id),
            [
                'enrollment_status' => Enrollment::STATUS_ENROLLED,
                'remarks' => 'All requirements verified. Officially enrolled.',
            ]
        );
        $res2->assertSessionHasNoErrors();
        $this->assertEquals(Enrollment::STATUS_ENROLLED, $enrollment->fresh()->enrollment_status);
        $this->assertNotNull($enrollment->fresh()->enrolled_at);
    }

    public function test_invalid_status_transitions_are_rejected(): void
    {
        // Enrolled cannot transition directly back to Pending
        $enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED);

        $response = $this->actingAs($this->admin)->post(
            route('admin.enrollments.updateStatus', $enrollment->id),
            [
                'enrollment_status' => Enrollment::STATUS_PENDING,
                'remarks' => 'Attempting invalid jump back to pending',
            ]
        );

        $response->assertSessionHasErrors('enrollment_status');
        $this->assertEquals(Enrollment::STATUS_ENROLLED, $enrollment->fresh()->enrollment_status);
    }

    // =========================================================================
    // Acceptance Criterion 3: Rejections and corrections require clear remarks
    // =========================================================================

    public function test_status_change_to_needs_correction_requires_remarks(): void
    {
        $enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_UNDER_REVIEW);

        $response = $this->actingAs($this->admin)->post(
            route('admin.enrollments.updateStatus', $enrollment->id),
            [
                'enrollment_status' => Enrollment::STATUS_NEEDS_CORRECTION,
                'remarks' => '', // Empty remarks
            ]
        );

        $response->assertSessionHasErrors('remarks');
        $this->assertEquals(Enrollment::STATUS_UNDER_REVIEW, $enrollment->fresh()->enrollment_status);
    }

    public function test_status_change_to_rejected_requires_remarks(): void
    {
        $enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_UNDER_REVIEW);

        $response = $this->actingAs($this->admin)->post(
            route('admin.enrollments.updateStatus', $enrollment->id),
            [
                'enrollment_status' => Enrollment::STATUS_REJECTED,
                'remarks' => '', // Empty remarks
            ]
        );

        $response->assertSessionHasErrors('remarks');
        $this->assertEquals(Enrollment::STATUS_UNDER_REVIEW, $enrollment->fresh()->enrollment_status);
    }

    public function test_rejection_remarks_are_visible_on_learner_dashboard_and_enrollment_views(): void
    {
        $enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_UNDER_REVIEW);
        $learner = $enrollment->user;
        $rejectionReason = 'Incomplete credentials: PSA birth certificate was unreadable and age criteria not met.';

        // Perform rejection with remarks
        $this->actingAs($this->admin)->post(
            route('admin.enrollments.updateStatus', $enrollment->id),
            [
                'enrollment_status' => Enrollment::STATUS_REJECTED,
                'remarks' => $rejectionReason,
            ]
        );

        $this->assertEquals(Enrollment::STATUS_REJECTED, $enrollment->fresh()->enrollment_status);

        // Check Learner Dashboard
        $dashResponse = $this->actingAs($learner)->get(route('learner.dashboard'));
        $dashResponse->assertStatus(200)
            ->assertSee('Application Disapproved / Rejected')
            ->assertSee($rejectionReason);

        // Check Learner Enrollment Journey
        $journeyResponse = $this->actingAs($learner)->get(route('learner.enrollment'));
        $journeyResponse->assertStatus(200)
            ->assertSee('Application Rejected')
            ->assertSee($rejectionReason);
    }

    // =========================================================================
    // Acceptance Criterion 4: Walk-in records follow Form 2 intake rules
    // =========================================================================

    public function test_walk_in_intake_validates_required_deped_form_2_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(
            route('admin.enrollments.walkIn'),
            [] // Empty payload
        );

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'birthdate',
            'gender',
            'civil_status',
            'highest_education_attained',
            'program_track',
            'clc_id',
            'enrollment_status',
            'contact_number',
            'street_address',
            'barangay',
        ]);
    }

    public function test_walk_in_intake_creates_traceable_record_with_demographics_and_tracking_number(): void
    {
        $walkInData = [
            'first_name' => 'Roberto',
            'middle_name' => 'Mendoza',
            'last_name' => 'Santos',
            'name_extension' => 'Jr.',
            'birthdate' => '2003-08-19',
            'gender' => 'male',
            'civil_status' => 'single',
            'contact_number' => '09191234567',
            'facebook_name' => 'Roberto Santos ALS',
            'street_address' => 'Unit 102 Pinaglabanan St.',
            'barangay' => 'Pedro Cruz',
            'city' => 'San Juan City',
            'highest_education_attained' => 'Grade 10 Completer (Junior High School)',
            'mother_tongue' => 'Tagalog / Filipino',
            'is_4ps_beneficiary' => '1',
            'is_ip' => '0',
            'is_pwd' => '0',
            'father_name' => 'Roberto Santos Sr.',
            'mother_name' => 'Alicia Mendoza Santos',
            'guardian_name' => 'Roberto Santos Sr.',
            'guardian_relationship' => 'Father',
            'emergency_contact_name' => 'Alicia Mendoza Santos',
            'emergency_contact_number' => '09199876543',
            'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
            'clc_id' => $this->clc1->id,
            'enrollment_status' => Enrollment::STATUS_ENROLLED,
            'delivery_mode' => 'blended',
            'schedule_preference' => 'weekend',
            'remarks' => 'Walk-in registration at CLC West Crame with physical Form 2.',
        ];

        $response = $this->actingAs($this->teacher1)->post(
            route('admin.enrollments.walkIn'),
            $walkInData
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify User was created
        $user = User::whereHas('profile', function ($q) {
            $q->where('first_name', 'Roberto')->where('last_name', 'Santos');
        })->first();

        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_LEARNER, $user->role);
        $this->assertEquals('single', $user->profile->civil_status);
        $this->assertTrue((bool) $user->profile->is_4ps_beneficiary);

        // Verify Enrollment
        $enrollment = Enrollment::where('user_id', $user->id)->first();
        $this->assertNotNull($enrollment);
        $this->assertStringStartsWith('ALS-2026-', $enrollment->tracking_number);
        $this->assertEquals(Enrollment::STATUS_ENROLLED, $enrollment->enrollment_status);
        $this->assertEquals($this->clc1->id, $enrollment->clc_id);
        $this->assertEquals($this->teacher1->id, $enrollment->assigned_teacher_id);
        $this->assertEquals('blended', $enrollment->delivery_mode);
        $this->assertNotNull($enrollment->enrolled_at);
    }

    // =========================================================================
    // Acceptance Criterion 5: Queue counts and filters match underlying records
    // =========================================================================

    public function test_queue_counts_and_filters_match_underlying_records(): void
    {
        // Create controlled records for CLC1
        $pending = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_PENDING);
        $underReview = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_UNDER_REVIEW);
        $rejected = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_REJECTED);

        // 1. Division Admin views queue
        $adminIndex = $this->actingAs($this->admin)->get(route('admin.enrollments.index'));
        $adminIndex->assertStatus(200);

        // View model data passed to view
        $statusCounts = $adminIndex->viewData('statusCounts');
        $this->assertIsArray($statusCounts);
        $this->assertGreaterThanOrEqual(1, $statusCounts['pending']);
        $this->assertGreaterThanOrEqual(1, $statusCounts['under_review']);
        $this->assertGreaterThanOrEqual(1, $statusCounts['rejected']);

        // 2. Filter by status=rejected shows only rejected enrollments
        $filterResponse = $this->actingAs($this->admin)->get(route('admin.enrollments.index', ['status' => 'rejected']));
        $filterResponse->assertStatus(200)
            ->assertSee($rejected->tracking_number)
            ->assertDontSee($pending->tracking_number);

        // 3. Mobile teacher queue view only includes their assigned center/students
        $teacherIndex = $this->actingAs($this->teacher1)->get(route('admin.enrollments.index'));
        $teacherIndex->assertStatus(200);
        $teacherCounts = $teacherIndex->viewData('statusCounts');

        // Teacher counts must match the scoped query
        $actualTeacherPending = Enrollment::where(function ($q) {
            $q->where('assigned_teacher_id', $this->teacher1->id)
              ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $this->teacher1->id));
        })->where('enrollment_status', Enrollment::STATUS_PENDING)->count();

        $this->assertEquals($actualTeacherPending, $teacherCounts['pending']);
    }
}
