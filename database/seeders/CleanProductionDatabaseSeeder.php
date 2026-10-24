<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class CleanProductionDatabaseSeeder extends Seeder
{
    /**
     * Run the clean production database seeds.
     * Seeds ONLY foundational data: Strands, CLCs, Administrators, Mobile Teachers, Modules, Banners.
     * ZERO fake/sample learners. ZERO fake enrollments. ZERO test logs.
     */
    public function run(): void
    {
        // 1. Learning Strands (LS1 to LS6)
        $this->call(LearningStrandSeeder::class);

        // 2. Administrators & Mobile Teachers ONLY
        $defaultPassword = Hash::make('Password123!');

        // 2.0 Super Administrator (Division IT Officer / System Admin)
        $superAdmin = User::updateOrCreate(
            ['username' => 'superadmin_als'],
            [
                'email' => 'superadmin.als@deped.gov.ph',
                'password' => $defaultPassword,
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'first_name' => 'Ricardo',
                'middle_name' => 'Alcantara',
                'last_name' => 'Ramos',
                'name_extension' => null,
                'birthdate' => '1980-03-12',
                'gender' => 'male',
                'civil_status' => 'married',
                'contact_number' => '09175558888',
                'facebook_name' => 'Ricardo Alcantara DepEd',
                'street_address' => 'SDO San Juan City Hall Compound',
                'barangay' => 'Pedro Cruz',
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
                'highest_education_attained' => 'Master of Science in Information Technology',
                'mother_tongue' => 'Tagalog / Filipino',
            ]
        );

        // 2.1 Division ALS Administrator / Coordinator
        $admin = User::updateOrCreate(
            ['username' => 'admin_als'],
            [
                'email' => 'admin.als@deped.gov.ph',
                'password' => $defaultPassword,
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'first_name' => 'Maria Elena',
                'middle_name' => 'Cruz',
                'last_name' => 'Santos',
                'name_extension' => null,
                'birthdate' => '1982-05-14',
                'gender' => 'female',
                'civil_status' => 'married',
                'contact_number' => '09171234567',
                'facebook_name' => 'Elena Santos DepEd',
                'street_address' => 'SDO San Juan Bldg., Pinaglabanan St.',
                'barangay' => 'Pedro Cruz',
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
                'highest_education_attained' => 'Master\'s Degree in Educational Management',
                'mother_tongue' => 'Tagalog / Filipino',
            ]
        );

        // 2.2 Mobile Teachers
        $teacher1 = User::updateOrCreate(
            ['username' => 'teacher_juan'],
            [
                'email' => 'juan.delacruz@deped.gov.ph',
                'password' => $defaultPassword,
                'role' => User::ROLE_MOBILE_TEACHER,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $teacher1->id],
            [
                'first_name' => 'Juan',
                'middle_name' => 'Ponce',
                'last_name' => 'Dela Cruz',
                'name_extension' => null,
                'birthdate' => '1990-08-21',
                'gender' => 'male',
                'civil_status' => 'single',
                'contact_number' => '09189876543',
                'facebook_name' => 'Teacher Juan ALS San Juan',
                'street_address' => 'Block 4, 1st Street, West Crame',
                'barangay' => 'West Crame',
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
                'highest_education_attained' => 'Bachelor of Secondary Education',
                'mother_tongue' => 'Tagalog / Filipino',
            ]
        );

        $teacher2 = User::updateOrCreate(
            ['username' => 'teacher_alyssa'],
            [
                'email' => 'alyssa.reyes@deped.gov.ph',
                'password' => $defaultPassword,
                'role' => User::ROLE_MOBILE_TEACHER,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $teacher2->id],
            [
                'first_name' => 'Alyssa',
                'middle_name' => 'Mendoza',
                'last_name' => 'Reyes',
                'name_extension' => null,
                'birthdate' => '1993-11-04',
                'gender' => 'female',
                'civil_status' => 'single',
                'contact_number' => '09205551234',
                'facebook_name' => 'Alyssa Reyes ALS',
                'street_address' => '22 Batis Compound',
                'barangay' => 'Batis',
                'city' => 'San Juan City',
                'province' => 'Metro Manila',
                'highest_education_attained' => 'Bachelor of Elementary Education',
                'mother_tongue' => 'Tagalog / Filipino',
            ]
        );

        // 3. Official 12 San Juan Community Learning Centers
        $this->call(SanJuanClcSeeder::class);

        // 4. Hero Banners for Portal
        $this->call(HeroBannerSeeder::class);

        // 5. Official Modules
        $this->call(ModuleSeeder::class);

        // 6. Import Official DepEd Modules (Elementary, Secondary, BLP)
        try {
            Artisan::call('app:import-elementary-modules');
        } catch (\Throwable $e) {
            // Ignore if already imported
        }

        try {
            Artisan::call('app:import-secondary-modules');
        } catch (\Throwable $e) {
            // Ignore if already imported
        }

        try {
            Artisan::call('app:import-blp-modules');
        } catch (\Throwable $e) {
            // Ignore if already imported
        }
    }
}
