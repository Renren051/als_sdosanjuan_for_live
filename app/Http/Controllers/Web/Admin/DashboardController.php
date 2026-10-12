<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleGameAttempt;
use App\Models\ModuleProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Executive / Mobile Teacher Dashboard with validated metrics and operational summaries.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        // 1. Base Enrollment Query Scope
        $enrollmentQuery = Enrollment::query();

        // Scope mobile teachers strictly to their assigned learners OR learners in their assigned CLCs
        if ($isTeacher) {
            $enrollmentQuery->where(function ($q) use ($currentUser) {
                $q->where('assigned_teacher_id', $currentUser->id)
                  ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
            });
        }

        // 2. Filter Inputs & Date Range Processing
        $dateRange = $request->input('date_range', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $calendarYear = $request->input('calendar_year');
        $clcId = $request->input('clc_id');
        $track = $request->input('track');
        $status = $request->input('status');

        // Preset Date Ranges
        if ($dateRange === 'today') {
            $dateFrom = Carbon::today()->toDateString();
            $dateTo = Carbon::today()->toDateString();
        } elseif ($dateRange === '7d') {
            $dateFrom = Carbon::now()->subDays(6)->toDateString();
            $dateTo = Carbon::now()->toDateString();
        } elseif ($dateRange === '30d') {
            $dateFrom = Carbon::now()->subDays(29)->toDateString();
            $dateTo = Carbon::now()->toDateString();
        } elseif ($dateRange === 'this_month') {
            $dateFrom = Carbon::now()->startOfMonth()->toDateString();
            $dateTo = Carbon::now()->endOfMonth()->toDateString();
        }

        // Apply filters to enrollment query
        if (!empty($calendarYear)) {
            $enrollmentQuery->where('calendar_year', $calendarYear);
        }

        if (!empty($dateFrom)) {
            $enrollmentQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $enrollmentQuery->whereDate('created_at', '<=', $dateTo);
        }

        if (!empty($clcId)) {
            $enrollmentQuery->where('clc_id', $clcId);
        }

        if (!empty($track)) {
            $enrollmentQuery->where('program_track', $track);
        }

        if (!empty($status)) {
            $enrollmentQuery->where('enrollment_status', $status);
        }

        // 3. Metric Calculations (Agreeing with underlying records)
        $totalIntakes = (clone $enrollmentQuery)->count();
        $totalEnrolled = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_ENROLLED)->count();
        $totalPending = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_PENDING)->count();
        $totalUnderReview = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_UNDER_REVIEW)->count();
        $totalNeedsCorrection = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_NEEDS_CORRECTION)->count();
        $totalRejected = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_REJECTED)->count();
        $totalCompleted = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_COMPLETED)->count();

        // System-wide metrics
        $clcsBaseQuery = CommunityLearningCenter::where('status', 'active');
        if ($isTeacher) {
            $clcsBaseQuery->where('assigned_teacher_id', $currentUser->id);
        }
        $totalClcs = $clcsBaseQuery->count();
        $totalModules = Module::where('is_published', true)->count();
        $totalTelemetrySyncs = ModuleProgress::count();

        // 4. Track Distribution
        $trackCounts = [
            'BLP' => (clone $enrollmentQuery)->where('program_track', Enrollment::TRACK_BLP)->count(),
            'Elementary' => (clone $enrollmentQuery)->where('program_track', Enrollment::TRACK_AE_ELEMENTARY)->count(),
            'Junior_High' => (clone $enrollmentQuery)->where('program_track', Enrollment::TRACK_AE_JUNIOR_HIGH)->count(),
        ];
        $totalTrackRecords = array_sum($trackCounts);

        $trackPercentages = [
            'BLP' => $totalTrackRecords > 0 ? round(($trackCounts['BLP'] / $totalTrackRecords) * 100, 1) : 0,
            'Elementary' => $totalTrackRecords > 0 ? round(($trackCounts['Elementary'] / $totalTrackRecords) * 100, 1) : 0,
            'Junior_High' => $totalTrackRecords > 0 ? round(($trackCounts['Junior_High'] / $totalTrackRecords) * 100, 1) : 0,
        ];

        // 5. Recent Intakes Queue (Scoped and Filtered)
        $recentEnrollments = (clone $enrollmentQuery)
            ->with(['user.profile', 'clc', 'assignedTeacher.profile'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // 6. Recent Telemetry Syncs
        $telemetryQuery = ModuleProgress::with(['user.profile', 'module.strand']);
        if ($isTeacher) {
            // Scope telemetry to teacher's assigned learners or CLC learners
            $telemetryQuery->whereHas('user.enrollments', function ($eq) use ($currentUser) {
                $eq->where(function ($q) use ($currentUser) {
                    $q->where('assigned_teacher_id', $currentUser->id)
                      ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
                });
            });
        }
        $recentTelemetry = $telemetryQuery->latest('synced_at')->take(6)->get();

        // Recent offline mini-game scores, scoped to assigned learners for mobile teachers.
        $gameAttemptsQuery = ModuleGameAttempt::with(['user.profile', 'module.strand']);
        if ($isTeacher) {
            $gameAttemptsQuery->whereHas('user.enrollments', function ($eq) use ($currentUser) {
                $eq->where(function ($q) use ($currentUser) {
                    $q->where('assigned_teacher_id', $currentUser->id)
                      ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
                });
            });
        }
        $recentGameAttempts = $gameAttemptsQuery->latest('attempted_at')->take(8)->get();

        // 7. CLCs Summary with dynamic active learners count respecting active filters
        $clcsSummaryQuery = CommunityLearningCenter::where('status', 'active');
        if ($isTeacher) {
            $clcsSummaryQuery->where('assigned_teacher_id', $currentUser->id);
        }
        if (!empty($clcId)) {
            $clcsSummaryQuery->where('id', $clcId);
        }

        $clcsSummary = $clcsSummaryQuery->withCount(['enrollments as active_learners' => function ($q) use ($currentUser, $isTeacher, $calendarYear, $dateFrom, $dateTo, $track) {
            $q->where('enrollment_status', Enrollment::STATUS_ENROLLED);
            if ($isTeacher) {
                $q->where(function ($sq) use ($currentUser) {
                    $sq->where('assigned_teacher_id', $currentUser->id)
                       ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
                });
            }
            if (!empty($calendarYear)) {
                $q->where('calendar_year', $calendarYear);
            }
            if (!empty($dateFrom)) {
                $q->whereDate('created_at', '>=', $dateFrom);
            }
            if (!empty($dateTo)) {
                $q->whereDate('created_at', '<=', $dateTo);
            }
            if (!empty($track)) {
                $q->where('program_track', $track);
            }
        }])
        ->with('assignedTeacher.profile')
        ->orderByDesc('active_learners')
        ->take(6)
        ->get();

        // All available CLCs for the filter dropdown
        $filterClcs = CommunityLearningCenter::where('status', 'active')
            ->when($isTeacher, fn ($q) => $q->where('assigned_teacher_id', $currentUser->id))
            ->orderBy('clc_name')
            ->get();

        // Available calendar years
        $availableYears = Enrollment::select('calendar_year')
            ->distinct()
            ->orderByDesc('calendar_year')
            ->pluck('calendar_year')
            ->toArray();
        if (empty($availableYears)) {
            $availableYears = ['2026'];
        }

        // Active filters list for display chips
        $activeFilters = [];
        if ($dateRange !== 'all') {
            $label = match($dateRange) {
                'today' => 'Today',
                '7d' => 'Past 7 Days',
                '30d' => 'Past 30 Days',
                'this_month' => 'This Month',
                'custom' => "From $dateFrom to $dateTo",
                default => $dateRange
            };
            $activeFilters['date_range'] = ['label' => 'Period: ' . $label, 'param' => 'date_range'];
        } elseif (!empty($dateFrom) || !empty($dateTo)) {
            $activeFilters['custom_date'] = ['label' => "Date: " . ($dateFrom ?: 'Any') . " to " . ($dateTo ?: 'Any'), 'param' => 'date_from'];
        }
        if (!empty($calendarYear)) {
            $activeFilters['calendar_year'] = ['label' => "Year: $calendarYear", 'param' => 'calendar_year'];
        }
        if (!empty($clcId)) {
            $selectedClc = $filterClcs->firstWhere('id', $clcId);
            $activeFilters['clc_id'] = ['label' => 'CLC: ' . ($selectedClc ? $selectedClc->clc_name : "ID #$clcId"), 'param' => 'clc_id'];
        }
        if (!empty($track)) {
            $activeFilters['track'] = ['label' => 'Track: ' . str_replace('_', ' ', $track), 'param' => 'track'];
        }
        if (!empty($status)) {
            $activeFilters['status'] = ['label' => 'Status: ' . ucfirst(str_replace('_', ' ', $status)), 'param' => 'status'];
        }

        // Metrics dictionary metadata for documentation and UI tooltips
        $metricsDefinitions = self::getMetricsDefinitions();

        return view('admin.dashboard', compact(
            'totalIntakes',
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
            'totalTrackRecords',
            'recentEnrollments',
            'recentTelemetry',
            'recentGameAttempts',
            'clcsSummary',
            'filterClcs',
            'availableYears',
            'activeFilters',
            'dateRange',
            'dateFrom',
            'dateTo',
            'calendarYear',
            'clcId',
            'track',
            'status',
            'isTeacher',
            'metricsDefinitions'
        ));
    }

    /**
     * Dictionary defining each dashboard metric, formula, and source.
     */
    public static function getMetricsDefinitions(): array
    {
        return [
            'total_enrolled' => [
                'name' => 'Officially Enrolled',
                'description' => 'Learners whose registration and intake credentials have been fully verified and enrolled into the official DepEd ALS masterlist.',
                'source' => 'enrollments.enrollment_status = "enrolled"',
                'policy_ref' => 'DepEd Order No. 27, s. 2017 / ALS Act RA 11510',
            ],
            'total_pending' => [
                'name' => 'Pending Initial Review',
                'description' => 'Newly submitted online Form 2 applications awaiting initial assessment and document verification by mobile teachers or registrars.',
                'source' => 'enrollments.enrollment_status = "pending"',
                'policy_ref' => 'SDO San Juan Intake Verification Protocol',
            ],
            'total_under_review' => [
                'name' => 'Under Active Review',
                'description' => 'Applications actively undergoing document verification, prior learning evaluation, or placement interview.',
                'source' => 'enrollments.enrollment_status = "under_review"',
                'policy_ref' => 'DepEd ALS Assessment & Placement Standards',
            ],
            'total_needs_correction' => [
                'name' => 'Needs Correction',
                'description' => 'Applications flagged with incomplete, blurred, or missing requirements where the learner has been notified to re-upload documents.',
                'source' => 'enrollments.enrollment_status = "needs_correction"',
                'policy_ref' => 'DepEd ALS Document Verification Standards',
            ],
            'total_rejected' => [
                'name' => 'Ineligible / Rejected',
                'description' => 'Applications determined to be ineligible (e.g. failing DepEd age qualifications or duplicate submissions) with traceable rejection remarks.',
                'source' => 'enrollments.enrollment_status = "rejected"',
                'policy_ref' => 'DepEd ALS Entry Qualification Rules',
            ],
            'total_completed' => [
                'name' => 'Completed / Graduated',
                'description' => 'Learners who have completed their program track curriculum or passed the A&E accreditation exam.',
                'source' => 'enrollments.enrollment_status = "completed"',
                'policy_ref' => 'DepEd ALS Completion Certification',
            ],
            'total_intakes' => [
                'name' => 'Total Processed Intakes',
                'description' => 'Total volume of digital Form 2 submissions and walk-in intakes recorded in the system within the selected filter period.',
                'source' => 'enrollments (COUNT of all non-deleted records in scope)',
                'policy_ref' => 'Division Enrolment Registry',
            ],
            'active_clcs' => [
                'name' => 'Active Community Learning Centers',
                'description' => 'Community learning centers and barangay halls actively delivering ALS programs in San Juan City.',
                'source' => 'community_learning_centers.status = "active"',
                'policy_ref' => 'San Juan City CLC Mapping & Directory',
            ],
            'published_modules' => [
                'name' => 'Published Learning Modules',
                'description' => 'Self-Learning Modules (SLMs) across the 6 ALS Learning Strands approved and published for offline download and study.',
                'source' => 'modules.is_published = true',
                'policy_ref' => 'DepEd Bureau of Alternative Education (BAE) SLMs',
            ],
            'offline_telemetry' => [
                'name' => 'Offline Telemetry Sync Packets',
                'description' => 'Reading sessions, page logs, and completion events synced from Android LMS mobile devices upon reconnecting to the internet.',
                'source' => 'module_progress (COUNT of all sync log records)',
                'policy_ref' => 'ALS Offline-First Telemetry Architecture',
            ],
            'track_distribution' => [
                'name' => 'Program Track Distribution',
                'description' => 'Enrollment breakdown across the three ALS tracks: Basic Literacy Program (BLP), A&E Elementary, and A&E Junior High School.',
                'source' => 'enrollments.program_track IN ("BLP", "A&E_Elementary", "A&E_Junior_High")',
                'policy_ref' => 'DepEd Curriculum Standards for ALS K to 12',
            ],
            'clc_summaries' => [
                'name' => 'CLC Active Learner Allocations',
                'description' => 'Current enrolled learner population assigned to each Community Learning Center and its assigned Mobile Teacher.',
                'source' => 'enrollments.clc_id GROUP BY clc_id WHERE enrollment_status = "enrolled"',
                'policy_ref' => 'DepEd SDO San Juan Resource Allocation',
            ],
        ];
    }
}

