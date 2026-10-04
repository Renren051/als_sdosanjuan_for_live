<?php

namespace Database\Seeders;

use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $targetDir = storage_path('app/public/modules');

        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $modulesData = [
            // LS1 - English
            [
                'strand_code' => 'LS1_ENG',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 1',
                'title' => 'Effective Communication and Reading Strategies',
                'description' => 'Master reading comprehension, main ideas, supporting details, and contextual vocabulary for daily communication.',
                'version' => '1.0',
            ],
            [
                'strand_code' => 'LS1_ENG',
                'level' => Module::LEVEL_ELEMENTARY,
                'module_number' => 'Module 2',
                'title' => 'Basic Sentence Construction and Daily Dialogue',
                'description' => 'Learn fundamental English grammar, subject-verb agreement, and polite expressions in community settings.',
                'version' => '1.0',
            ],

            // LS1 - Filipino
            [
                'strand_code' => 'LS1_FIL',
                'level' => Module::LEVEL_BLP,
                'module_number' => 'Modyul 1',
                'title' => 'Pagkilala sa mga Titik, Tunog, at Pangunahing Salita',
                'description' => 'Pangunahing pagbasa at pagsulat ng mga payak na salita, pantig, at pangungusap sa wikang Filipino.',
                'version' => '1.0',
            ],
            [
                'strand_code' => 'LS1_FIL',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Modyul 2',
                'title' => 'Mapanuring Pagbasa at Pagsulat ng Sanaysay',
                'description' => 'Pagpapalawak ng kasanayan sa kritikal na pag-unawa sa teksto, opinyon, at pagpapahayag ng sariling kaisipan.',
                'version' => '1.0',
            ],

            // LS2 - Science
            [
                'strand_code' => 'LS2',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 1',
                'title' => 'Urban Environmental Protection and Solid Waste Management',
                'description' => 'Scientific understanding of ecological balance, 3Rs (Reduce, Reuse, Recycle), and San Juan environmental ordinances.',
                'version' => '1.1',
            ],
            [
                'strand_code' => 'LS2',
                'level' => Module::LEVEL_ELEMENTARY,
                'module_number' => 'Module 2',
                'title' => 'Health, Nutrition, and Disease Prevention',
                'description' => 'Fundamentals of personal hygiene, balanced nutrition, common communicable illnesses, and community health centers.',
                'version' => '1.0',
            ],

            // LS3 - Math
            [
                'strand_code' => 'LS3',
                'level' => Module::LEVEL_BLP,
                'module_number' => 'Module 1',
                'title' => 'Fundamental Numeracy and Counting Money',
                'description' => 'Basic numbers, simple addition and subtraction, and practical money calculation for grocery and market transactions.',
                'version' => '1.0',
            ],
            [
                'strand_code' => 'LS3',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 2',
                'title' => 'Financial Literacy, Percentages, and Micro-Budgeting',
                'description' => 'Practical calculation of simple interest, discounts, profit margins, household budgeting, and saving habits.',
                'version' => '1.2',
            ],

            // LS4 - Life and Career
            [
                'strand_code' => 'LS4',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 1',
                'title' => 'Workplace Ethics, Rights, and Employability Skills',
                'description' => 'Resume writing, job interview preparation, professional communication, and Philippine labor rights overview.',
                'version' => '1.0',
            ],
            [
                'strand_code' => 'LS4',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 2',
                'title' => 'Starting and Managing a Micro-Enterprise in San Juan City',
                'description' => 'Business ideation, local barangay permits, cost estimation, customer service, and digital selling channels.',
                'version' => '1.0',
            ],

            // LS5 - Self and Society
            [
                'strand_code' => 'LS5',
                'level' => Module::LEVEL_ELEMENTARY,
                'module_number' => 'Module 1',
                'title' => 'Ang Aking Barangay at Pamayanan sa San Juan',
                'description' => 'Pagkilala sa mga tungkulin at serbisyo ng lokal na pamahalaan, barangay officials, at aktibong pakikilahok.',
                'version' => '1.0',
            ],
            [
                'strand_code' => 'LS5',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 2',
                'title' => 'Human Rights, Civic Duties, and Peace Education',
                'description' => 'The Philippine Constitution Bill of Rights, dispute mediation, gender sensitivity, and responsible community membership.',
                'version' => '1.0',
            ],

            // LS6 - Digital Citizenship
            [
                'strand_code' => 'LS6',
                'level' => Module::LEVEL_ELEMENTARY,
                'module_number' => 'Module 1',
                'title' => 'Introduction to Smartphones, Tablets, and Mobile Apps',
                'description' => 'Basic navigation, touchscreen gestures, Wi-Fi connectivity, app installation, and battery management.',
                'version' => '1.0',
            ],
            [
                'strand_code' => 'LS6',
                'level' => Module::LEVEL_JUNIOR_HIGH,
                'module_number' => 'Module 2',
                'title' => 'Online Safety, Fact-Checking, and Cyberbullying Awareness',
                'description' => 'Protecting personal data, identifying fake news, cybercrime prevention law, and ethical online communication.',
                'version' => '1.1',
            ],
        ];

        foreach ($modulesData as $data) {
            $strand = LearningStrand::where('code', $data['strand_code'])->first();
            if (!$strand) {
                continue;
            }

            $fileName = Str::slug("{$strand->code}-{$data['level']}-{$data['module_number']}") . '.pdf';
            $relativeStoragePath = 'modules/' . $fileName;
            $fullFilePath = storage_path('app/public/' . $relativeStoragePath);

            // Generate clean, authentic DepEd ALS Module PDF
            $html = $this->generateModuleHtml($strand, $data);
            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
            $pdf->save($fullFilePath);

            $fileSizeBytes = File::exists($fullFilePath) ? File::size($fullFilePath) : 102400;

            Module::updateOrCreate(
                [
                    'strand_id' => $strand->id,
                    'level' => $data['level'],
                    'module_number' => $data['module_number'],
                ],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'pdf_file_path' => $relativeStoragePath,
                    'pdf_file_size' => $fileSizeBytes,
                    'version' => $data['version'],
                    'is_published' => true,
                    'uploaded_by' => $admin->id,
                ]
            );
        }
    }

    /**
     * Generate HTML layout for ALS module PDF conforming to DepEd guidelines.
     */
    private function generateModuleHtml(LearningStrand $strand, array $data): string
    {
        $dateStr = date('F Y');
        $primaryColor = $strand->color_code ?? '#1e3a8a';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11pt; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double {$primaryColor}; padding-bottom: 12px; margin-bottom: 20px; }
        .deped-title { font-size: 11pt; font-weight: bold; color: #0f172a; text-transform: uppercase; margin: 0; }
        .sdo-title { font-size: 13pt; font-weight: bold; color: {$primaryColor}; text-transform: uppercase; margin: 4px 0; }
        .als-title { font-size: 10pt; color: #475569; margin: 0; font-style: italic; }
        .module-banner { background-color: {$primaryColor}; color: #ffffff; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; }
        .strand-badge { font-size: 10pt; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; opacity: 0.9; }
        .module-title { font-size: 17pt; font-weight: bold; margin: 6px 0 2px 0; }
        .module-meta { font-size: 9.5pt; opacity: 0.85; }
        .section-title { font-size: 12pt; font-weight: bold; color: {$primaryColor}; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-top: 20px; margin-bottom: 8px; }
        .callout-box { background-color: #f8fafc; border-left: 4px solid {$primaryColor}; padding: 10px 14px; margin: 12px 0; font-size: 10.5pt; }
        .page-break { page-break-after: always; }
        .footer { text-align: center; font-size: 8.5pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; position: fixed; bottom: 0; left: 0; right: 0; }
        ul, ol { padding-left: 20px; }
        li { margin-bottom: 6px; }
        .exercise-box { border: 1px dashed {$primaryColor}; background-color: #f0fdf4; padding: 12px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <p class="deped-title">Republic of the Philippines &bull; Department of Education</p>
        <p class="sdo-title">Schools Division Office &bull; San Juan City</p>
        <p class="als-title">Alternative Learning System (ALS) &bull; Self-Learning Module</p>
    </div>

    <div class="module-banner">
        <div class="strand-badge">{$strand->code} &bull; {$strand->name}</div>
        <div class="module-title">{$data['module_number']}: {$data['title']}</div>
        <div class="module-meta">Academic Level: {$data['level']} &bull; Version: {$data['version']} &bull; San Juan City ALS</div>
    </div>

    <div class="section-title">I. WHAT I NEED TO KNOW (Alamin Natin)</div>
    <p>{$data['description']}</p>
    <div class="callout-box">
        <strong>Expected Competencies & Learning Objectives:</strong>
        <ul>
            <li>Understand core concepts and practical real-world applications in San Juan community life.</li>
            <li>Demonstrate proficiency through guided self-assessment and practical modular exercises.</li>
            <li>Apply key learnings to daily problem-solving, employment, or academic progression.</li>
        </ul>
    </div>

    <div class="section-title">II. LESSON OVERVIEW & DISCUSSION (Tuklasin at Suriin)</div>
    <p>This module is prepared by the SDO San Juan City Alternative Learning System Division for out-of-school youth and adults (OSYA). It is optimized for both printed community study and offline mobile reading using the ALS San Juan Mobile LMS App.</p>
    <p>Key takeaways include critical reflection, community participation, and mastering essential skills needed for the Accreditation & Equivalency (A&E) national readiness examination.</p>

    <div class="page-break"></div>

    <div class="header">
        <p class="deped-title">SDO San Juan City &bull; Alternative Learning System</p>
        <p class="als-title">{$strand->code} - {$data['title']}</p>
    </div>

    <div class="section-title">III. GUIDED PRACTICE & APPLICATION (Isagawa Natin)</div>
    <div class="exercise-box">
        <strong>Activity 1: Community Connection</strong>
        <p>Answer the following guide questions on your learner study notebook or through the ALS San Juan App reflection section:</p>
        <ol>
            <li>How does the topic of <em>{$data['title']}</em> apply to your life in your barangay in San Juan City?</li>
            <li>What concrete steps can you take to share this knowledge with your family and neighbors?</li>
            <li>Cite one challenge you encountered while studying this lesson and how you solved it.</li>
        </ol>
    </div>

    <div class="section-title">IV. POST-ASSESSMENT & REFLECTION (Tayahin)</div>
    <p>Submit your completed written reflections during your weekly consultation with your assigned Mobile Teacher at your Community Learning Center (CLC).</p>
    <div class="callout-box">
        <strong>Mobile Sync Note:</strong> When you read this module on your mobile phone, your progress is automatically saved locally. Once connected to the internet, your reading status updates directly to your teacher's dashboard.
    </div>

    <div class="footer">
        SDO San Juan City ALS Module Repository &bull; Property of the Department of Education &bull; Not for Sale &bull; {$dateStr}
    </div>
</body>
</html>
HTML;
    }
}
