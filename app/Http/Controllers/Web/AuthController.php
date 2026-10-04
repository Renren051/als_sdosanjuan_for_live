<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $credentials['login'];
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('login'))
                ->with('error', 'Invalid login credentials. Please check your username/email and password.');
        }

        if (!$user->isActive()) {
            return back()->with('error', 'Your account is currently inactive. Please contact your ALS Mobile Teacher or Division Administrator.');
        }

        Auth::login($user, $request->boolean('remember'));
        $user->update(['last_login_at' => now()]);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Show registration & digital ALS Form 2 intake wizard.
     */
    public function showRegister(): View
    {
        $clcs = CommunityLearningCenter::where('status', 'active')
            ->orderBy('barangay')
            ->get();

        return view('auth.register', compact('clcs'));
    }

    /**
     * Handle online ALS Form 2 intake registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Account credentials
            'username' => ['required', 'string', 'min:4', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Demographics (ALS Form 2)
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:10'],
            'birthdate' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'civil_status' => ['required', 'in:single,married,widowed,separated,solo_parent,other'],
            'contact_number' => ['required', 'string', 'max:25'],
            'facebook_name' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'highest_education_attained' => ['required', 'string', 'max:150'],

            // Form 2 Demographics
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'is_ip' => ['nullable', 'boolean'],
            'ip_community_name' => ['nullable', 'string', 'max:100'],
            'is_pwd' => ['nullable', 'boolean'],
            'pwd_disability_type' => ['nullable', 'string', 'max:150'],
            'is_4ps_beneficiary' => ['nullable', 'boolean'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:25'],

            // Track & CLC Selection
            'program_track' => ['required', 'in:' . implode(',', [
                Enrollment::TRACK_BLP,
                Enrollment::TRACK_AE_ELEMENTARY,
                Enrollment::TRACK_AE_JUNIOR_HIGH,
            ])],
            'clc_id' => ['required', 'exists:community_learning_centers,id'],
            'delivery_mode' => ['nullable', 'in:modular_offline,modular_print,face_to_face,blended'],
            'schedule_preference' => ['nullable', 'in:weekend,weekday_am,weekday_pm,flexible'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
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
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
                'highest_education_attained' => $validated['highest_education_attained'],
                'mother_tongue' => $validated['mother_tongue'] ?? 'Tagalog / Filipino',
                'is_ip' => $validated['is_ip'] ?? false,
                'ip_community_name' => $validated['ip_community_name'] ?? null,
                'is_pwd' => $validated['is_pwd'] ?? false,
                'pwd_disability_type' => $validated['pwd_disability_type'] ?? null,
                'is_4ps_beneficiary' => $validated['is_4ps_beneficiary'] ?? false,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_relationship' => $validated['guardian_relationship'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
            ]);

            $clc = CommunityLearningCenter::find($validated['clc_id']);

            Enrollment::create([
                'user_id' => $user->id,
                'calendar_year' => '2026',
                'program_track' => $validated['program_track'],
                'clc_id' => $clc->id,
                'assigned_teacher_id' => $clc->assigned_teacher_id,
                'enrollment_status' => Enrollment::STATUS_PENDING,
                'delivery_mode' => $validated['delivery_mode'] ?? 'modular_offline',
                'schedule_preference' => $validated['schedule_preference'] ?? 'flexible',
                'remarks' => 'Online ALS Form 2 submission received. Awaiting teacher document review.',
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('learner.dashboard')->with('success', 'Your ALS Form 2 registration has been submitted successfully! Please proceed to upload your credential documents.');
    }

    /**
     * Log user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user to corresponding portal based on role.
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->isAdmin() || $user->isMobileTeacher()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('learner.dashboard');
    }
}
