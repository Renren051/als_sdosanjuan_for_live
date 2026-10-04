<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Export DepEd ALS Form 1 (Masterlist of Enrolled Learners).
     * GET /api/v1/admin/reports/form-1?clc_id={id}&year={year}&format={pdf|json}
     */
    public function exportForm1(Request $request)
    {
        $year = $request->input('year', '2026');
        $clcId = $request->input('clc_id');

        $query = Enrollment::with(['user.profile', 'clc', 'assignedTeacher.profile'])
            ->where('calendar_year', $year);

        if ($clcId) {
            $query->where('clc_id', $clcId);
            $selectedClc = CommunityLearningCenter::with('assignedTeacher.profile')->find($clcId);
        } else {
            $selectedClc = null;
        }

        $enrollments = $query->orderBy('program_track')
            ->get();

        if ($request->input('format') === 'json') {
            return response()->json([
                'success' => true,
                'calendar_year' => $year,
                'clc' => $selectedClc,
                'total_learners' => $enrollments->count(),
                'enrollments' => $enrollments,
            ]);
        }

        $html = view('reports.form1_masterlist', [
            'year' => $year,
            'clc' => $selectedClc,
            'enrollments' => $enrollments,
            'generatedAt' => now()->format('F d, Y h:i A'),
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        $filename = 'ALS_Form_1_Masterlist_' . ($selectedClc ? $selectedClc->clc_code : 'All_CLCs') . "_{$year}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Export individual DepEd ALS Form 2 (Learner Enrolment Form).
     * GET /api/v1/admin/reports/form-2/{learner_id}
     */
    public function exportForm2($learnerId, Request $request)
    {
        $learner = User::with(['profile', 'latestEnrollment.clc', 'latestEnrollment.assignedTeacher.profile'])
            ->findOrFail($learnerId);

        $enrollment = $learner->latestEnrollment;

        if ($request->input('format') === 'json') {
            return response()->json([
                'success' => true,
                'learner' => $learner,
                'enrollment' => $enrollment,
            ]);
        }

        $html = view('reports.form2_intake', [
            'learner' => $learner,
            'profile' => $learner->profile,
            'enrollment' => $enrollment,
            'generatedAt' => now()->format('F d, Y'),
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        $filename = 'ALS_Form_2_' . ($learner->lrn ?: $learner->username) . '.pdf';

        return $pdf->stream($filename);
    }
}
