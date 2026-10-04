<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class WebPortalTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200)
            ->assertSee('SDO San Juan City')
            ->assertSee('Alternative Learning System');
    }

    public function test_registration_wizard_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200)
            ->assertSee('Digital Learner Enrolment Intake')
            ->assertSee('Step 1: Learner Personal Information');
    }

    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200)
            ->assertSee('Executive Dashboard')
            ->assertSee('Officially Enrolled');
    }

    public function test_admin_can_access_enrollment_queue(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/enrollments');
        $response->assertStatus(200)
            ->assertSee('Enrollment Intake & Verification Queue');
    }

    public function test_admin_can_access_clc_manager(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/clcs');
        $response->assertStatus(200)
            ->assertSee('San Juan City Learning Center Network');
    }

    public function test_admin_can_access_module_repository(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/modules');
        $response->assertStatus(200)
            ->assertSee('Official DepEd Self-Learning Modules');
    }

    public function test_admin_can_access_form1_report(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/reports/form-1');
        $response->assertStatus(200)
            ->assertSee('MASTERLIST OF ENROLLED LEARNERS (ALS FORM 1)');
    }

    public function test_learner_can_access_learner_portal(): void
    {
        $learner = User::where('username', 'learner_mark')->first();

        $response = $this->actingAs($learner)->get('/learner/dashboard');
        $response->assertStatus(200)
            ->assertSee('Officially Enrolled')
            ->assertSee('136542240001');

        $docResponse = $this->actingAs($learner)->get('/learner/documents');
        $docResponse->assertStatus(200)
            ->assertSee('PSA Birth Certificate');

        $modResponse = $this->actingAs($learner)->get('/learner/modules');
        $modResponse->assertStatus(200)
            ->assertSee('Self-Learning Module Library');
    }
}
