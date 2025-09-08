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
        Schema::create('course', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shift');
            $table->string('name', 150);
            $table->char('abbreviation', 10);
            $table->unsignedInteger('count_modules');
            $table->unsignedInteger('modality')->nullable();
            $table->unsignedInteger('hours');
            $table->unsignedInteger('internshipHours')->nullable();
            $table->unsignedInteger('tgHours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course');
    }
};
