<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardMetricsTest extends TestCase
{
    protected User $admin;
    protected User $teacher1;
    protected User $teacher2;
    protected User $learner;
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

        $this->clc1->update(['assigned_teacher_id' => $this->teacher1->id]);
        $this->clc2->update(['assigned_teacher_id' => $this->teacher2->id]);

        $learnerUser = User::where('role', User::ROLE_LEARNER)->first();
        if (!$learnerUser) {
            $learnerUser = User::create([
                'username' => 'test_learner_auth',
                'email' => 'auth_learner@test.com',
                'password' => bcrypt('password'),
                'role' => User::ROLE_LEARNER,
                'status' => User::STATUS_ACTIVE,
            ]);
        }
        $this->learner = $learnerUser;
    }

    /**
     * Helper to create a test learner with complete profile and enrollment.
     */
    protected function createLearnerEnrollment(
        CommunityLearningCenter $clc,
        ?User $assignedTeacher = null,
        string $status = Enrollment::STATUS_PENDING,
        string $track = Enrollment::TRACK_AE_JUNIOR_HIGH,
        string $calendarYear = '2026',
        ?Carbon $createdAt = null
    ): Enrollment {
        $user = User::create([
            'username' => 'dash_learner_' . Str::random(8),
            'email' => 'dash_' . Str::random(8) . '@example.com',
            'password' => bcrypt('Password123!'),
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => 'Maria',
            'last_name' => 'Santos ' . Str::random(4),
            'birthdate' => '2004-05-10',
            'gender' => 'female',
            'civil_status' => 'single',
            'contact_number' => '09171234567',
            'street_address' => '456 N. Domingo St.',
            'barangay' => $clc->barangay,
            'city' => 'San Juan City',
            'highest_education_attained' => 'Grade 8',
        ]);

        $enrollment = Enrollment::create([
            'tracking_number' => 'TRK-DASH-' . strtoupper(Str::random(6)),
            'user_id' => $user->id,
            'calendar_year' => $calendarYear,
            'program_track' => $track,
            'clc_id' => $clc->id,
            'assigned_teacher_id' => $assignedTeacher?->id,
            'enrollment_status' => $status,
            'delivery_mode' => 'modular_offline',
            'schedule_preference' => 'weekend',
            'created_at' => $createdAt ?: now(),
            'updated_at' => $createdAt ?: now(),
        ]);

        if ($createdAt) {
            \Illuminate\Support\Facades\DB::table('enrollments')->where('id', $enrollment->id)->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $enrollment->refresh();
        }

        return $enrollment;
    }

    // =========================================================================
    // Acceptance Criterion 4: Dashboard data is visible only to authorized staff
    // =========================================================================

    public function test_guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function test_learner_cannot_access_dashboard_and_receives_forbidden_response(): void
    {
        $response = $this->actingAs($this->learner)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_division_admin_can_access_dashboard_with_all_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard')
            ->assertViewHasAll([
                'totalEnrolled',
                'totalPending',
                'totalUnderReview',
                'totalNeedsCorrection',
                'totalRejected',
                'totalCompleted',
                'totalClcs',
                'totalModules',
                'totalTelemetrySyncs',
                'trackCounts',
                'trackPercentages',
                'recentEnrollments',
                'clcsSummary',
                'metricsDefinitions',
            ]);
    }

    public function test_mobile_teacher_can_access_dashboard_with_scoped_metrics(): void
    {
        $response = $this->actingAs($this->teacher1)->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('isTeacher', true);
    }

    // =========================================================================
    // Acceptance Criterion 2: Counts agree with enrollment and CLC records
    // =========================================================================

    public function test_kpi_metrics_accurately_reflect_underlying_enrollment_records_by_status(): void
    {
        // Create known records
        $enrolled = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED);
        $pending = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_PENDING);
        $underReview = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_UNDER_REVIEW);
        $needsCorrection = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_NEEDS_CORRECTION);
        $rejected = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_REJECTED);
        $completed = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_COMPLETED);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Verify that view data counts match active database queries
        $expectedEnrolled = Enrollment::where('enrollment_status', Enrollment::STATUS_ENROLLED)->count();
        $expectedPending = Enrollment::where('enrollment_status', Enrollment::STATUS_PENDING)->count();
        $expectedUnderReview = Enrollment::where('enrollment_status', Enrollment::STATUS_UNDER_REVIEW)->count();
        $expectedNeedsCorrection = Enrollment::where('enrollment_status', Enrollment::STATUS_NEEDS_CORRECTION)->count();
        $expectedRejected = Enrollment::where('enrollment_status', Enrollment::STATUS_REJECTED)->count();
        $expectedCompleted = Enrollment::where('enrollment_status', Enrollment::STATUS_COMPLETED)->count();

        $this->assertEquals($expectedEnrolled, $response->viewData('totalEnrolled'));
        $this->assertEquals($expectedPending, $response->viewData('totalPending'));
        $this->assertEquals($expectedUnderReview, $response->viewData('totalUnderReview'));
        $this->assertEquals($expectedNeedsCorrection, $response->viewData('totalNeedsCorrection'));
        $this->assertEquals($expectedRejected, $response->viewData('totalRejected'));
        $this->assertEquals($expectedCompleted, $response->viewData('totalCompleted'));
    }

    public function test_program_track_distribution_counts_agree_with_records(): void
    {
        $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED, Enrollment::TRACK_BLP);
        $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED, Enrollment::TRACK_AE_ELEMENTARY);
        $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED, Enrollment::TRACK_AE_JUNIOR_HIGH);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $trackCounts = $response->viewData('trackCounts');
        $this->assertEquals(Enrollment::where('program_track', Enrollment::TRACK_BLP)->count(), $trackCounts['BLP']);
        $this->assertEquals(Enrollment::where('program_track', Enrollment::TRACK_AE_ELEMENTARY)->count(), $trackCounts['Elementary']);
        $this->assertEquals(Enrollment::where('program_track', Enrollment::TRACK_AE_JUNIOR_HIGH)->count(), $trackCounts['Junior_High']);

        $trackPercentages = $response->viewData('trackPercentages');
        $totalTracks = array_sum($trackCounts);
        if ($totalTracks > 0) {
            $expectedBlpPct = round(($trackCounts['BLP'] / $totalTracks) * 100, 1);
            $this->assertEquals($expectedBlpPct, $trackPercentages['BLP']);
        }
    }

    public function test_clc_active_learner_counts_agree_with_enrolled_records(): void
    {
        // Enroll learner in CLC1
        $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $clcsSummary = $response->viewData('clcsSummary');
        $clc1Summary = $clcsSummary->firstWhere('id', $this->clc1->id);

        if ($clc1Summary) {
            $expectedClc1Enrolled = Enrollment::where('clc_id', $this->clc1->id)
                ->where('enrollment_status', Enrollment::STATUS_ENROLLED)
                ->count();
            $this->assertEquals($expectedClc1Enrolled, $clc1Summary->active_learners);
        }
    }

    public function test_mobile_teacher_only_counts_assigned_learners_and_clcs(): void
    {
        // Create 1 learner assigned to teacher1 (at clc1)
        $t1Enrollment = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED);

        // Create 1 learner assigned to teacher2 (at clc2)
        $t2Enrollment = $this->createLearnerEnrollment($this->clc2, $this->teacher2, Enrollment::STATUS_ENROLLED);

        $response = $this->actingAs($this->teacher1)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $expectedTeacher1Enrolled = Enrollment::where(function ($q) {
            $q->where('assigned_teacher_id', $this->teacher1->id)
              ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $this->teacher1->id));
        })->where('enrollment_status', Enrollment::STATUS_ENROLLED)->count();

        $this->assertEquals($expectedTeacher1Enrolled, $response->viewData('totalEnrolled'));

        // Recent enrollments list should not include teacher 2's learner
        $recentEnrollments = $response->viewData('recentEnrollments');
        $this->assertFalse($recentEnrollments->contains('id', $t2Enrollment->id));
    }

    // =========================================================================
    // Acceptance Criterion 3: Empty, large, and filtered result sets render clearly
    // =========================================================================

    public function test_dashboard_filters_by_date_range_preset_and_custom_dates(): void
    {
        // Create an enrollment today with newest timestamp
        $todayRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED, Enrollment::TRACK_BLP, '2026', Carbon::now()->addSeconds(30));

        // Create an old enrollment from 60 days ago
        $oldRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED, Enrollment::TRACK_BLP, '2026', Carbon::now()->subDays(60));

        // 1. Filter by preset 'today' with clc_id scope
        $todayResponse = $this->actingAs($this->admin)->get(route('admin.dashboard', ['date_range' => 'today', 'clc_id' => $this->clc1->id]));
        $todayResponse->assertStatus(200);
        $recentToday = $todayResponse->viewData('recentEnrollments');
        $this->assertTrue($recentToday->contains('id', $todayRecord->id));
        $this->assertFalse($recentToday->contains('id', $oldRecord->id));

        // 2. Filter by preset '7d'
        $weekResponse = $this->actingAs($this->admin)->get(route('admin.dashboard', ['date_range' => '7d', 'clc_id' => $this->clc1->id]));
        $weekResponse->assertStatus(200);
        $this->assertFalse($weekResponse->viewData('recentEnrollments')->contains('id', $oldRecord->id));

        // 3. Filter by custom date range
        $customResponse = $this->actingAs($this->admin)->get(route('admin.dashboard', [
            'date_range' => 'custom',
            'date_from' => Carbon::now()->subDays(70)->toDateString(),
            'date_to' => Carbon::now()->subDays(50)->toDateString(),
            'clc_id' => $this->clc1->id,
        ]));
        $customResponse->assertStatus(200);
        $this->assertTrue($customResponse->viewData('recentEnrollments')->contains('id', $oldRecord->id));
        $this->assertFalse($customResponse->viewData('recentEnrollments')->contains('id', $todayRecord->id));
    }

    public function test_dashboard_filters_by_community_learning_center(): void
    {
        $clc1Record = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_PENDING, Enrollment::TRACK_AE_JUNIOR_HIGH, '2026', now()->addSeconds(5));
        $clc2Record = $this->createLearnerEnrollment($this->clc2, $this->teacher2, Enrollment::STATUS_PENDING, Enrollment::TRACK_AE_JUNIOR_HIGH, '2026', now()->addSeconds(5));

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['clc_id' => $this->clc1->id]));
        $response->assertStatus(200);

        $recent = $response->viewData('recentEnrollments');
        $this->assertTrue($recent->contains('id', $clc1Record->id));
        $this->assertFalse($recent->contains('id', $clc2Record->id));
        $this->assertTrue($recent->every(fn ($e) => $e->clc_id == $this->clc1->id));

        // CLC Summary should only show CLC1
        $clcsSummary = $response->viewData('clcsSummary');
        $this->assertEquals(1, $clcsSummary->count());
        $this->assertEquals($this->clc1->id, $clcsSummary->first()->id);
    }

    public function test_dashboard_filters_by_program_track(): void
    {
        $blpRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_PENDING, Enrollment::TRACK_BLP);
        $jhsRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_PENDING, Enrollment::TRACK_AE_JUNIOR_HIGH);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['track' => Enrollment::TRACK_BLP]));
        $response->assertStatus(200);

        $recent = $response->viewData('recentEnrollments');
        $this->assertTrue($recent->contains('id', $blpRecord->id));
        $this->assertFalse($recent->contains('id', $jhsRecord->id));
    }

    public function test_dashboard_filters_by_enrollment_status(): void
    {
        $rejectedRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_REJECTED);
        $enrolledRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['status' => Enrollment::STATUS_REJECTED]));
        $response->assertStatus(200);

        $recent = $response->viewData('recentEnrollments');
        $this->assertTrue($recent->contains('id', $rejectedRecord->id));
        $this->assertFalse($recent->contains('id', $enrolledRecord->id));
    }

    public function test_dashboard_handles_empty_dataset_gracefully_without_errors(): void
    {
        // Query with an impossible year or date to trigger empty result set
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', [
            'calendar_year' => '1990',
            'clc_id' => 999999,
        ]));

        $response->assertStatus(200);
        $this->assertEquals(0, $response->viewData('totalIntakes'));
        $this->assertEquals(0, $response->viewData('totalEnrolled'));
        $this->assertEquals(0, $response->viewData('totalPending'));
        $this->assertEquals(0, $response->viewData('totalTrackRecords'));

        // Assert empty state messages render without blade or PHP division by zero errors
        $response->assertSee('No enrolment records in this selection');
        $response->assertSee('No enrollment applications match your filter criteria');
    }

    public function test_soft_deleted_enrollments_are_excluded_from_metric_calculations(): void
    {
        $deletedRecord = $this->createLearnerEnrollment($this->clc1, $this->teacher1, Enrollment::STATUS_ENROLLED);
        $deletedRecord->delete(); // Soft delete

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $recent = $response->viewData('recentEnrollments');
        $this->assertFalse($recent->contains('id', $deletedRecord->id));

        $expectedEnrolled = Enrollment::where('enrollment_status', Enrollment::STATUS_ENROLLED)->count();
        $this->assertEquals($expectedEnrolled, $response->viewData('totalEnrolled'));
    }

    // =========================================================================
    // Acceptance Criterion 1: Every displayed metric has a documented definition and source
    // =========================================================================

    public function test_metric_definitions_modal_and_dictionary_data_are_present_in_view(): void
    {
        $definitions = DashboardController::getMetricsDefinitions();

        $this->assertArrayHasKey('total_enrolled', $definitions);
        $this->assertArrayHasKey('total_pending', $definitions);
        $this->assertArrayHasKey('total_under_review', $definitions);
        $this->assertArrayHasKey('total_needs_correction', $definitions);
        $this->assertArrayHasKey('total_rejected', $definitions);
        $this->assertArrayHasKey('total_completed', $definitions);
        $this->assertArrayHasKey('total_intakes', $definitions);
        $this->assertArrayHasKey('active_clcs', $definitions);
        $this->assertArrayHasKey('published_modules', $definitions);
        $this->assertArrayHasKey('offline_telemetry', $definitions);

        // Check that each definition specifies a name, description, source, and policy ref
        foreach ($definitions as $metric) {
            $this->assertNotEmpty($metric['name']);
            $this->assertNotEmpty($metric['description']);
            $this->assertNotEmpty($metric['source']);
            $this->assertNotEmpty($metric['policy_ref']);
        }

        // Test that the view renders the dictionary button and definition elements
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertSee('Metric Definitions')
            ->assertSee('Dashboard Metrics Dictionary')
            ->assertSee('Officially Enrolled')
            ->assertSee('Pending Initial Review')
            ->assertSee('Needs Correction')
            ->assertSee('Ineligible / Rejected')
            ->assertSee('DepEd Order No. 27, s. 2017');
    }
}
