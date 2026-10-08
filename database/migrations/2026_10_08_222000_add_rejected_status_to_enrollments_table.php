<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN enrollment_status ENUM('pending','under_review','enrolled','needs_correction','rejected','withdrawn','completed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN enrollment_status ENUM('pending','under_review','enrolled','needs_correction','withdrawn','completed') NOT NULL DEFAULT 'pending'");
    }
};
