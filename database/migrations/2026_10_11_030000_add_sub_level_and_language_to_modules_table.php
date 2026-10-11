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
        Schema::table('modules', function (Blueprint $table) {
            $table->string('sub_level', 50)->nullable()->after('level'); // e.g. "Lower Elementary", "Advanced Elementary"
            $table->string('language', 20)->default('English')->after('sub_level'); // "English", "Filipino"

            $table->index(['level', 'sub_level', 'language']);
            $table->index('sub_level');
            $table->index('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropIndex(['level', 'sub_level', 'language']);
            $table->dropIndex(['sub_level']);
            $table->dropIndex(['language']);
            $table->dropColumn(['sub_level', 'language']);
        });
    }
};
