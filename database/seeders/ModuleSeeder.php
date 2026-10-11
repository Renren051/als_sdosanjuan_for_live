<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Focus exclusively on authentic DepEd ALS A&E Lower and Advanced Elementary modules.
     */
    public function run(): void
    {
        // 1. Remove old dummy placeholder PDF files in storage/app/public/modules/
        $dummyFiles = File::glob(storage_path('app/public/modules/*.pdf'));
        foreach ($dummyFiles as $file) {
            File::delete($file);
        }

        // 2. Remove legacy dummy modules in database
        $legacyDummyIds = Module::whereNull('sub_level')->pluck('id');
        if ($legacyDummyIds->isNotEmpty()) {
            ModuleProgress::whereIn('module_id', $legacyDummyIds)->delete();
            Module::whereIn('id', $legacyDummyIds)->forceDelete();
        }

        // 3. Import all authentic DepEd ALS Elementary modules (Lower & Advanced, English & Filipino)
        Artisan::call('app:import-elementary-modules');

        // 4. Import all authentic DepEd ALS Secondary / Junior High School modules
        Artisan::call('app:import-secondary-modules');

        // 5. Import all authentic DepEd ALS Basic Literacy Program modules
        Artisan::call('app:import-blp-modules');
    }
}
