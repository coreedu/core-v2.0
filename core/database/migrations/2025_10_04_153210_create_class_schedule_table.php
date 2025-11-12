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
            $table->unsignedInteger('schedule_id')->nullable();
            $table->unsignedInteger('instructor')->nullable();
            $table->unsignedInteger('component')->nullable();
            $table->unsignedInteger('day');
            $table->unsignedInteger('room')->nullable();
            $table->char('group', 10)->nullable();
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
