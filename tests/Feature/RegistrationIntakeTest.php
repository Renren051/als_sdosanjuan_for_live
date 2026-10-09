<?php

namespace Tests\Feature;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\UserProfile;
use Tests\TestCase;

class RegistrationIntakeTest extends TestCase
{
    protected function getValidIntakeData(array $overrides = []): array
    {
        $clc = CommunityLearningCenter::first();

        return array_merge([
            // Account
            'username' => 'test.learner.' . uniqid(),
            'email' => 'test.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',

            // Demographics (Part I)
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'name_extension' => null,
            'birthdate' => '2004-05-15',
            'gender' => 'female',
            'civil_status' => 'single',
            'contact_number' => '09171234567',
            'facebook_name' => 'Maria Dela Cruz ALS',
            'street_address' => 'Block 4 Lot 12 F. Manalo St.',
            'barangay' => 'Kabayanan',
            'city' => 'San Juan City',

            // Educational Background (Part II)
            'highest_education_attained' => 'Junior High Undergraduate (Grade 7-9)',
            'mother_tongue' => 'Tagalog / Filipino',

            // Social Inclusion
            'is_4ps_beneficiary' => '1',
            'is_ip' => '0',
            'ip_community_name' => null,
            'is_pwd' => '0',
            'pwd_disability_type' => null,

            // Parents & Guardian
            'father_name' => 'Jose Dela Cruz Sr.',
            'mother_name' => 'Corazon Santos Dela Cruz',
            'guardian_name' => 'Jose Dela Cruz Sr.',
            'guardian_relationship' => 'Father',
            'emergency_contact_name' => 'Corazon Santos Dela Cruz',
            'emergency_contact_number' => '09187654321',

            // Program & Center Allocation (Part III)
            'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
            'clc_id' => $clc->id,
            'delivery_mode' => 'modular_offline',
            'schedule_preference' => 'flexible',
        ], $overrides);
    }

    public function test_registration_form_renders_all_form2_sections_and_fields(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertSee('ALS Online Registration')
            ->assertSee('Personal Information')
            ->assertSee('Part 1')
            ->assertSee('Part 2')
            ->assertSee('COMPLETE HOME ADDRESS')
            ->assertSee('PARENTS INFORMATION')
            ->assertSee('last_name')
            ->assertSee('first_name')
            ->assertSee('father_name')
            ->assertSee('mother_name')
            ->assertSee('contact_number');
    }

    public function test_successful_registration_creates_traceable_pending_enrollment_and_complete_profile(): void
    {
        $data = $this->getValidIntakeData();

        $response = $this->post('/register', $data);

        // 1. Successful redirect to dashboard with reference flash
        $response->assertRedirect('/learner/dashboard');
        $response->assertSessionHas('success');

        // 2. User created with Learner role
        $user = User::where('username', $data['username'])->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_LEARNER, $user->role);
        $this->assertEquals(User::STATUS_ACTIVE, $user->status);

        // 3. UserProfile created with complete Form 2 fields
        $profile = $user->profile;
        $this->assertNotNull($profile);
        $this->assertEquals('Maria', $profile->first_name);
        $this->assertEquals('Dela Cruz', $profile->last_name);
        $this->assertEquals('single', $profile->civil_status);
        $this->assertEquals('Kabayanan', $profile->barangay);
        $this->assertEquals('Junior High Undergraduate (Grade 7-9)', $profile->highest_education_attained);
        $this->assertTrue($profile->is_4ps_beneficiary);
        $this->assertFalse($profile->is_ip);
        $this->assertFalse($profile->is_pwd);
        $this->assertEquals('Jose Dela Cruz Sr.', $profile->father_name);
        $this->assertEquals('Corazon Santos Dela Cruz', $profile->mother_name);
        $this->assertEquals('Father', $profile->guardian_relationship);
        $this->assertEquals('Corazon Santos Dela Cruz', $profile->emergency_contact_name);

        // 4. Initial Enrollment record is Pending & assigned teacher from CLC
        $clc = CommunityLearningCenter::find($data['clc_id']);
        $enrollment = $user->latestEnrollment;
        $this->assertNotNull($enrollment);
        $this->assertEquals(Enrollment::STATUS_PENDING, $enrollment->enrollment_status);
        $this->assertEquals($data['program_track'], $enrollment->program_track);
        $this->assertEquals($clc->id, $enrollment->clc_id);
        $this->assertEquals($clc->assigned_teacher_id, $enrollment->assigned_teacher_id);
        $this->assertStringStartsWith('ALS-2026-', $enrollment->tracking_number);

        // 5. Learner Dashboard displays pending status and tracking number
        $dashResponse = $this->actingAs($user)->get('/learner/dashboard');
        $dashResponse->assertStatus(200)
            ->assertSee('Enrolment Application Under Review')
            ->assertSee('Pending Document Verification')
            ->assertSee($enrollment->tracking_number);

        // Clean up test user
        $user->tokens()->delete();
        $user->documents()->delete();
        $user->enrollments()->delete();
        $user->profile()->delete();
        $user->delete();
    }

    public function test_incomplete_or_invalid_submission_is_rejected_with_validation_errors(): void
    {
        // Missing required fields
        $response = $this->post('/register', [
            'username' => 'abc', // Too short (min 4)
            'first_name' => '',  // Required
            'last_name' => '',   // Required
            'password' => '123', // Too short & no confirm
        ]);

        $response->assertSessionHasErrors([
            'username',
            'first_name',
            'last_name',
            'birthdate',
            'contact_number',
            'street_address',
            'barangay',
            'highest_education_attained',
            'program_track',
            'clc_id',
            'password',
        ]);
    }

    public function test_conditional_ip_and_pwd_validation_requires_specifications(): void
    {
        // 1. IP is true without IP community name
        $ipData = $this->getValidIntakeData([
            'is_ip' => '1',
            'ip_community_name' => '',
        ]);

        $response1 = $this->post('/register', $ipData);
        $response1->assertSessionHasErrors('ip_community_name');

        // 2. PWD is true without PWD disability type
        $pwdData = $this->getValidIntakeData([
            'is_pwd' => '1',
            'pwd_disability_type' => '',
        ]);

        $response2 = $this->post('/register', $pwdData);
        $response2->assertSessionHasErrors('pwd_disability_type');
    }

    public function test_duplicate_username_and_email_are_handled_safely(): void
    {
        $existing = User::where('role', User::ROLE_LEARNER)->first();
        $this->assertNotNull($existing);

        // Duplicate username
        $data = $this->getValidIntakeData([
            'username' => $existing->username,
        ]);

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors('username');

        // Duplicate email
        if ($existing->email) {
            $data2 = $this->getValidIntakeData([
                'email' => $existing->email,
            ]);

            $response2 = $this->post('/register', $data2);
            $response2->assertSessionHasErrors('email');
        }
    }

    public function test_authenticated_user_cannot_access_or_submit_registration_again(): void
    {
        $learner = User::where('role', User::ROLE_LEARNER)->first();

        // GET /register redirects authenticated user
        $getResponse = $this->actingAs($learner)->get('/register');
        $getResponse->assertRedirect('/learner/dashboard');

        // POST /register redirects authenticated user without re-registering
        $postResponse = $this->actingAs($learner)->post('/register', $this->getValidIntakeData());
        $postResponse->assertRedirect('/learner/dashboard');
    }

    public function test_api_registration_creates_complete_form2_record_and_returns_tracking_number(): void
    {
        $clc = CommunityLearningCenter::first();
        $username = 'api.learner.' . uniqid();

        $payload = [
            'username' => $username,
            'email' => "{$username}@example.com",
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'first_name' => 'Alex',
            'last_name' => 'Bautista',
            'birthdate' => '2005-08-20',
            'gender' => 'male',
            'civil_status' => 'single',
            'contact_number' => '09191234567',
            'street_address' => '12 N. Domingo St.',
            'barangay' => 'Pedro Cruz',
            'highest_education_attained' => 'Elementary Graduate (Grade 6)',
            'mother_tongue' => 'Tagalog / Filipino',
            'father_name' => 'Ramon Bautista',
            'mother_name' => 'Elena Bautista',
            'emergency_contact_name' => 'Elena Bautista',
            'emergency_contact_number' => '09191234567',
            'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
            'clc_id' => $clc->id,
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'username',
                        'profile',
                        'enrollment' => [
                            'id',
                            'tracking_number',
                            'enrollment_status',
                        ],
                    ],
                ],
            ]);

        $this->assertEquals(Enrollment::STATUS_PENDING, $response->json('data.user.enrollment.enrollment_status'));
        $this->assertNotEmpty($response->json('data.user.enrollment.tracking_number'));

        // Clean up test user
        $apiUserId = $response->json('data.user.id');
        $apiUser = User::find($apiUserId);
        if ($apiUser) {
            $apiUser->tokens()->delete();
            $apiUser->documents()->delete();
            $apiUser->enrollments()->delete();
            $apiUser->profile()->delete();
            $apiUser->delete();
        }
    }
}
