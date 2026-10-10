<?php

namespace Tests\Feature;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminTeacherManagementTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up test users created during test execution
        $testUsers = User::where('username', 'like', 'test_super_%')
            ->orWhere('username', 'like', 'test_teacher_%')
            ->get();

        foreach ($testUsers as $u) {
            CommunityLearningCenter::where('assigned_teacher_id', $u->id)->update(['assigned_teacher_id' => null]);
            Enrollment::where('assigned_teacher_id', $u->id)->forceDelete();
            if ($u->profile) {
                $u->profile->forceDelete();
            }
            $u->forceDelete();
        }

        parent::tearDown();
    }

    private function createSuperAdmin(): User
    {
        $super = User::create([
            'username' => 'test_super_' . uniqid(),
            'email' => 'super_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => User::ROLE_SUPER_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $super->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'birthdate' => '1985-01-01',
            'contact_number' => '09170000000',
            'street_address' => 'SDO San Juan',
            'barangay' => 'Pedro Cruz',
            'highest_education_attained' => 'Master of Science',
        ]);

        return $super;
    }

    private function createMobileTeacher(string $status = User::STATUS_ACTIVE): User
    {
        $teacher = User::create([
            'username' => 'test_teacher_' . uniqid(),
            'email' => 'teacher_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => User::ROLE_MOBILE_TEACHER,
            'status' => $status,
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $teacher->id,
            'first_name' => 'Test',
            'last_name' => 'Teacher',
            'birthdate' => '1990-01-01',
            'contact_number' => '09170000000',
            'street_address' => 'SDO San Juan',
            'barangay' => 'Pedro Cruz',
            'highest_education_attained' => 'Bachelor of Secondary Education',
        ]);

        return $teacher;
    }

    public function test_guest_is_redirected_to_login_from_superadmin(): void
    {
        $response = $this->get(route('superadmin.teachers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_admin_and_teacher_cannot_access_superadmin_teachers(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if ($admin) {
            $this->actingAs($admin)->get(route('superadmin.teachers.index'))->assertStatus(403);
        }

        $teacher = User::where('role', User::ROLE_MOBILE_TEACHER)->first();
        if ($teacher) {
            $this->actingAs($teacher)->get(route('superadmin.teachers.index'))->assertStatus(403);
        }
    }

    public function test_super_admin_can_view_teachers_index(): void
    {
        $super = $this->createSuperAdmin();

        $response = $this->actingAs($super)->get(route('superadmin.teachers.index'));
        $response->assertStatus(200);
        $response->assertSee('Mobile Teacher Management');
        $response->assertSee('Add New Mobile Teacher');
    }

    public function test_super_admin_can_create_new_teacher_and_assign_clc(): void
    {
        $super = $this->createSuperAdmin();
        $clc = CommunityLearningCenter::where('status', 'active')->first();

        $newUsername = 'test_teacher_' . uniqid();
        $newEmail = $newUsername . '@deped.gov.ph';

        $response = $this->actingAs($super)->post(route('superadmin.teachers.store'), [
            'first_name' => 'Ronaldo',
            'middle_name' => 'Cruz',
            'last_name' => 'Mendoza',
            'username' => $newUsername,
            'email' => $newEmail,
            'password' => 'SecurePass123!',
            'contact_number' => '09171112233',
            'status' => 'active',
            'clc_ids' => $clc ? [$clc->id] : [],
        ]);

        $response->assertRedirect(route('superadmin.teachers.index'));
        $response->assertSessionHas('success');

        $created = User::where('username', $newUsername)->first();
        $this->assertNotNull($created);
        $this->assertEquals(User::ROLE_MOBILE_TEACHER, $created->role);
        $this->assertEquals('Ronaldo', $created->profile->first_name);

        if ($clc) {
            $this->assertEquals($created->id, $clc->fresh()->assigned_teacher_id);
        }
    }

    public function test_super_admin_can_update_teacher(): void
    {
        $super = $this->createSuperAdmin();
        $teacher = $this->createMobileTeacher();

        $response = $this->actingAs($super)->put(route('superadmin.teachers.update', $teacher->id), [
            'first_name' => 'UpdatedName',
            'last_name' => 'UpdatedLast',
            'username' => $teacher->username,
            'email' => $teacher->email,
            'contact_number' => '09998887766',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('superadmin.teachers.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('UpdatedName', $teacher->fresh()->profile->first_name);
    }

    public function test_super_admin_can_toggle_teacher_status(): void
    {
        $super = $this->createSuperAdmin();
        $teacher = $this->createMobileTeacher(User::STATUS_ACTIVE);

        $response = $this->actingAs($super)->patch(route('superadmin.teachers.toggleStatus', $teacher->id));
        $response->assertRedirect(route('superadmin.teachers.index'));
        $this->assertEquals(User::STATUS_INACTIVE, $teacher->fresh()->status);

        $response2 = $this->actingAs($super)->patch(route('superadmin.teachers.toggleStatus', $teacher->id));
        $response2->assertRedirect(route('superadmin.teachers.index'));
        $this->assertEquals(User::STATUS_ACTIVE, $teacher->fresh()->status);
    }

    public function test_super_admin_can_reset_teacher_password(): void
    {
        $super = $this->createSuperAdmin();
        $teacher = $this->createMobileTeacher();

        $response = $this->actingAs($super)->post(route('superadmin.teachers.resetPassword', $teacher->id), [
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('superadmin.teachers.index'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewPassword123!', $teacher->fresh()->password));
    }

    public function test_super_admin_cannot_delete_teacher_with_assigned_enrollments(): void
    {
        $super = $this->createSuperAdmin();
        $teacher = $this->createMobileTeacher();

        // Create an enrollment assigned to this teacher
        $learner = User::where('role', User::ROLE_LEARNER)->first();
        if ($learner) {
            Enrollment::create([
                'user_id' => $learner->id,
                'calendar_year' => 2026,
                'program_track' => 'A&E_Junior_High',
                'clc_id' => 1,
                'assigned_teacher_id' => $teacher->id,
                'enrollment_status' => 'enrolled',
                'delivery_mode' => 'modular_offline',
                'schedule_preference' => 'weekend',
            ]);

            $response = $this->actingAs($super)->delete(route('superadmin.teachers.destroy', $teacher->id));
            $response->assertRedirect(route('superadmin.teachers.index'));
            $response->assertSessionHas('error');

            $this->assertNotNull(User::find($teacher->id));
        }
    }

    public function test_super_admin_can_delete_unassigned_teacher(): void
    {
        $super = $this->createSuperAdmin();
        $teacher = $this->createMobileTeacher();

        $response = $this->actingAs($super)->delete(route('superadmin.teachers.destroy', $teacher->id));
        $response->assertRedirect(route('superadmin.teachers.index'));
        $response->assertSessionHas('success');

        $this->assertNull(User::find($teacher->id));
    }
}
