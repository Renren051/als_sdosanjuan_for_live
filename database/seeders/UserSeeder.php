<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');

        // 0. Super Administrator (Division IT Officer / System Admin)
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

        // 1. Division ALS Administrator / Coordinator
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

        // 2. Mobile Teachers
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

        // 3. Sample Learners
        $learnersData = [
            [
                'user' => [
                    'lrn' => '136542240001',
                    'username' => 'learner_mark',
                    'email' => 'mark.bautista@example.com',
                ],
                'profile' => [
                    'first_name' => 'Mark Kevin',
                    'middle_name' => 'Villanueva',
                    'last_name' => 'Bautista',
                    'name_extension' => null,
                    'birthdate' => '2005-03-12',
                    'gender' => 'male',
                    'civil_status' => 'single',
                    'contact_number' => '09081234567',
                    'facebook_name' => 'Mark Kevin Bautista',
                    'street_address' => '142 2nd West Crame',
                    'barangay' => 'West Crame',
                    'highest_education_attained' => 'Grade 8 (Junior High Undergraduate)',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => true,
                    'guardian_name' => 'Lourdes V. Bautista',
                    'guardian_relationship' => 'Mother',
                    'emergency_contact_name' => 'Lourdes Bautista',
                    'emergency_contact_number' => '09081234567',
                ],
            ],
            [
                'user' => [
                    'lrn' => '136542240002',
                    'username' => 'learner_jenny',
                    'email' => 'jenny.dizon@example.com',
                ],
                'profile' => [
                    'first_name' => 'Jenny Rose',
                    'middle_name' => 'Domingo',
                    'last_name' => 'Dizon',
                    'name_extension' => null,
                    'birthdate' => '2001-09-28',
                    'gender' => 'female',
                    'civil_status' => 'single',
                    'contact_number' => '09194567890',
                    'facebook_name' => 'Jenny Rose Dizon',
                    'street_address' => '56 A. Lake Street',
                    'barangay' => 'Addition Hills',
                    'highest_education_attained' => 'Grade 5 (Elementary Undergraduate)',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => false,
                    'guardian_name' => 'Rolando Dizon',
                    'guardian_relationship' => 'Father',
                    'emergency_contact_name' => 'Rolando Dizon',
                    'emergency_contact_number' => '09194567890',
                ],
            ],
            [
                'user' => [
                    'lrn' => '136542240003',
                    'username' => 'learner_carlos',
                    'email' => 'carlos.tan@example.com',
                ],
                'profile' => [
                    'first_name' => 'Carlos Miguel',
                    'middle_name' => 'Perez',
                    'last_name' => 'Tan',
                    'name_extension' => 'Jr.',
                    'birthdate' => '1992-06-15',
                    'gender' => 'male',
                    'civil_status' => 'married',
                    'contact_number' => '09228765432',
                    'facebook_name' => 'Carlos Tan Jr.',
                    'street_address' => '89 F. Manalo St.',
                    'barangay' => 'Batis',
                    'highest_education_attained' => 'No Formal Schooling',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => false,
                    'emergency_contact_name' => 'Elena G. Tan',
                    'emergency_contact_number' => '09228765432',
                ],
            ],
            [
                'user' => [
                    'lrn' => '136542240004',
                    'username' => 'learner_bea',
                    'email' => 'bea.ramos@example.com',
                ],
                'profile' => [
                    'first_name' => 'Bea Nicole',
                    'middle_name' => 'Santos',
                    'last_name' => 'Ramos',
                    'name_extension' => null,
                    'birthdate' => '2006-12-05',
                    'gender' => 'female',
                    'civil_status' => 'single',
                    'contact_number' => '09391112233',
                    'facebook_name' => 'Bea Ramos',
                    'street_address' => '12 Aurora Blvd. Extension',
                    'barangay' => 'Salapan',
                    'highest_education_attained' => 'Grade 9 (Junior High Undergraduate)',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => true,
                    'guardian_name' => 'Teresa Ramos',
                    'guardian_relationship' => 'Grandmother',
                    'emergency_contact_name' => 'Teresa Ramos',
                    'emergency_contact_number' => '09391112233',
                ],
            ],
        ];

        foreach ($learnersData as $item) {
            $learner = User::updateOrCreate(
                ['username' => $item['user']['username']],
                [
                    'lrn' => $item['user']['lrn'],
                    'email' => $item['user']['email'],
                    'password' => $defaultPassword,
                    'role' => User::ROLE_LEARNER,
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]
            );

            UserProfile::updateOrCreate(
                ['user_id' => $learner->id],
                array_merge($item['profile'], [
                    'city' => 'San Juan City',
                    'province' => 'Metro Manila',
                    'mother_tongue' => 'Tagalog / Filipino',
                ])
            );
        }
    }
}
