<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Preview & Generate DepEd ALS Form 1 (Masterlist of Enrolled Learners).
     */
    public function form1(Request $request): View
    {
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        $year = $request->input('year', '2026');
        $clcId = $request->input('clc_id');
        $track = $request->input('track');
        $status = $request->input('status', Enrollment::STATUS_ENROLLED);

        // CLC list: if teacher, only their assigned CLCs
        $clcsQuery = CommunityLearningCenter::where('status', 'active');
        if ($isTeacher) {
            $clcsQuery->where('assigned_teacher_id', $currentUser->id);
        }
        $clcs = $clcsQuery->orderBy('clc_name')->get();

        // Scope check: if teacher specified a CLC outside their scope -> 403
        if ($clcId && $isTeacher) {
            $teacherClcIds = $clcs->pluck('id')->toArray();
            if (!in_array((int)$clcId, $teacherClcIds)) {
                abort(403, 'Unauthorized: You are not assigned to this Community Learning Center.');
            }
        }

        $query = Enrollment::with(['user.profile', 'clc', 'assignedTeacher.profile']);

        if ($year && $year !== 'all') {
            $query->where('calendar_year', $year);
        }

        if ($isTeacher) {
            $query->where(function ($q) use ($currentUser) {
                $q->where('assigned_teacher_id', $currentUser->id)
                  ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
            });
        }

        if ($clcId) {
            $query->where('clc_id', $clcId);
            $selectedClc = CommunityLearningCenter::with('assignedTeacher.profile')->find($clcId);
        } else {
            $selectedClc = null;
        }

        if ($track && in_array($track, [Enrollment::TRACK_BLP, Enrollment::TRACK_AE_ELEMENTARY, Enrollment::TRACK_AE_JUNIOR_HIGH])) {
            $query->where('program_track', $track);
        }

        if ($status && $status !== 'all') {
            $query->where('enrollment_status', $status);
        }

        $enrollments = $query->orderBy('program_track')->orderBy('created_at')->get();

        $summary = self::computeForm1Summary($enrollments);

        return view('admin.reports.form1', compact('clcs', 'enrollments', 'selectedClc', 'year', 'track', 'status', 'summary', 'isTeacher'));
    }

    /**
     * Export DepEd ALS Form 1 as PDF stream.
     */
    public function exportForm1Pdf(Request $request)
    {
        @ini_set('memory_limit', '512M');
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        $year = $request->input('year', '2026');
        $clcId = $request->input('clc_id');
        $track = $request->input('track');
        $status = $request->input('status', Enrollment::STATUS_ENROLLED);

        if ($clcId && $isTeacher) {
            $assignedClc = CommunityLearningCenter::where('id', $clcId)
                ->where('assigned_teacher_id', $currentUser->id)
                ->exists();
            if (!$assignedClc) {
                abort(403, 'Unauthorized: You are not assigned to this Community Learning Center.');
            }
        }

        $query = Enrollment::with(['user.profile', 'clc', 'assignedTeacher.profile']);

        if ($year && $year !== 'all') {
            $query->where('calendar_year', $year);
        }

        if ($isTeacher) {
            $query->where(function ($q) use ($currentUser) {
                $q->where('assigned_teacher_id', $currentUser->id)
                  ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
            });
        }

        if ($clcId) {
            $query->where('clc_id', $clcId);
            $selectedClc = CommunityLearningCenter::with('assignedTeacher.profile')->find($clcId);
        } else {
            $selectedClc = null;
        }

        if ($track && in_array($track, [Enrollment::TRACK_BLP, Enrollment::TRACK_AE_ELEMENTARY, Enrollment::TRACK_AE_JUNIOR_HIGH])) {
            $query->where('program_track', $track);
        }

        if ($status && $status !== 'all') {
            $query->where('enrollment_status', $status);
        }

        $enrollments = $query->orderBy('program_track')->orderBy('created_at')->get();
        $summary = self::computeForm1Summary($enrollments);

        $html = view('reports.form1_masterlist', [
            'year' => $year,
            'clc' => $selectedClc,
            'enrollments' => $enrollments,
            'summary' => $summary,
            'track' => $track,
            'status' => $status,
            'generatedAt' => now()->format('F d, Y h:i A'),
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        $filename = 'ALS_Form_1_Masterlist_' . ($selectedClc ? $selectedClc->clc_code : 'SDO_San_Juan') . "_{$year}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Preview & Generate DepEd ALS Form 2 (Learner Enrolment Form).
     */
    public function form2(Request $request): View
    {
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        $learnerId = $request->input('learner_id');
        $search = $request->input('q');
        $clcId = $request->input('clc_id');

        $learnersQuery = User::where('role', User::ROLE_LEARNER)
            ->with(['profile', 'latestEnrollment.clc', 'latestEnrollment.assignedTeacher.profile']);

        if ($isTeacher) {
            $learnersQuery->whereHas('enrollments', function ($eq) use ($currentUser) {
                $eq->where('assigned_teacher_id', $currentUser->id)
                   ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
            });
        }

        if ($clcId) {
            $learnersQuery->whereHas('enrollments', fn ($eq) => $eq->where('clc_id', $clcId));
        }

        if ($search) {
            $term = '%' . $search . '%';
            $learnersQuery->where(function ($q) use ($term) {
                $q->where('lrn', 'like', $term)
                  ->orWhere('username', 'like', $term)
                  ->orWhereHas('profile', function ($pq) use ($term) {
                      $pq->where('first_name', 'like', $term)
                         ->orWhere('last_name', 'like', $term);
                  });
            });
        }

        $learners = $learnersQuery->orderBy('username')->get();

        $selectedLearner = null;
        if ($learnerId) {
            $selectedLearner = User::with([
                'profile',
                'latestEnrollment.clc',
                'latestEnrollment.assignedTeacher.profile',
                'documents',
            ])->find($learnerId);

            if ($selectedLearner && $isTeacher) {
                $isScoped = $selectedLearner->enrollments()
                    ->where(function ($q) use ($currentUser) {
                        $q->where('assigned_teacher_id', $currentUser->id)
                          ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
                    })->exists();

                if (!$isScoped) {
                    abort(403, 'Unauthorized: You cannot access learner reports outside your assigned teaching scope.');
                }
            }
        } elseif ($learners->isNotEmpty()) {
            $selectedLearner = User::with([
                'profile',
                'latestEnrollment.clc',
                'latestEnrollment.assignedTeacher.profile',
                'documents',
            ])->find($learners->first()->id);
        }

        $clcs = CommunityLearningCenter::where('status', 'active')
            ->when($isTeacher, fn ($q) => $q->where('assigned_teacher_id', $currentUser->id))
            ->orderBy('clc_name')
            ->get();

        return view('admin.reports.form2', compact('learners', 'selectedLearner', 'clcs', 'clcId', 'search', 'isTeacher'));
    }

    /**
     * Export DepEd ALS Form 2 as PDF stream.
     */
    public function exportForm2Pdf($learnerId, Request $request)
    {
        @ini_set('memory_limit', '512M');
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        $learner = User::with([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
            'documents',
        ])->where('role', User::ROLE_LEARNER)->findOrFail($learnerId);

        if ($isTeacher) {
            $isScoped = $learner->enrollments()
                ->where(function ($q) use ($currentUser) {
                    $q->where('assigned_teacher_id', $currentUser->id)
                      ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
                })->exists();

            if (!$isScoped) {
                abort(403, 'Unauthorized: You cannot access learner reports outside your assigned teaching scope.');
            }
        }

        $enrollment = $learner->latestEnrollment;

        $html = view('reports.form2_intake', [
            'learner' => $learner,
            'profile' => $learner->profile,
            'enrollment' => $enrollment,
            'documents' => $learner->documents,
            'generatedAt' => now()->format('F d, Y'),
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        $filename = 'ALS_Form_2_' . ($learner->lrn ?: $learner->username) . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Compute statistical breakdown summary for Form 1.
     */
    public static function computeForm1Summary($enrollments): array
    {
        $totalLearners = $enrollments->count();

        return [
            'male' => $enrollments->filter(fn ($e) => strtolower($e->user?->profile?->gender ?? '') === 'male')->count(),
            'female' => $enrollments->filter(fn ($e) => strtolower($e->user?->profile?->gender ?? '') === 'female')->count(),
            'total' => $totalLearners,
            'tracks' => [
                'BLP' => [
                    'male' => $enrollments->filter(fn ($e) => $e->program_track === Enrollment::TRACK_BLP && strtolower($e->user?->profile?->gender ?? '') === 'male')->count(),
                    'female' => $enrollments->filter(fn ($e) => $e->program_track === Enrollment::TRACK_BLP && strtolower($e->user?->profile?->gender ?? '') === 'female')->count(),
                    'total' => $enrollments->where('program_track', Enrollment::TRACK_BLP)->count(),
                ],
                'A&E_Elementary' => [
                    'male' => $enrollments->filter(fn ($e) => $e->program_track === Enrollment::TRACK_AE_ELEMENTARY && strtolower($e->user?->profile?->gender ?? '') === 'male')->count(),
                    'female' => $enrollments->filter(fn ($e) => $e->program_track === Enrollment::TRACK_AE_ELEMENTARY && strtolower($e->user?->profile?->gender ?? '') === 'female')->count(),
                    'total' => $enrollments->where('program_track', Enrollment::TRACK_AE_ELEMENTARY)->count(),
                ],
                'A&E_Junior_High' => [
                    'male' => $enrollments->filter(fn ($e) => $e->program_track === Enrollment::TRACK_AE_JUNIOR_HIGH && strtolower($e->user?->profile?->gender ?? '') === 'male')->count(),
                    'female' => $enrollments->filter(fn ($e) => $e->program_track === Enrollment::TRACK_AE_JUNIOR_HIGH && strtolower($e->user?->profile?->gender ?? '') === 'female')->count(),
                    'total' => $enrollments->where('program_track', Enrollment::TRACK_AE_JUNIOR_HIGH)->count(),
                ],
            ],
            'status_breakdown' => [
                'enrolled' => $enrollments->where('enrollment_status', Enrollment::STATUS_ENROLLED)->count(),
                'completed' => $enrollments->where('enrollment_status', Enrollment::STATUS_COMPLETED)->count(),
                'under_review' => $enrollments->where('enrollment_status', Enrollment::STATUS_UNDER_REVIEW)->count(),
                'pending' => $enrollments->where('enrollment_status', Enrollment::STATUS_PENDING)->count(),
                'needs_correction' => $enrollments->where('enrollment_status', Enrollment::STATUS_NEEDS_CORRECTION)->count(),
                'rejected' => $enrollments->where('enrollment_status', Enrollment::STATUS_REJECTED)->count(),
            ],
        ];
    }
}

