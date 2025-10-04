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
        Schema::create('class_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('instructor');
            $table->unsignedInteger('component');
            $table->unsignedInteger('shift');
            $table->unsignedInteger('day');
            $table->unsignedInteger('room');
            $table->unsignedInteger('course');
            $table->unsignedInteger('modality');
            $table->char('group', 10);
            $table->unsignedInteger('lesson');
            $table->unsignedInteger('module');
            $table->unsignedInteger('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedule');
    }
};
