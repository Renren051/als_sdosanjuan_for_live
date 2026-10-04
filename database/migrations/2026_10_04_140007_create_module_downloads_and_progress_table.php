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
        Schema::create('module_downloads_and_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('last_opened_at')->nullable();
            $table->enum('completion_status', ['downloaded', 'in_progress', 'completed'])->default('downloaded');
            $table->string('device_identifier')->nullable(); // Mobile phone UUID or hardware identifier
            $table->unsignedInteger('last_page_read')->default(1);
            $table->unsignedInteger('total_pages')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'module_id']);
            $table->index('completion_status');
            $table->index('synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_downloads_and_progress');
    }
};
