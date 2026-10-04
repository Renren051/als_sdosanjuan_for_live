<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Display the intake verification queue.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        $query = Enrollment::with(['user.profile', 'clc', 'assignedTeacher.profile', 'documents']);

        if ($isTeacher) {
            $query->where('assigned_teacher_id', $currentUser->id);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('enrollment_status', $request->input('status'));
        }

        // CLC Filter
        if ($request->filled('clc_id')) {
            $query->where('clc_id', $request->input('clc_id'));
        }

        // Track Filter
        if ($request->filled('track')) {
            $query->where('program_track', $request->input('track'));
        }

        // Search Filter
        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('lrn', 'like', $search)
                  ->orWhere('username', 'like', $search)
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', $search)
                         ->orWhere('last_name', 'like', $search)
                         ->orWhere('barangay', 'like', $search);
                  });
            });
        }

        $enrollments = $query->latest()->paginate(15)->withQueryString();

        $clcs = CommunityLearningCenter::where('status', 'active')
            ->orderBy('barangay')
            ->get();

        $teachers = User::where('role', User::ROLE_MOBILE_TEACHER)
            ->where('status', User::STATUS_ACTIVE)
            ->with('profile')
            ->get();

        return view('admin.enrollments.index', compact('enrollments', 'clcs', 'teachers'));
    }

    /**
     * Display detailed ALS Form 2 digital intake & document review screen.
     */
    public function show($id): View
    {
        $enrollment = Enrollment::with([
            'user.profile',
            'clc',
            'assignedTeacher.profile',
            'documents.reviewer.profile',
        ])->findOrFail($id);

        $clcs = CommunityLearningCenter::where('status', 'active')
            ->orderBy('barangay')
            ->get();

        $teachers = User::where('role', User::ROLE_MOBILE_TEACHER)
            ->where('status', User::STATUS_ACTIVE)
            ->with('profile')
            ->get();

        return view('admin.enrollments.show', compact('enrollment', 'clcs', 'teachers'));
    }

    /**
     * Update enrollment status and assign LRN.
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $enrollment = Enrollment::with('user')->findOrFail($id);

        $validated = $request->validate([
            'enrollment_status' => ['required', 'in:pending,under_review,enrolled,needs_correction,withdrawn,completed'],
            'lrn' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/', 'unique:users,lrn,' . $enrollment->user_id],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'clc_id' => ['nullable', 'exists:community_learning_centers,id'],
            'assigned_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        DB::transaction(function () use ($enrollment, $validated) {
            // Assign LRN if provided
            if (!empty($validated['lrn'])) {
                $enrollment->user->update(['lrn' => $validated['lrn']]);
            }

            $updateData = [
                'enrollment_status' => $validated['enrollment_status'],
                'remarks' => $validated['remarks'] ?? $enrollment->remarks,
            ];

            if (!empty($validated['clc_id'])) {
                $updateData['clc_id'] = $validated['clc_id'];
            }

            if (!empty($validated['assigned_teacher_id'])) {
                $updateData['assigned_teacher_id'] = $validated['assigned_teacher_id'];
            }

            if ($validated['enrollment_status'] === Enrollment::STATUS_ENROLLED && !$enrollment->enrolled_at) {
                $updateData['enrolled_at'] = now();
            }

            if ($validated['enrollment_status'] === Enrollment::STATUS_COMPLETED && !$enrollment->completed_at) {
                $updateData['completed_at'] = now();
            }

            $enrollment->update($updateData);
        });

        return back()->with('success', 'Enrollment status and applicant records updated successfully.');
    }

    /**
     * Verify or reject an individual submitted document.
     */
    public function updateDocumentStatus(Request $request, $id): RedirectResponse
    {
        $document = LearnerDocument::findOrFail($id);

        $validated = $request->validate([
            'verification_status' => ['required', 'in:verified,rejected,pending'],
            'verification_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $document->update([
            'verification_status' => $validated['verification_status'],
            'verification_notes' => $validated['verification_notes'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Document status updated to {$validated['verification_status']}.");
    }

    /**
     * Process walk-in manual enrollment intake by Mobile Teacher.
     */
    public function walkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birthdate' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'contact_number' => ['required', 'string', 'max:25'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'highest_education_attained' => ['required', 'string', 'max:150'],
            'program_track' => ['required', 'in:BLP,A&E_Elementary,A&E_Junior_High'],
            'clc_id' => ['required', 'exists:community_learning_centers,id'],
            'lrn' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/', 'unique:users,lrn'],
            'enrollment_status' => ['required', 'in:enrolled,under_review,pending'],
        ]);

        // Auto-generate username & temporary password
        $cleanFirst = Str::slug($validated['first_name']);
        $cleanLast = Str::slug($validated['last_name']);
        $baseUsername = "{$cleanFirst}.{$cleanLast}";
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = "{$baseUsername}{$counter}";
            $counter++;
        }

        $defaultPassword = 'ALS@' . date('Y');

        $enrollment = DB::transaction(function () use ($validated, $username, $defaultPassword) {
            $user = User::create([
                'lrn' => $validated['lrn'] ?? null,
                'username' => $username,
                'email' => "{$username}@als-sanjuan.local",
                'password' => Hash::make($defaultPassword),
                'role' => User::ROLE_LEARNER,
                'status' => User::STATUS_ACTIVE,
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'birthdate' => $validated['birthdate'],
                'gender' => $validated['gender'],
                'civil_status' => 'single',
                'contact_number' => $validated['contact_number'],
                'street_address' => $validated['street_address'],
                'barangay' => $validated['barangay'],
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
                'highest_education_attained' => $validated['highest_education_attained'],
            ]);

            $clc = CommunityLearningCenter::find($validated['clc_id']);

            return Enrollment::create([
                'user_id' => $user->id,
                'calendar_year' => '2026',
                'program_track' => $validated['program_track'],
                'clc_id' => $clc->id,
                'assigned_teacher_id' => $clc->assigned_teacher_id ?: Auth::id(),
                'enrollment_status' => $validated['enrollment_status'],
                'delivery_mode' => 'modular_offline',
                'remarks' => "Walk-in registration encoded by " . Auth::user()->full_name,
                'enrolled_at' => $validated['enrollment_status'] === 'enrolled' ? now() : null,
            ]);
        });

        return redirect()->route('admin.enrollments.show', $enrollment->id)
            ->with('success', "Walk-in learner registered! Username: [{$username}] | Default Password: [{$defaultPassword}]");
    }
}
