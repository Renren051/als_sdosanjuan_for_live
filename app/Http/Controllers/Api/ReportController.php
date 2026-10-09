<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Export DepEd ALS Form 1 (Masterlist of Enrolled Learners).
     * GET /api/v1/admin/reports/form-1?clc_id={id}&year={year}&track={track}&status={status}&format={pdf|json}
     */
    public function exportForm1(Request $request)
    {
        @ini_set('memory_limit', '512M');
        $currentUser = Auth::user();
        $isTeacher = $currentUser && $currentUser->isMobileTeacher();

        $year = $request->input('year', '2026');
        $clcId = $request->input('clc_id');
        $track = $request->input('track');
        $status = $request->input('status');

        // Scoping check for mobile teachers
        if ($clcId && $isTeacher) {
            $assigned = CommunityLearningCenter::where('id', $clcId)
                ->where('assigned_teacher_id', $currentUser->id)
                ->exists();
            if (!$assigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You are not assigned to this Community Learning Center.',
                ], 403);
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
        $summary = AdminReportController::computeForm1Summary($enrollments);

        if ($request->input('format') === 'json') {
            return response()->json([
                'success' => true,
                'calendar_year' => $year,
                'clc' => $selectedClc,
                'total_learners' => $enrollments->count(),
                'summary' => $summary,
                'enrollments' => $enrollments,
            ]);
        }

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
     * Export individual DepEd ALS Form 2 (Learner Enrolment Form).
     * GET /api/v1/admin/reports/form-2/{learner_id}?format={pdf|json}
     */
    public function exportForm2($learnerId, Request $request)
    {
        @ini_set('memory_limit', '512M');
        $currentUser = Auth::user();
        $isTeacher = $currentUser && $currentUser->isMobileTeacher();

        $learner = User::with([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
            'documents',
        ])->where('role', User::ROLE_LEARNER)->findOrFail($learnerId);

        // Security check for mobile teacher scope
        if ($isTeacher) {
            $isScoped = $learner->enrollments()
                ->where(function ($q) use ($currentUser) {
                    $q->where('assigned_teacher_id', $currentUser->id)
                      ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
                })->exists();

            if (!$isScoped) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You cannot access learner reports outside your assigned teaching scope.',
                ], 403);
            }
        }

        $enrollment = $learner->latestEnrollment;

        if ($request->input('format') === 'json') {
            return response()->json([
                'success' => true,
                'learner' => $learner,
                'enrollment' => $enrollment,
                'profile' => $learner->profile,
                'documents' => $learner->documents,
            ]);
        }

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
}

