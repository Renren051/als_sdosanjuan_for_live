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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('calendar_year', 10)->default('2026');
            $table->enum('program_track', ['BLP', 'A&E_Elementary', 'A&E_Junior_High']);
            $table->foreignId('clc_id')->constrained('community_learning_centers')->onDelete('cascade');
            $table->foreignId('assigned_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('enrollment_status', [
                'pending',
                'under_review',
                'enrolled',
                'needs_correction',
                'withdrawn',
                'completed'
            ])->default('pending');
            $table->enum('delivery_mode', [
                'modular_offline',
                'modular_print',
                'face_to_face',
                'blended'
            ])->default('modular_offline');
            $table->enum('schedule_preference', [
                'weekend',
                'weekday_am',
                'weekday_pm',
                'flexible'
            ])->default('flexible');
            $table->text('remarks')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'calendar_year']);
            $table->index(['clc_id', 'enrollment_status']);
            $table->index('enrollment_status');
            $table->index('calendar_year');
            $table->index('program_track');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
