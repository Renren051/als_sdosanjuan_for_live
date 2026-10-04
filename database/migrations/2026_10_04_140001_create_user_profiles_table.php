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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('name_extension', 10)->nullable(); // Jr., Sr., III, etc.
            $table->date('birthdate');
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'solo_parent', 'other'])->default('single');
            $table->string('contact_number', 25);
            $table->string('facebook_name')->nullable();
            $table->string('street_address');
            $table->string('barangay'); // 21 San Juan barangays
            $table->string('city')->default('San Juan City');
            $table->string('province')->default('Metro Manila');
            $table->string('highest_education_attained'); // e.g. "Elementary Undergraduate", "Junior High Undergraduate"

            // Official DepEd ALS Form 2 Demographic Enrichment
            $table->string('mother_tongue')->default('Tagalog / Filipino');
            $table->boolean('is_ip')->default(false); // Indigenous People
            $table->string('ip_community_name')->nullable();
            $table->boolean('is_pwd')->default(false); // Person with Disability
            $table->string('pwd_disability_type')->nullable();
            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number', 25)->nullable();
            $table->string('profile_photo_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('barangay');
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
