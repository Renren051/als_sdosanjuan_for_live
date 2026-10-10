<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherManagementController extends Controller
{
    /**
     * Display a listing of all Mobile Teachers with filtering and metrics.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $clcFilter = $request->query('clc_id');

        $query = User::query()
            ->where('role', User::ROLE_MOBILE_TEACHER)
            ->with(['profile', 'assignedClcs', 'assignedLearners']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('contact_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($status && in_array($status, [User::STATUS_ACTIVE, User::STATUS_INACTIVE], true)) {
            $query->where('status', $status);
        }

        if ($clcFilter) {
            $query->whereHas('assignedClcs', function ($cq) use ($clcFilter) {
                $cq->where('id', $clcFilter);
            });
        }

        $teachers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Metrics
        $totalTeachers = User::where('role', User::ROLE_MOBILE_TEACHER)->count();
        $activeTeachers = User::where('role', User::ROLE_MOBILE_TEACHER)->where('status', User::STATUS_ACTIVE)->count();
        $inactiveTeachers = User::where('role', User::ROLE_MOBILE_TEACHER)->where('status', User::STATUS_INACTIVE)->count();
        $assignedClcsCount = CommunityLearningCenter::whereNotNull('assigned_teacher_id')->count();

        $allClcs = CommunityLearningCenter::where('status', 'active')->orderBy('clc_name')->get();

        return view('superadmin.teachers.index', compact(
            'teachers',
            'totalTeachers',
            'activeTeachers',
            'inactiveTeachers',
            'assignedClcsCount',
            'allClcs',
            'search',
            'status',
            'clcFilter'
        ));
    }

    /**
     * Store a newly created Mobile Teacher account and profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female'],
            'civil_status' => ['nullable', 'string', 'max:30'],
            'highest_education_attained' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:active,inactive'],
            'clc_ids' => ['nullable', 'array'],
            'clc_ids.*' => ['exists:community_learning_centers,id'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_MOBILE_TEACHER,
                'status' => $validated['status'],
                'email_verified_at' => now(),
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'birthdate' => $request->input('birthdate', '1990-01-01'),
                'contact_number' => $validated['contact_number'] ?? '09170000000',
                'street_address' => $request->input('street_address', 'DepEd SDO San Juan'),
                'barangay' => $request->input('barangay', 'Pedro Cruz'),
                'gender' => $validated['gender'] ?? 'male',
                'civil_status' => $validated['civil_status'] ?? 'single',
                'highest_education_attained' => $validated['highest_education_attained'] ?? 'Bachelor of Secondary Education',
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
            ]);

            if (!empty($validated['clc_ids'])) {
                CommunityLearningCenter::whereIn('id', $validated['clc_ids'])
                    ->update(['assigned_teacher_id' => $user->id]);
            }
        });

        return redirect()->route('superadmin.teachers.index')
            ->with('success', "Mobile Teacher account '{$validated['username']}' created successfully.");
    }

    /**
     * Update the specified Mobile Teacher account.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $teacher = User::where('role', User::ROLE_MOBILE_TEACHER)->findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($teacher->id)],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($teacher->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female'],
            'civil_status' => ['nullable', 'string', 'max:30'],
            'highest_education_attained' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:active,inactive'],
            'clc_ids' => ['nullable', 'array'],
            'clc_ids.*' => ['exists:community_learning_centers,id'],
        ]);

        DB::transaction(function () use ($teacher, $validated, $request) {
            $updateData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $teacher->update($updateData);

            $profileData = [
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'contact_number' => $validated['contact_number'] ?? '09170000000',
                'gender' => $validated['gender'] ?? 'male',
                'civil_status' => $validated['civil_status'] ?? 'single',
                'highest_education_attained' => $validated['highest_education_attained'] ?? 'Bachelor of Secondary Education',
            ];

            if ($teacher->profile) {
                $teacher->profile->update($profileData);
            } else {
                UserProfile::create(array_merge([
                    'birthdate' => $request->input('birthdate', '1990-01-01'),
                    'street_address' => 'DepEd SDO San Juan',
                    'barangay' => 'Pedro Cruz',
                    'city' => 'San Juan City',
                    'province' => 'Metro Manila',
                ], $profileData, ['user_id' => $teacher->id]));
            }

            // Sync CLC assignments
            CommunityLearningCenter::where('assigned_teacher_id', $teacher->id)
                ->update(['assigned_teacher_id' => null]);

            if (!empty($validated['clc_ids'])) {
                CommunityLearningCenter::whereIn('id', $validated['clc_ids'])
                    ->update(['assigned_teacher_id' => $teacher->id]);
            }
        });

        return redirect()->route('superadmin.teachers.index')
            ->with('success', "Mobile Teacher record for '{$teacher->full_name}' updated successfully.");
    }

    /**
     * Toggle a Mobile Teacher account's active/inactive status.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $teacher = User::where('role', User::ROLE_MOBILE_TEACHER)->findOrFail($id);
        $newStatus = $teacher->status === User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        $teacher->update(['status' => $newStatus]);

        $label = $newStatus === User::STATUS_ACTIVE ? 'activated' : 'deactivated';
        return redirect()->route('superadmin.teachers.index')
            ->with('success', "Account for '{$teacher->full_name}' has been {$label}.");
    }

    /**
     * Reset a Mobile Teacher's password.
     */
    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $teacher = User::where('role', User::ROLE_MOBILE_TEACHER)->findOrFail($id);

        $request->validate([
            'new_password' => ['required', 'string', 'min:8'],
        ]);

        $teacher->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return redirect()->route('superadmin.teachers.index')
            ->with('success', "Password for '{$teacher->full_name}' was successfully reset.");
    }

    /**
     * Delete/remove a Mobile Teacher account.
     */
    public function destroy(int $id): RedirectResponse
    {
        $teacher = User::where('role', User::ROLE_MOBILE_TEACHER)->findOrFail($id);

        // Safeguard: Check if teacher has active assigned enrollments
        $assignedLearnerCount = Enrollment::where('assigned_teacher_id', $teacher->id)->count();
        if ($assignedLearnerCount > 0) {
            return redirect()->route('superadmin.teachers.index')
                ->with('error', "Cannot delete '{$teacher->full_name}': teacher currently has {$assignedLearnerCount} assigned learner enrollment(s). Please reassign learners to another teacher before deletion.");
        }

        DB::transaction(function () use ($teacher) {
            CommunityLearningCenter::where('assigned_teacher_id', $teacher->id)
                ->update(['assigned_teacher_id' => null]);

            if ($teacher->profile) {
                $teacher->profile->delete();
            }
            $teacher->delete();
        });

        return redirect()->route('superadmin.teachers.index')
            ->with('success', "Mobile Teacher account deleted successfully.");
    }
}
