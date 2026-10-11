<?php

namespace App\Console\Commands;

use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportSecondaryModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:import-secondary-modules {--source= : Custom source directory path}';

    /**
     * The console command description.
     */
    protected $description = 'Import authentic DepEd ALS A&E Secondary / Junior High School PDF modules into storage and database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultSource = 'C:\\Users\\Renren\\Documents\\ALS SYSTEMS\\ALS Modules\\A&E Secondary_Junior High School';
        $sourceBase = $this->option('source') ?: $defaultSource;

        if (!File::isDirectory($sourceBase)) {
            $this->error("Source directory not found: {$sourceBase}");
            return Command::FAILURE;
        }

        $this->info("Found Secondary modules source directory: {$sourceBase}");

        // Preload learning strands
        $strands = LearningStrand::all()->keyBy('code');
        $ls1Eng = $strands->get('LS1_ENG')?->id ?? 1;
        $ls1Fil = $strands->get('LS1_FIL')?->id ?? 2;
        $ls2 = $strands->get('LS2')?->id ?? 3;
        $ls3 = $strands->get('LS3')?->id ?? 4;
        $ls4 = $strands->get('LS4')?->id ?? 5;
        $ls5 = $strands->get('LS5')?->id ?? 6;
        $ls6 = $strands->get('LS6')?->id ?? 7;

        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        $targetBase = storage_path('app/public/modules/secondary');
        if (!File::isDirectory($targetBase)) {
            File::makeDirectory($targetBase, 0755, true);
        }

        $totalImported = 0;

        // ---------------------------------------------------------------------
        // 1. CORE DEPED ALS A&E SECONDARY LEVEL MODULES
        // ---------------------------------------------------------------------
        $coreSource = $sourceBase . DIRECTORY_SEPARATOR . 'ALS A&E Secondary Level Modules';
        if (File::isDirectory($coreSource)) {
            $this->info("Importing Core ALS A&E Secondary Level Modules...");
            $coreTarget = $targetBase . DIRECTORY_SEPARATOR . 'core';
            if (!File::isDirectory($coreTarget)) {
                File::makeDirectory($coreTarget, 0755, true);
            }

            for ($strandNum = 1; $strandNum <= 5; $strandNum++) {
                $strandDir = $coreSource . DIRECTORY_SEPARATOR . "Learning Strand {$strandNum}";
                if (!File::isDirectory($strandDir)) continue;

                $files = File::files($strandDir);
                $this->info("Processing Core Strand {$strandNum}: found " . count($files) . " modules.");

                $idx = 1;
                foreach ($files as $file) {
                    if (strtolower($file->getExtension()) !== 'pdf') continue;

                    $rawTitle = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $cleanTitle = trim(str_replace('_', ' ', $rawTitle));
                    $fileSize = $file->getSize();

                    // Generate safe target filename
                    $safeName = Str::slug($cleanTitle) . '.pdf';
                    $destPath = $coreTarget . DIRECTORY_SEPARATOR . $safeName;
                    File::copy($file->getRealPath(), $destPath);
                    $relativeFilePath = "modules/secondary/core/{$safeName}";

                    // Map strand based on curriculum content
                    $targetStrandId = match ($strandNum) {
                        1 => $ls1Eng,
                        2 => $this->isMathTopic($cleanTitle) ? $ls3 : $ls2,
                        3 => $ls4, // Livelihood & Entrepreneurship
                        4 => $ls5, // Civics & Society
                        5 => $ls5, // Peace & International Understanding
                        default => $ls1Eng,
                    };

                    $prefix = match ($targetStrandId) {
                        $ls1Eng => 'SEC-LS1',
                        $ls2 => 'SEC-LS2',
                        $ls3 => 'SEC-LS3',
                        $ls4 => 'SEC-LS4',
                        $ls5 => 'SEC-LS5',
                        default => 'SEC-M',
                    };

                    $moduleNumber = sprintf('M-%s-%02d', $prefix, $idx);

                    Module::updateOrCreate(
                        [
                            'level' => Module::LEVEL_JUNIOR_HIGH,
                            'sub_level' => Module::SUB_LEVEL_JUNIOR_HIGH,
                            'language' => Module::LANGUAGE_ENGLISH,
                            'title' => $cleanTitle,
                        ],
                        [
                            'strand_id' => $targetStrandId,
                            'module_number' => $moduleNumber,
                            'description' => "DepEd ALS A&E Secondary (Junior High School) Self-Learning Module: {$cleanTitle}.",
                            'pdf_file_path' => $relativeFilePath,
                            'pdf_file_size' => $fileSize,
                            'version' => '1.0',
                            'is_published' => true,
                            'uploaded_by' => $adminId,
                        ]
                    );

                    $idx++;
                    $totalImported++;
                }
            }
        }

        // ---------------------------------------------------------------------
        // 2. UNESCO - KOICA JHS MODULES (Learner modules only; exclude _SG)
        // ---------------------------------------------------------------------
        $unescoSource = $sourceBase . DIRECTORY_SEPARATOR . 'UNESCO - KOICA JHS Modules';
        if (File::isDirectory($unescoSource)) {
            $this->info("Importing UNESCO - KOICA Junior High School Modules...");
            $unescoTarget = $targetBase . DIRECTORY_SEPARATOR . 'unesco';
            if (!File::isDirectory($unescoTarget)) {
                File::makeDirectory($unescoTarget, 0755, true);
            }

            $unescoConfigs = [
                'LS1- Communication Skills (English)' => ['strand' => $ls1Eng, 'lang' => Module::LANGUAGE_ENGLISH, 'prefix' => 'UN-JHS-LS1E', 'name' => 'Communication Skills (English)'],
                'LS1- Communication Skills (Filipino)' => ['strand' => $ls1Fil, 'lang' => Module::LANGUAGE_FILIPINO, 'prefix' => 'UN-JHS-LS1F', 'name' => 'Kasanayang Pangkomunikasyon (Filipino)'],
                'LS2-Scientific and Critical Thinking Skills' => ['strand' => $ls2, 'lang' => Module::LANGUAGE_ENGLISH, 'prefix' => 'UN-JHS-LS2', 'name' => 'Scientific & Critical Thinking'],
                'LS3-Mathematical and Problem-Solving Skills' => ['strand' => $ls3, 'lang' => Module::LANGUAGE_ENGLISH, 'prefix' => 'UN-JHS-LS3', 'name' => 'Mathematical & Problem Solving'],
                'LS4- Life and Career Skills' => ['strand' => $ls4, 'lang' => Module::LANGUAGE_ENGLISH, 'prefix' => 'UN-JHS-LS4', 'name' => 'Life and Career Skills'],
                'LS5- Understanding the Self and Society' => ['strand' => $ls5, 'lang' => Module::LANGUAGE_ENGLISH, 'prefix' => 'UN-JHS-LS5', 'name' => 'Understanding Self & Society'],
                'LS6- Digital Citizenship' => ['strand' => $ls6, 'lang' => Module::LANGUAGE_ENGLISH, 'prefix' => 'UN-JHS-LS6', 'name' => 'Digital Citizenship'],
            ];

            foreach ($unescoConfigs as $folderName => $cfg) {
                $folderPath = $unescoSource . DIRECTORY_SEPARATOR . $folderName;
                if (!File::isDirectory($folderPath)) continue;

                $files = File::files($folderPath);
                $idx = 1;
                foreach ($files as $file) {
                    if (strtolower($file->getExtension()) !== 'pdf') continue;

                    $rawFilename = $file->getFilename();
                    // Exclude Teacher Session Guides
                    if (preg_match('/(_SG|\bSG\b|_SG01|\(SG\))/i', $rawFilename)) {
                        continue;
                    }

                    $fileSize = $file->getSize();
                    $cleanTitle = $this->formatUnescoTitle($rawFilename, $cfg['name']);

                    // Extract version tag if present
                    $version = '1.0';
                    if (preg_match('/\(V([0-9\.]+)\)/i', $rawFilename, $vm)) {
                        $version = $vm[1];
                    }

                    $safeName = Str::slug(pathinfo($rawFilename, PATHINFO_FILENAME)) . '.pdf';
                    $destPath = $unescoTarget . DIRECTORY_SEPARATOR . $safeName;
                    File::copy($file->getRealPath(), $destPath);
                    $relativeFilePath = "modules/secondary/unesco/{$safeName}";

                    $moduleNumber = sprintf('M-%s-%02d', $cfg['prefix'], $idx);

                    Module::updateOrCreate(
                        [
                            'level' => Module::LEVEL_JUNIOR_HIGH,
                            'sub_level' => Module::SUB_LEVEL_JUNIOR_HIGH,
                            'language' => $cfg['lang'],
                            'title' => $cleanTitle,
                        ],
                        [
                            'strand_id' => $cfg['strand'],
                            'module_number' => $moduleNumber,
                            'description' => "UNESCO-KOICA DepEd ALS JHS Module: {$cleanTitle}.",
                            'pdf_file_path' => $relativeFilePath,
                            'pdf_file_size' => $fileSize,
                            'version' => $version,
                            'is_published' => true,
                            'uploaded_by' => $adminId,
                        ]
                    );

                    $idx++;
                    $totalImported++;
                }
            }
        }

        // ---------------------------------------------------------------------
        // 3. MYDEV & OPPORTUNITY 2.0 (Life & Career Skills)
        // ---------------------------------------------------------------------
        $mydevSource = $sourceBase . DIRECTORY_SEPARATOR . 'MYDev and Opportunity 2.0';
        if (File::isDirectory($mydevSource)) {
            $this->info("Importing MYDev and Opportunity 2.0 Modules...");
            $mydevTarget = $targetBase . DIRECTORY_SEPARATOR . 'mydev';
            if (!File::isDirectory($mydevTarget)) {
                File::makeDirectory($mydevTarget, 0755, true);
            }

            $files = File::files($mydevSource);
            $idx = 1;
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) !== 'pdf') continue;

                $rawTitle = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $cleanTitle = 'Opportunity 2.0 - ' . trim(str_replace(['Module ', 'Module-'], 'Module ', $rawTitle));
                $fileSize = $file->getSize();

                $safeName = Str::slug($rawTitle) . '.pdf';
                $destPath = $mydevTarget . DIRECTORY_SEPARATOR . $safeName;
                File::copy($file->getRealPath(), $destPath);
                $relativeFilePath = "modules/secondary/mydev/{$safeName}";

                $moduleNumber = sprintf('M-MYDEV-JHS-%02d', $idx);

                Module::updateOrCreate(
                    [
                        'level' => Module::LEVEL_JUNIOR_HIGH,
                        'sub_level' => Module::SUB_LEVEL_JUNIOR_HIGH,
                        'language' => Module::LANGUAGE_ENGLISH,
                        'title' => $cleanTitle,
                    ],
                    [
                        'strand_id' => $ls4,
                        'module_number' => $moduleNumber,
                        'description' => "USAID / DepEd ALS Opportunity 2.0 Work Readiness Module: {$cleanTitle}.",
                        'pdf_file_path' => $relativeFilePath,
                        'pdf_file_size' => $fileSize,
                        'version' => '2.0',
                        'is_published' => true,
                        'uploaded_by' => $adminId,
                    ]
                );

                $idx++;
                $totalImported++;
            }
        }

        // ---------------------------------------------------------------------
        // 4. BE YOUR OWN BOSS (BYOB) MODULES
        // ---------------------------------------------------------------------
        $byobSource = $sourceBase . DIRECTORY_SEPARATOR . 'Be Your Own Boss Modules';
        if (File::isDirectory($byobSource)) {
            $this->info("Importing Be Your Own Boss Modules...");
            $byobTarget = $targetBase . DIRECTORY_SEPARATOR . 'byob';
            if (!File::isDirectory($byobTarget)) {
                File::makeDirectory($byobTarget, 0755, true);
            }

            $files = File::files($byobSource);
            $idx = 1;
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) !== 'pdf') continue;

                $rawTitle = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $cleanTitle = 'Opportunity 2.0 - Be Your Own Boss (BYOB) Youth Handbook';
                $fileSize = $file->getSize();

                $safeName = Str::slug($rawTitle) . '.pdf';
                $destPath = $byobTarget . DIRECTORY_SEPARATOR . $safeName;
                File::copy($file->getRealPath(), $destPath);
                $relativeFilePath = "modules/secondary/byob/{$safeName}";

                $moduleNumber = sprintf('M-BYOB-JHS-%02d', $idx);

                Module::updateOrCreate(
                    [
                        'level' => Module::LEVEL_JUNIOR_HIGH,
                        'sub_level' => Module::SUB_LEVEL_JUNIOR_HIGH,
                        'language' => Module::LANGUAGE_ENGLISH,
                        'title' => $cleanTitle,
                    ],
                    [
                        'strand_id' => $ls4,
                        'module_number' => $moduleNumber,
                        'description' => "Opportunity 2.0 Be Your Own Boss (BYOB) Entrepreneurship & Youth Handbook.",
                        'pdf_file_path' => $relativeFilePath,
                        'pdf_file_size' => $fileSize,
                        'version' => '2.0',
                        'is_published' => true,
                        'uploaded_by' => $adminId,
                    ]
                );

                $idx++;
                $totalImported++;
            }
        }

        $this->info("Secondary module import completed successfully! Total modules imported/synced: {$totalImported}.");

        return Command::SUCCESS;
    }

    /**
     * Check if module title belongs to Math & Numeracy (LS3).
     */
    private function isMathTopic(string $title): bool
    {
        $t = strtolower($title);
        $mathKeywords = [
            'math', 'area', 'volume', 'angles', 'lines', 'scales', 'maps',
            'mass and weight', 'measuring weight', 'measurement', 'perimeter',
            'statistics', 'ratio', 'proportion', 'percentage', 'integers',
            'estimation', 'calendar', 'electric bills'
        ];

        foreach ($mathKeywords as $kw) {
            if (str_contains($t, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format UNESCO KOICA filenames into friendly, readable titles.
     */
    private function formatUnescoTitle(string $filename, string $strandName): string
    {
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

        // Case 1: "MODULE 1 I GET IT (RECOGNIZING THE MAIN IDEA)"
        if (preg_match('/^MOD(?:ULE|YUL)\s*([0-9]+)\s*(.+)$/i', $nameWithoutExt, $m)) {
            $num = $m[1];
            $topic = ucwords(strtolower(trim($m[2])));
            return "UNESCO JHS Module {$num}: {$topic}";
        }

        // Case 2: "UNESCO_ALS_LS1_ENGLISH_M02 (V1.1.1)"
        if (preg_match('/_M0?([0-9]+)/i', $nameWithoutExt, $m)) {
            $num = $m[1];
            return "UNESCO JHS {$strandName} - Module {$num}";
        }

        return trim(str_replace('_', ' ', $nameWithoutExt));
    }
}
