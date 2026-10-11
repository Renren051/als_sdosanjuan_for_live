<?php

namespace App\Console\Commands;

use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportElementaryModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:import-elementary-modules {--source= : Custom source directory path}';

    /**
     * The console command description.
     */
    protected $description = 'Import real DepEd ALS A&E Lower and Advanced Elementary PDF modules into storage and database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultSource = 'C:\\Users\\Renren\\Documents\\ALS SYSTEMS\\ALS Modules\\A&E Lower and Advanced Elementary';
        $sourceBase = $this->option('source') ?: $defaultSource;

        if (!File::isDirectory($sourceBase)) {
            $this->error("Source directory not found: {$sourceBase}");
            return Command::FAILURE;
        }

        $this->info("Found source directory: {$sourceBase}");

        // Preload strands
        $strands = LearningStrand::all()->keyBy('code');
        $ls1Eng = $strands->get('LS1_ENG')?->id ?? 1;
        $ls1Fil = $strands->get('LS1_FIL')?->id ?? 2;
        $ls2 = $strands->get('LS2')?->id ?? 3;
        $ls3 = $strands->get('LS3')?->id ?? 4;
        $ls4 = $strands->get('LS4')?->id ?? 5;
        $ls5 = $strands->get('LS5')?->id ?? 6;

        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        $targetBase = storage_path('app/public/modules/elementary');
        if (!File::isDirectory($targetBase)) {
            File::makeDirectory($targetBase, 0755, true);
        }

        $categories = [
            [
                'folder' => 'A&E Lower Elementary (English)',
                'sub_level' => Module::SUB_LEVEL_LOWER_ELEMENTARY,
                'language' => Module::LANGUAGE_ENGLISH,
                'prefix' => 'LE-EN',
                'dest_folder' => 'lower_elementary_english',
            ],
            [
                'folder' => 'A&E Lower Elementary (Filipino)',
                'sub_level' => Module::SUB_LEVEL_LOWER_ELEMENTARY,
                'language' => Module::LANGUAGE_FILIPINO,
                'prefix' => 'LE-FIL',
                'dest_folder' => 'lower_elementary_filipino',
            ],
            [
                'folder' => 'A&E Advanced Elementary (English)',
                'sub_level' => Module::SUB_LEVEL_ADVANCED_ELEMENTARY,
                'language' => Module::LANGUAGE_ENGLISH,
                'prefix' => 'AE-EN',
                'dest_folder' => 'advanced_elementary_english',
            ],
            [
                'folder' => 'A&E Advanced Elementary (Filipino)',
                'sub_level' => Module::SUB_LEVEL_ADVANCED_ELEMENTARY,
                'language' => Module::LANGUAGE_FILIPINO,
                'prefix' => 'AE-FIL',
                'dest_folder' => 'advanced_elementary_filipino',
            ],
        ];

        $totalImported = 0;

        foreach ($categories as $cat) {
            $catSourceDir = $sourceBase . DIRECTORY_SEPARATOR . $cat['folder'];
            if (!File::isDirectory($catSourceDir)) {
                $this->warn("Category folder not found: {$cat['folder']}");
                continue;
            }

            $catDestDir = $targetBase . DIRECTORY_SEPARATOR . $cat['dest_folder'];
            if (!File::isDirectory($catDestDir)) {
                File::makeDirectory($catDestDir, 0755, true);
            }

            $files = File::files($catSourceDir);
            $this->info("Processing {$cat['folder']}: found " . count($files) . " PDF files.");

            $index = 1;
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) !== 'pdf') {
                    continue;
                }

                $fileName = $file->getFilename();
                $cleanTitle = pathinfo($fileName, PATHINFO_FILENAME);
                $fileSize = $file->getSize();

                // Generate consistent safe destination filename
                $safeName = Str::slug($cleanTitle) . '.pdf';
                $destPath = $catDestDir . DIRECTORY_SEPARATOR . $safeName;

                // Copy file into storage
                File::copy($file->getRealPath(), $destPath);
                $relativeFilePath = "modules/elementary/{$cat['dest_folder']}/{$safeName}";

                // Determine Learning Strand
                $strandId = $this->determineStrand(
                    $cleanTitle,
                    $cat['language'],
                    $ls1Eng,
                    $ls1Fil,
                    $ls2,
                    $ls3,
                    $ls4,
                    $ls5
                );

                $moduleNumber = sprintf('M-%s-%02d', $cat['prefix'], $index);

                // Update or create Module record
                Module::updateOrCreate(
                    [
                        'level' => Module::LEVEL_ELEMENTARY,
                        'sub_level' => $cat['sub_level'],
                        'language' => $cat['language'],
                        'title' => $cleanTitle,
                    ],
                    [
                        'strand_id' => $strandId,
                        'module_number' => $moduleNumber,
                        'description' => "Official DepEd ALS {$cat['sub_level']} ({$cat['language']}) Self-Learning Module: {$cleanTitle}.",
                        'pdf_file_path' => $relativeFilePath,
                        'pdf_file_size' => $fileSize,
                        'version' => '1.0',
                        'is_published' => true,
                        'uploaded_by' => $adminId,
                    ]
                );

                $index++;
                $totalImported++;
            }
        }

        $this->info("Import completed successfully! Total elementary modules imported/synced: {$totalImported}.");

        return Command::SUCCESS;
    }

    /**
     * Classify learning strand based on title keywords and language.
     */
    private function determineStrand(
        string $title,
        string $language,
        int $ls1Eng,
        int $ls1Fil,
        int $ls2,
        int $ls3,
        int $ls4,
        int $ls5
    ): int {
        $t = strtolower($title);

        // LS3: Mathematics & Numeracy
        $mathKeywords = [
            'addition', 'subtraction', 'multiplication', 'division', 'fraction', 'fractions',
            'decimal', 'decimals', 'shape', 'shapes', 'heometriko', 'heometrikong',
            'percent', 'percentages', 'porsiyento', 'peresentahe', 'ratio', 'proportion',
            'proporsiyon', 'panumbasan', 'measuring', 'pagsukat', 'time', 'oras',
            'solving day to day problems', 'paglutas ng pang araw araw na suliranin',
            'pagdaragdag', 'pagbabawas', 'pagpaparami', 'paghahati', 'praksyon'
        ];
        foreach ($mathKeywords as $kw) {
            if (str_contains($t, $kw)) {
                return $ls3;
            }
        }

        // LS4: Life & Career Skills / Business
        $careerKeywords = [
            'business', 'negosyo', 'personnel change', 'personal na pagbabago',
            'planning your own business', 'pagpaplano ng iyong negosyo', 'succeed in business',
            'magtagumpay sa negosyo'
        ];
        foreach ($careerKeywords as $kw) {
            if (str_contains($t, $kw)) {
                return $ls4;
            }
        }

        // LS2: Scientific Literacy & Health
        $scienceKeywords = [
            'health', 'kalusugan', 'kaulusugan', 'nutrition', 'nutrisyon', 'food', 'pagkain',
            'first aid', 'lunas', 'medicine', 'gamot', 'breath', 'hininga', 'humihinga',
            'cardiovascular', 'germ', 'germs', 'mikrobyo', 'disease', 'diseases', 'karamdaman',
            'worm', 'worms', 'bulati', 'earth', 'yaman ng mundo', 'animal', 'animals', 'hayop',
            'plant', 'plants', 'halaman', 'environment', 'kapaligiran', 'clean up', 'linisin',
            'machine', 'machines', 'makina', 'electricity', 'elektrisidad', 'aquatic', 'yamang dagat',
            'soil', 'yamang lupa', 'typhoon', 'typhoons', 'bagyo', 'calamities', 'kalamidad',
            'temperature', 'temperatura', 'air pressure', 'presyon ng hangin', 'sense organ',
            'pandama', 'think green', 'stress'
        ];
        foreach ($scienceKeywords as $kw) {
            if (str_contains($t, $kw)) {
                return $ls2;
            }
        }

        // LS5: Understanding Self & Society / Culture / Ethics
        $societyKeywords = [
            'family', 'pamilya', 'heroes', 'bayani', 'religion', 'religions', 'relihiyon',
            'culture', 'cultural', 'kultura', 'symbol', 'symbols', 'sagisag', 'discipline',
            'disiplina', 'who am i', 'sino ako', 'where am i going', 'saan ako patungo',
            'different', 'naiiba', 'history', 'kasaysayan', 'filipinos', 'pilipino',
            'superstitious', 'pamahiin', 'song dances', 'awit sayaw', 'world according to the map',
            'mundo ayon sa mapa', 'we are connected', 'nakaugnay tayo', 'home', 'tahanan',
            'i need you', 'kailangan kita', 'in us', 'sa atin'
        ];
        foreach ($societyKeywords as $kw) {
            if (str_contains($t, $kw)) {
                return $ls5;
            }
        }

        // LS1: Communication Skills
        if ($language === Module::LANGUAGE_FILIPINO) {
            return $ls1Fil;
        }

        return $ls1Eng;
    }
}
