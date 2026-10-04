<?php

namespace Database\Seeders;

use App\Models\LearningStrand;
use Illuminate\Database\Seeder;

class LearningStrandSeeder extends Seeder
{
    /**
     * Run the database seeds for ALS Learning Strands.
     */
    public function run(): void
    {
        $strands = [
            [
                'code' => 'LS1_ENG',
                'name' => 'Communication Skills (English)',
                'description' => 'Development of listening, speaking, reading, writing, and viewing competencies in English for daily communication and academic readiness.',
                'color_code' => '#1d4ed8', // Blue
                'icon_name' => 'chat-bubble-left-right',
                'order_seq' => 1,
            ],
            [
                'code' => 'LS1_FIL',
                'name' => 'Communication Skills (Filipino)',
                'description' => 'Pagpapaunlad ng kasanayan sa pakikinig, pagsasalita, pagbasa, pagsulat, at panonood sa wikang Filipino para sa epektibong komunikasyon.',
                'color_code' => '#dc2626', // Red
                'icon_name' => 'book-open',
                'order_seq' => 2,
            ],
            [
                'code' => 'LS2',
                'name' => 'Scientific Literacy and Critical Thinking Skills',
                'description' => 'Application of scientific knowledge, investigative skills, environmental stewardship, health literacy, and critical problem solving in everyday situations.',
                'color_code' => '#059669', // Emerald
                'icon_name' => 'beaker',
                'order_seq' => 3,
            ],
            [
                'code' => 'LS3',
                'name' => 'Mathematical and Problem Solving Skills',
                'description' => 'Numeracy, measurement, geometry, business arithmetic, data analysis, and mathematical reasoning applied to daily life and financial literacy.',
                'color_code' => '#d97706', // Amber
                'icon_name' => 'calculator',
                'order_seq' => 4,
            ],
            [
                'code' => 'LS4',
                'name' => 'Life and Career Skills',
                'description' => 'Workplace readiness, entrepreneurship, technical-vocational awareness, personal productivity, and leadership skills for sustainable livelihood.',
                'color_code' => '#7c3aed', // Purple
                'icon_name' => 'briefcase',
                'order_seq' => 5,
            ],
            [
                'code' => 'LS5',
                'name' => 'Understanding the Self and Society',
                'description' => 'Filipino cultural heritage, community participation, civic responsibility, human rights, peace education, and global citizenship.',
                'color_code' => '#0891b2', // Cyan
                'icon_name' => 'globe-asia-australia',
                'order_seq' => 6,
            ],
            [
                'code' => 'LS6',
                'name' => 'Digital Citizenship',
                'description' => 'Safe, ethical, and productive utilization of digital devices, internet connectivity, mobile applications, software tools, and online safety practices.',
                'color_code' => '#4f46e5', // Indigo
                'icon_name' => 'device-phone-mobile',
                'order_seq' => 7,
            ],
        ];

        foreach ($strands as $strand) {
            LearningStrand::updateOrCreate(
                ['code' => $strand['code']],
                $strand
            );
        }
    }
}
