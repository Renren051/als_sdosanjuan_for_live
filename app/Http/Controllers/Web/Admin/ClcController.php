<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClcController extends Controller
{
    /**
     * Display all CLCs with assigned teachers and enrollment counts.
     */
    public function index(): View
    {
        $clcs = CommunityLearningCenter::with(['assignedTeacher.profile'])
            ->withCount([
                'enrollments as total_learners',
                'enrollments as active_learners' => function ($q) {
                    $q->where('enrollment_status', Enrollment::STATUS_ENROLLED);
                },
                'enrollments as pending_learners' => function ($q) {
                    $q->where('enrollment_status', Enrollment::STATUS_PENDING);
                },
            ])
            ->orderBy('barangay')
            ->get();

        $teachers = User::where('role', User::ROLE_MOBILE_TEACHER)
            ->where('status', User::STATUS_ACTIVE)
            ->with('profile')
            ->get();

        $barangays = [
            'Addition Hills', 'Balong-Bato', 'Batis', 'Corazon de Jesus', 'Ermitaño',
            'Salapan', 'San Perfecto', 'Santa Lucia', 'Tibagan', 'Progreso',
            'Rivera', 'Pedro Cruz', 'Maytunas', 'Isabelita', 'Kabayanan',
            'St. Joseph (Halo-halo)', 'Pasadena', 'Little Baguio', 'Greenhills',
            'West Crame', 'Onse'
        ];

        return view('admin.clcs.index', compact('clcs', 'teachers', 'barangays'));
    }

    /**
     * Store new CLC.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clc_name' => ['required', 'string', 'max:150'],
            'clc_code' => ['required', 'string', 'max:30', 'unique:community_learning_centers,clc_code'],
            'clc_type' => ['required', 'string', 'max:50'],
            'barangay' => ['required', 'string', 'max:100'],
            'physical_location_notes' => ['nullable', 'string', 'max:500'],
            'assigned_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        CommunityLearningCenter::create($validated);

        return back()->with('success', 'New Community Learning Center created successfully.');
    }

    /**
     * Update CLC and teacher assignment.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $clc = CommunityLearningCenter::findOrFail($id);

        $validated = $request->validate([
            'clc_name' => ['required', 'string', 'max:150'],
            'clc_code' => ['required', 'string', 'max:30', 'unique:community_learning_centers,clc_code,' . $clc->id],
            'clc_type' => ['required', 'string', 'max:50'],
            'barangay' => ['required', 'string', 'max:100'],
            'physical_location_notes' => ['nullable', 'string', 'max:500'],
            'assigned_teacher_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $clc->update($validated);

        return back()->with('success', 'CLC updated successfully.');
    }

    /**
     * Reassign learners in bulk based on schedule shift or CLC transfer.
     */
    public function reassignLearners(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enrollment_ids' => ['required', 'array'],
            'enrollment_ids.*' => ['exists:enrollments,id'],
            'target_clc_id' => ['required', 'exists:community_learning_centers,id'],
            'target_teacher_id' => ['nullable', 'exists:users,id'],
            'schedule_preference' => ['nullable', 'in:weekend,weekday_am,weekday_pm,flexible'],
        ]);

        $clc = CommunityLearningCenter::find($validated['target_clc_id']);
        $teacherId = $validated['target_teacher_id'] ?: $clc->assigned_teacher_id;

        $updateData = [
            'clc_id' => $clc->id,
            'assigned_teacher_id' => $teacherId,
        ];

        if (!empty($validated['schedule_preference'])) {
            $updateData['schedule_preference'] = $validated['schedule_preference'];
        }

        Enrollment::whereIn('id', $validated['enrollment_ids'])->update($updateData);

        return back()->with('success', count($validated['enrollment_ids']) . ' learner(s) successfully reassigned.');
    }
}
