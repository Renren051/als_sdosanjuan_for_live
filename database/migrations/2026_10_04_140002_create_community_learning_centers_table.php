<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_learning_centers', function (Blueprint $table) {
            $table->id();
            $table->string('clc_name');
            $table->string('clc_code', 30)->unique();
            $table->string('clc_type')->default('Community Center'); // e.g. Barangay Hall, Public School, Church, BJMP Facility
            $table->string('barangay'); // San Juan barangay
            $table->text('physical_location_notes')->nullable();
            $table->foreignId('assigned_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('barangay');
            $table->index('assigned_teacher_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_learning_centers');
    }
};
