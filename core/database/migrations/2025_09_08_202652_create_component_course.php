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
        Schema::create('component_course', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('course');
            $table->unsignedInteger('component');
            $table->unsignedInteger('lesson_count');
            $table->unsignedInteger('module');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_course');
    }
};
