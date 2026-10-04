<?php

namespace Database\Seeders;

use App\Models\CommunityLearningCenter;
use App\Models\User;
use Illuminate\Database\Seeder;

class SanJuanClcSeeder extends Seeder
{
    /**
     * Run the database seeds for San Juan City CLCs.
     */
    public function run(): void
    {
        $teacherJuan = User::where('username', 'teacher_juan')->first();
        $teacherAlyssa = User::where('username', 'teacher_alyssa')->first();

        $clcs = [
            [
                'clc_name' => 'West Crame Community Learning Center',
                'clc_code' => 'CLC-SJ-WCR-01',
                'clc_type' => 'Barangay Hall',
                'barangay' => 'West Crame',
                'physical_location_notes' => '3rd Floor, West Crame Barangay Hall, 1st Avenue corner Road 1',
                'assigned_teacher_id' => $teacherJuan?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Addition Hills Integrated School CLC',
                'clc_code' => 'CLC-SJ-ADH-01',
                'clc_type' => 'Public School',
                'barangay' => 'Addition Hills',
                'physical_location_notes' => 'Room 102, ALS Building, Addition Hills Integrated School, C. Salvador St.',
                'assigned_teacher_id' => $teacherJuan?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'San Juan National High School CLC',
                'clc_code' => 'CLC-SJ-CDJ-01',
                'clc_type' => 'Public School',
                'barangay' => 'Corazon de Jesus',
                'physical_location_notes' => 'Ground Floor, DepEd Building, N. Domingo St.',
                'assigned_teacher_id' => $teacherAlyssa?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Batis Barangay Hall Learning Center',
                'clc_code' => 'CLC-SJ-BAT-01',
                'clc_type' => 'Barangay Hall',
                'barangay' => 'Batis',
                'physical_location_notes' => 'Multi-purpose Hall, Batis Barangay Hall, F. Manalo St.',
                'assigned_teacher_id' => $teacherAlyssa?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Salapan Elementary School CLC',
                'clc_code' => 'CLC-SJ-SAL-01',
                'clc_type' => 'Public School',
                'barangay' => 'Salapan',
                'physical_location_notes' => 'ALS Room, Salapan Elementary School, F. Roman St.',
                'assigned_teacher_id' => $teacherJuan?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Pedro Cruz Community Learning Center',
                'clc_code' => 'CLC-SJ-PCR-01',
                'clc_type' => 'Community Center',
                'barangay' => 'Pedro Cruz',
                'physical_location_notes' => 'Covered Court Multi-Purpose Room, Pedro Cruz',
                'assigned_teacher_id' => $teacherAlyssa?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'San Juan City Jail Bureau (BJMP) CLC',
                'clc_code' => 'CLC-SJ-BJMP-01',
                'clc_type' => 'BJMP / Jail Facility',
                'barangay' => 'Corazon de Jesus',
                'physical_location_notes' => 'BJMP San Juan City Jail Inmate Education Room',
                'assigned_teacher_id' => $teacherJuan?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Kabayanan Elementary School CLC',
                'clc_code' => 'CLC-SJ-KAB-01',
                'clc_type' => 'Public School',
                'barangay' => 'Kabayanan',
                'physical_location_notes' => 'Building B, Kabayanan Elementary School, I. Lopez St.',
                'assigned_teacher_id' => $teacherAlyssa?->id,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Santa Lucia Multi-Purpose Center CLC',
                'clc_code' => 'CLC-SJ-STL-01',
                'clc_type' => 'Community Center',
                'barangay' => 'Santa Lucia',
                'physical_location_notes' => '2nd Floor, Sta. Lucia Covered Court Building',
                'assigned_teacher_id' => null,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Balong-Bato Community Learning Center',
                'clc_code' => 'CLC-SJ-BLB-01',
                'clc_type' => 'Barangay Hall',
                'barangay' => 'Balong-Bato',
                'physical_location_notes' => 'Balong-Bato Session Hall, A. Bonifacio St.',
                'assigned_teacher_id' => null,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Rivera Covered Court CLC',
                'clc_code' => 'CLC-SJ-RIV-01',
                'clc_type' => 'Community Center',
                'barangay' => 'Rivera',
                'physical_location_notes' => 'Rivera Covered Court Annex, F. Blumentritt St.',
                'assigned_teacher_id' => null,
                'status' => 'active',
            ],
            [
                'clc_name' => 'Pasadena Community Learning Center',
                'clc_code' => 'CLC-SJ-PAS-01',
                'clc_type' => 'Community Center',
                'barangay' => 'Pasadena',
                'physical_location_notes' => 'Pasadena Daycare & ALS Center, F. Manalo St.',
                'assigned_teacher_id' => null,
                'status' => 'active',
            ],
        ];

        foreach ($clcs as $clc) {
            CommunityLearningCenter::updateOrCreate(
                ['clc_code' => $clc['clc_code']],
                $clc
            );
        }
    }
}
