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
        Schema::create('learning_strands', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // LS1_ENG, LS1_FIL, LS2, LS3, LS4, LS5, LS6
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color_code', 30)->default('#2563eb');
            $table->string('icon_name', 50)->default('book-open');
            $table->unsignedInteger('order_seq')->default(1);
            $table->timestamps();

            $table->index('code');
            $table->index('order_seq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_strands');
    }
};
