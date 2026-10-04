<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Executive / Mobile Teacher Dashboard.
     */
    public function index(): View
    {
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        // Query scope: if teacher, filter to their assigned CLCs/learners
        $enrollmentQuery = Enrollment::query();
        if ($isTeacher) {
            $enrollmentQuery->where('assigned_teacher_id', $currentUser->id);
        }

        $totalEnrolled = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_ENROLLED)->count();
        $totalPending = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_PENDING)->count();
        $totalUnderReview = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_UNDER_REVIEW)->count();
        $totalNeedsCorrection = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_NEEDS_CORRECTION)->count();
        $totalCompleted = (clone $enrollmentQuery)->where('enrollment_status', Enrollment::STATUS_COMPLETED)->count();

        // System-wide metrics
        $totalClcs = CommunityLearningCenter::where('status', 'active')->count();
        $totalModules = Module::where('is_published', true)->count();
        $totalTelemetrySyncs = ModuleProgress::count();

        // Track distribution
        $trackCounts = [
            'BLP' => (clone $enrollmentQuery)->where('program_track', Enrollment::TRACK_BLP)->count(),
            'Elementary' => (clone $enrollmentQuery)->where('program_track', Enrollment::TRACK_AE_ELEMENTARY)->count(),
            'Junior_High' => (clone $enrollmentQuery)->where('program_track', Enrollment::TRACK_AE_JUNIOR_HIGH)->count(),
        ];

        // Recent Intakes
        $recentEnrollments = (clone $enrollmentQuery)
            ->with(['user.profile', 'clc', 'assignedTeacher.profile'])
            ->latest()
            ->take(8)
            ->get();

        // Recent Telemetry Syncs
        $recentTelemetry = ModuleProgress::with(['user.profile', 'module.strand'])
            ->latest('synced_at')
            ->take(6)
            ->get();

        // CLCs summary with counts
        $clcsSummary = CommunityLearningCenter::withCount(['enrollments as active_learners' => function ($q) {
            $q->where('enrollment_status', Enrollment::STATUS_ENROLLED);
        }])
        ->with('assignedTeacher.profile')
        ->orderByDesc('active_learners')
        ->take(6)
        ->get();

        return view('admin.dashboard', compact(
            'totalEnrolled',
            'totalPending',
            'totalUnderReview',
            'totalNeedsCorrection',
            'totalCompleted',
            'totalClcs',
            'totalModules',
            'totalTelemetrySyncs',
            'trackCounts',
            'recentEnrollments',
            'recentTelemetry',
            'clcsSummary',
            'isTeacher'
        ));
    }
}
