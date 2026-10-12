<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_game_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('attempt_uuid');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('game_type', 50)->default('memory_match');
            $table->unsignedSmallInteger('score');
            $table->unsignedSmallInteger('max_score');
            $table->unsignedSmallInteger('moves');
            $table->unsignedInteger('elapsed_seconds');
            $table->string('device_identifier', 150)->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->unique(['user_id', 'attempt_uuid']);
            $table->index(['user_id', 'attempted_at']);
            $table->index(['module_id', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_game_attempts');
    }
};
