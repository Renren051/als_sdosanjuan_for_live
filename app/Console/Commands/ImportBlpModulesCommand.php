<?php

namespace App\Console\Commands;

use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportBlpModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:import-blp-modules {--source= : Custom source directory path}';

    /**
     * The console command description.
     */
    protected $description = 'Import authentic DepEd ALS Basic Literacy Program (BLP) PDF modules into storage and database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultSource = 'C:\\Users\\Renren\\Documents\\ALS SYSTEMS\\ALS Modules\\Basic Literacy Program';
        $sourceBase = $this->option('source') ?: $defaultSource;

        if (!File::isDirectory($sourceBase)) {
            $this->error("Source directory not found: {$sourceBase}");
            return Command::FAILURE;
        }

        $this->info("Found Basic Literacy Program source directory: {$sourceBase}");

        // Preload learning strands
        $strands = LearningStrand::all()->keyBy('code');
        $ls1Fil = $strands->get('LS1_FIL')?->id ?? 2;
        $ls2 = $strands->get('LS2')?->id ?? 3;
        $ls5 = $strands->get('LS5')?->id ?? 6;

        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        $targetBase = storage_path('app/public/modules/blp');
        if (!File::isDirectory($targetBase)) {
            File::makeDirectory($targetBase, 0755, true);
        }

        $blpStrandConfigs = [
            'BLP LS1_ Communication Skills' => [
                'strand_id' => $ls1Fil,
                'sub_dir' => 'ls1',
                'prefix' => 'BLP-LS1',
                'name' => 'Kasanayang Pangkomunikasyon (Filipino)',
            ],
            'BLP LS2_ Problem-solving and Critical Thinking' => [
                'strand_id' => $ls2,
                'sub_dir' => 'ls2',
                'prefix' => 'BLP-LS2',
                'name' => 'Paglutas ng Suliranin at Mapanuring Pag-iisip',
            ],
            'BLP LS5_ Understanding the Self and Society' => [
                'strand_id' => $ls5,
                'sub_dir' => 'ls5',
                'prefix' => 'BLP-LS5',
                'name' => 'Pag-unawa sa Sarili at Lipunan',
            ],
        ];

        $totalImported = 0;

        foreach ($blpStrandConfigs as $folderName => $cfg) {
            $strandSourceDir = $sourceBase . DIRECTORY_SEPARATOR . $folderName;
            if (!File::isDirectory($strandSourceDir)) {
                $this->warn("Strand folder not found: {$folderName}");
                continue;
            }

            $strandTargetDir = $targetBase . DIRECTORY_SEPARATOR . $cfg['sub_dir'];
            if (!File::isDirectory($strandTargetDir)) {
                File::makeDirectory($strandTargetDir, 0755, true);
            }

            $topicDirs = File::directories($strandSourceDir);
            $idx = 1;

            foreach ($topicDirs as $topicDir) {
                $rawTopicName = basename($topicDir);
                $cleanTopic = str_replace('_', ' ', $rawTopicName);
                $cleanTopic = preg_replace('/\s+/', ' ', trim($cleanTopic));

                $files = File::files($topicDir);
                $lessonFiles = [];

                foreach ($files as $file) {
                    $fn = $file->getFilename();
                    // Match learner lessons only: Aralin*.pdf or Insides.pdf
                    if (preg_match('/^(Aralin.*|Insides)\.pdf$/i', $fn)) {
                        $lessonFiles[] = $file;
                    }
                }

                // Natural sort lessons (Aralin 1, Aralin 2, ... Aralin 10)
                usort($lessonFiles, function ($a, $b) {
                    return strnatcasecmp($a->getFilename(), $b->getFilename());
                });

                foreach ($lessonFiles as $file) {
                    $lessonFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $fileSize = $file->getSize();

                    // Format clean title
                    if (strtolower($lessonFilename) === 'insides') {
                        $cleanTitle = "{$cleanTopic}";
                    } elseif (strtolower($lessonFilename) === 'aralin') {
                        $cleanTitle = "{$cleanTopic} - Aralin";
                    } else {
                        $cleanTitle = "{$cleanTopic} - {$lessonFilename}";
                    }

                    // Safe storage file name
                    $safeName = Str::slug("{$cleanTopic}-{$lessonFilename}") . '.pdf';
                    $destPath = $strandTargetDir . DIRECTORY_SEPARATOR . $safeName;
                    File::copy($file->getRealPath(), $destPath);
                    $relativeFilePath = "modules/blp/{$cfg['sub_dir']}/{$safeName}";

                    $moduleNumber = sprintf('M-%s-%02d', $cfg['prefix'], $idx);

                    Module::updateOrCreate(
                        [
                            'level' => Module::LEVEL_BLP,
                            'sub_level' => Module::SUB_LEVEL_BLP,
                            'language' => Module::LANGUAGE_FILIPINO,
                            'title' => $cleanTitle,
                        ],
                        [
                            'strand_id' => $cfg['strand_id'],
                            'module_number' => $moduleNumber,
                            'description' => "DepEd ALS Basic Literacy Program (BLP) Modyul: {$cleanTitle}.",
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

            $this->info("Completed {$folderName}: imported " . ($idx - 1) . " lesson modules.");
        }

        $this->info("Basic Literacy Program import completed successfully! Total modules imported/synced: {$totalImported}.");

        return Command::SUCCESS;
    }
}
