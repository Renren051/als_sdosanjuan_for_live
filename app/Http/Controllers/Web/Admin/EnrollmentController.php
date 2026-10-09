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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Display the intake verification queue with matching underlying record counts.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();
        $isTeacher = $currentUser->isMobileTeacher();

        $query = Enrollment::with(['user.profile', 'clc', 'assignedTeacher.profile', 'documents']);

        // Scope mobile teachers to their assigned learners or CLCs
        if ($isTeacher) {
            $query->where(function ($q) use ($currentUser) {
                $q->where('assigned_teacher_id', $currentUser->id)
                  ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
            });
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

        // Compute tab counts matching underlying records for the authenticated staff scope
        $baseCountQuery = Enrollment::query();
        if ($isTeacher) {
            $baseCountQuery->where(function ($q) use ($currentUser) {
                $q->where('assigned_teacher_id', $currentUser->id)
                  ->orWhereHas('clc', fn ($cq) => $cq->where('assigned_teacher_id', $currentUser->id));
            });
        }

        $statusCounts = [
            '' => (clone $baseCountQuery)->count(),
            'pending' => (clone $baseCountQuery)->where('enrollment_status', Enrollment::STATUS_PENDING)->count(),
            'under_review' => (clone $baseCountQuery)->where('enrollment_status', Enrollment::STATUS_UNDER_REVIEW)->count(),
            'enrolled' => (clone $baseCountQuery)->where('enrollment_status', Enrollment::STATUS_ENROLLED)->count(),
            'needs_correction' => (clone $baseCountQuery)->where('enrollment_status', Enrollment::STATUS_NEEDS_CORRECTION)->count(),
            'rejected' => (clone $baseCountQuery)->where('enrollment_status', Enrollment::STATUS_REJECTED)->count(),
            'completed' => (clone $baseCountQuery)->where('enrollment_status', Enrollment::STATUS_COMPLETED)->count(),
        ];

        $clcs = CommunityLearningCenter::where('status', 'active')
            ->orderBy('barangay')
            ->get();

        $teachers = User::where('role', User::ROLE_MOBILE_TEACHER)
            ->where('status', User::STATUS_ACTIVE)
            ->with('profile')
            ->get();

        return view('admin.enrollments.index', compact('enrollments', 'clcs', 'teachers', 'statusCounts'));
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

        $this->authorizeStaffAccess($enrollment);

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
     * Update enrollment status and assign LRN with transition enforcement and mandatory feedback remarks.
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $enrollment = Enrollment::with(['user', 'clc'])->findOrFail($id);

        $this->authorizeStaffAccess($enrollment);

        $validated = $request->validate([
            'enrollment_status' => [
                'required',
                'in:pending,under_review,enrolled,needs_correction,rejected,withdrawn,completed',
            ],
            'lrn' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/', 'unique:users,lrn,' . $enrollment->user_id],
            'remarks' => [
                Rule::requiredIf(in_array($request->input('enrollment_status'), [
                    Enrollment::STATUS_NEEDS_CORRECTION,
                    Enrollment::STATUS_REJECTED,
                ], true)),
                'nullable',
                'string',
                'max:1000',
            ],
            'clc_id' => ['nullable', 'exists:community_learning_centers,id'],
            'assigned_teacher_id' => ['nullable', 'exists:users,id'],
        ], [
            'remarks.required' => 'Please provide specific feedback remarks for the learner when requesting corrections or rejecting an application.',
        ]);

        // Enforce valid status transitions
        if (!$enrollment->canTransitionTo($validated['enrollment_status'])) {
            $msg = "Invalid status transition from '{$enrollment->status_label}' to '" . ucfirst(str_replace('_', ' ', $validated['enrollment_status'])) . "'.";
            return back()->with('error', $msg)->withErrors(['enrollment_status' => $msg]);
        }

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
        $document = LearnerDocument::with('enrollment.clc')->findOrFail($id);

        $this->authorizeStaffAccess($document->enrollment);

        $validated = $request->validate([
            'verification_status' => ['required', 'in:verified,rejected,pending'],
            'verification_notes' => [
                Rule::requiredIf($request->input('verification_status') === 'rejected'),
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'verification_notes.required' => 'Please provide a clear correction note explaining why this document was rejected.',
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
     * Process walk-in manual enrollment intake by Mobile Teacher adhering to full Form 2 standards.
     */
    public function walkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Demographics Part I
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:10'],
            'birthdate' => ['required', 'date', 'before:today', 'after:1920-01-01'],
            'gender' => ['required', 'in:male,female,other'],
            'civil_status' => ['required', 'in:single,married,widowed,separated,solo_parent,other'],
            'contact_number' => ['required', 'string', 'max:25'],
            'facebook_name' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],

            // Education & Social Inclusion Part II
            'highest_education_attained' => ['required', 'string', 'max:150'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'is_4ps_beneficiary' => ['nullable', 'boolean'],
            'is_ip' => ['nullable', 'boolean'],
            'ip_community_name' => ['nullable', 'string', 'max:100', 'required_if:is_ip,1,true'],
            'is_pwd' => ['nullable', 'boolean'],
            'pwd_disability_type' => ['nullable', 'string', 'max:150', 'required_if:is_pwd,1,true'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:25'],

            // Track & CLC Part III
            'program_track' => ['required', 'in:BLP,A&E_Elementary,A&E_Junior_High'],
            'clc_id' => ['required', 'exists:community_learning_centers,id'],
            'lrn' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/', 'unique:users,lrn'],
            'enrollment_status' => ['required', 'in:enrolled,under_review,pending'],
            'delivery_mode' => ['nullable', 'in:modular_offline,modular_print,face_to_face,blended'],
            'schedule_preference' => ['nullable', 'in:weekend,weekday_am,weekday_pm,flexible'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Auto-generate username & default portal password
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
                'name_extension' => $validated['name_extension'] ?? null,
                'birthdate' => $validated['birthdate'],
                'gender' => $validated['gender'],
                'civil_status' => $validated['civil_status'],
                'contact_number' => $validated['contact_number'],
                'facebook_name' => $validated['facebook_name'] ?? null,
                'street_address' => $validated['street_address'],
                'barangay' => $validated['barangay'],
                'city' => $validated['city'] ?? 'San Juan City',
                'province' => $validated['province'] ?? 'Metro Manila',
                'highest_education_attained' => $validated['highest_education_attained'],
                'mother_tongue' => $validated['mother_tongue'] ?? 'Tagalog / Filipino',
                'is_ip' => $validated['is_ip'] ?? false,
                'ip_community_name' => $validated['ip_community_name'] ?? null,
                'is_pwd' => $validated['is_pwd'] ?? false,
                'pwd_disability_type' => $validated['pwd_disability_type'] ?? null,
                'is_4ps_beneficiary' => $validated['is_4ps_beneficiary'] ?? false,
                'father_name' => $validated['father_name'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_relationship' => $validated['guardian_relationship'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
            ]);

            $clc = CommunityLearningCenter::find($validated['clc_id']);

            return Enrollment::create([
                'user_id' => $user->id,
                'calendar_year' => '2026',
                'program_track' => $validated['program_track'],
                'clc_id' => $clc->id,
                'assigned_teacher_id' => $clc->assigned_teacher_id ?: Auth::id(),
                'enrollment_status' => $validated['enrollment_status'],
                'delivery_mode' => $validated['delivery_mode'] ?? 'modular_offline',
                'schedule_preference' => $validated['schedule_preference'] ?? 'flexible',
                'remarks' => ($validated['remarks'] ?? null) ?: ("Walk-in registration encoded by " . Auth::user()->full_name . " on " . now()->timezone('Asia/Manila')->format('F d, Y h:i A')),
                'enrolled_at' => $validated['enrollment_status'] === 'enrolled' ? now() : null,
            ]);
        });

        return redirect()->route('admin.enrollments.show', $enrollment->id)
            ->with('success', "Walk-in learner registered! Reference: [{$enrollment->tracking_number}] | Username: [{$username}] | Default Password: [{$defaultPassword}]");
    }

    /**
     * Authorize staff access to an individual enrollment dossier.
     * Division administrators have division-wide review access.
     * Mobile teachers can only review and edit enrollments assigned to them or their assigned CLC.
     */
    private function authorizeStaffAccess(Enrollment $enrollment): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isMobileTeacher()) {
            $isAssigned = $enrollment->assigned_teacher_id === $user->id
                || ($enrollment->clc && $enrollment->clc->assigned_teacher_id === $user->id);

            if (!$isAssigned) {
                abort(403, 'Unauthorized. You may only review and update enrollments assigned to you or your learning center.');
            }
            return;
        }

        abort(403, 'Unauthorized staff action.');
    }
}
