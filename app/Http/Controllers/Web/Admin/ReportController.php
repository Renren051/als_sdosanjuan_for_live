<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Preview & Generate DepEd ALS Form 1 (Masterlist of Enrolled Learners).
     */
    public function form1(Request $request): View
    {
        $year = $request->input('year', '2026');
        $clcId = $request->input('clc_id');

        $clcs = CommunityLearningCenter::where('status', 'active')
            ->orderBy('barangay')
            ->get();

        $query = Enrollment::with(['user.profile', 'clc', 'assignedTeacher.profile'])
            ->where('calendar_year', $year);

        if ($clcId) {
            $query->where('clc_id', $clcId);
            $selectedClc = CommunityLearningCenter::with('assignedTeacher.profile')->find($clcId);
        } else {
            $selectedClc = null;
        }

        $enrollments = $query->orderBy('program_track')->get();

        return view('admin.reports.form1', compact('clcs', 'enrollments', 'selectedClc', 'year'));
    }

    /**
     * Preview & Generate DepEd ALS Form 2 (Learner Enrolment Form).
     */
    public function form2(Request $request): View
    {
        $learnerId = $request->input('learner_id');

        $learners = User::where('role', User::ROLE_LEARNER)
            ->with(['profile', 'latestEnrollment.clc'])
            ->get();

        $selectedLearner = null;
        if ($learnerId) {
            $selectedLearner = User::with([
                'profile',
                'latestEnrollment.clc',
                'latestEnrollment.assignedTeacher.profile',
                'documents',
            ])->find($learnerId);
        } elseif ($learners->isNotEmpty()) {
            $selectedLearner = $learners->first();
        }

        return view('admin.reports.form2', compact('learners', 'selectedLearner'));
    }
}
