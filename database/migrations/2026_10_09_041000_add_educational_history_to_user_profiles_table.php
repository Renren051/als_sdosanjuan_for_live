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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('dropout_reason')->nullable()->after('highest_education_attained');
            $table->boolean('has_attended_als')->default(false)->after('dropout_reason');
            $table->string('previous_als_program')->nullable()->after('has_attended_als');
            $table->string('previous_als_year', 20)->nullable()->after('previous_als_program');
            $table->boolean('previous_als_completed')->nullable()->after('previous_als_year');
            $table->string('previous_als_incomplete_reason')->nullable()->after('previous_als_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'dropout_reason',
                'has_attended_als',
                'previous_als_program',
                'previous_als_year',
                'previous_als_completed',
                'previous_als_incomplete_reason',
            ]);
        });
    }
};
