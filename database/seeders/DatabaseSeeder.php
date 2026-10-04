<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LearningStrandSeeder::class,
            UserSeeder::class,
            SanJuanClcSeeder::class,
            ModuleSeeder::class,
            EnrollmentSeeder::class,
        ]);
    }
}
