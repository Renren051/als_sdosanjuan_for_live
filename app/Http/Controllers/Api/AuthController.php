<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate and return bearer token + user role.
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->input('login');
        $password = $request->input('password');
        $deviceName = $request->input('device_name', 'Android-LMS-App');

        // Check by username or email
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials do not match our records.'],
            ]);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive. Please contact your ALS Mobile Teacher or Administrator.',
            ], 403);
        }

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Create Sanctum Token
        $token = $user->createToken($deviceName)->plainTextToken;

        // Load profile and latest enrollment
        $user->load(['profile', 'latestEnrollment.clc', 'latestEnrollment.assignedTeacher.profile']);

        return response()->json([
            'success' => true,
            'message' => 'Authenticated successfully.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'lrn' => $user->lrn,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'full_name' => $user->full_name,
                    'profile' => $user->profile,
                    'latest_enrollment' => $user->latestEnrollment,
                ],
            ],
        ]);
    }

    /**
     * Submit online enrollment (Form 2 intake) & register learner account.
     * POST /api/v1/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            // 1. Create User
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_LEARNER,
                'status' => User::STATUS_ACTIVE,
            ]);

            // 2. Create User Profile
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
                'province' => 'Metro Manila',
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

            // 3. Find CLC & assigned teacher
            $clc = CommunityLearningCenter::find($validated['clc_id']);

            // 4. Create Initial Enrollment Record
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

        // Generate token for auto-login
        $token = $user->createToken('Learner-App-Registration')->plainTextToken;
        $user->load(['profile', 'latestEnrollment.clc']);

        return response()->json([
            'success' => true,
            'message' => 'Registration and ALS Form 2 intake submitted successfully.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'full_name' => $user->full_name,
                    'profile' => $user->profile,
                    'enrollment' => $user->latestEnrollment,
                ],
            ],
        ], 201);
    }

    /**
     * Get current authenticated user profile & active enrollment.
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
            'documents',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * Logout and revoke tokens.
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
