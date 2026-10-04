<?php

namespace Tests\Feature;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlsApiTest extends TestCase
{
    public function test_login_returns_token_and_user_profile(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'learner_mark',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'lrn',
                        'username',
                        'role',
                        'profile',
                    ],
                ],
            ]);
    }

    public function test_registration_creates_learner_and_enrollment(): void
    {
        $clc = CommunityLearningCenter::first();
        $randomUsername = 'learner_' . time();

        $response = $this->postJson('/api/v1/auth/register', [
            'username' => $randomUsername,
            'email' => "{$randomUsername}@example.com",
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'first_name' => 'John',
            'middle_name' => 'Reyes',
            'last_name' => 'Mercado',
            'birthdate' => '2004-05-10',
            'gender' => 'male',
            'civil_status' => 'single',
            'contact_number' => '09170001122',
            'street_address' => '101 Batis St.',
            'barangay' => 'Batis',
            'highest_education_attained' => 'Grade 7 (Junior High Undergraduate)',
            'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
            'clc_id' => $clc->id,
            'delivery_mode' => 'modular_offline',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', $randomUsername);

        $this->assertDatabaseHas('users', ['username' => $randomUsername]);
    }

    public function test_modules_listing_returns_published_modules(): void
    {
        $response = $this->getJson('/api/v1/modules');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertGreaterThanOrEqual(14, count($response->json('data')));
    }

    public function test_strands_listing_returns_learning_strands(): void
    {
        $response = $this->getJson('/api/v1/strands');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertCount(7, $response->json('data'));
    }

    public function test_learner_can_sync_offline_telemetry_progress(): void
    {
        $user = User::where('username', 'learner_mark')->first();
        $token = $user->createToken('test-sync')->plainTextToken;
        $module = Module::first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/learner/sync-progress', [
                'device_identifier' => 'Android-Test-Phone',
                'items' => [
                    [
                        'module_id' => $module->id,
                        'completion_status' => 'in_progress',
                        'last_page_read' => 3,
                        'total_pages' => 6,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.synced_count', 1);
    }

    public function test_admin_can_export_form1_json(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $token = $admin->createToken('test-admin')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/reports/form-1?year=2026&format=json');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'calendar_year',
                'total_learners',
                'enrollments',
            ]);
    }

    public function test_learner_can_upload_document(): void
    {
        Storage::fake('public');

        $user = User::where('username', 'learner_mark')->first();
        $token = $user->createToken('test-doc-upload')->plainTextToken;
        $file = UploadedFile::fake()->create('psa_birth_certificate.pdf', 500, 'application/pdf');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/v1/learner/documents/upload', [
                'document_type' => 'psa_birth_cert',
                'document_file' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }
}
