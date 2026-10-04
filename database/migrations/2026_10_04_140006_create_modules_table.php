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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('strand_id')->constrained('learning_strands')->onDelete('cascade');
            $table->enum('level', ['BLP', 'Elementary', 'Junior_High']);
            $table->string('module_number', 50); // e.g. "Module 1", "M01"
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('pdf_file_path');
            $table->unsignedBigInteger('pdf_file_size')->default(0); // In bytes
            $table->string('version', 20)->default('1.0');
            $table->boolean('is_published')->default(true);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['strand_id', 'level']);
            $table->index(['level', 'is_published']);
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
