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
        Schema::create('learner_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->onDelete('cascade');
            $table->enum('document_type', [
                'psa_birth_cert',
                'barangay_clearance',
                'form_137_sf10',
                'photo_id',
                'portfolio_document'
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('mime_type', 100);
            $table->enum('verification_status', [
                'pending',
                'verified',
                'rejected'
            ])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'document_type']);
            $table->index('verification_status');
            $table->index('enrollment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_documents');
    }
};
