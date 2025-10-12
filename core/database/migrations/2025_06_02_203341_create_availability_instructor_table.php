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
        Schema::create('availability_instructor', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('time_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('day_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('day_id')->references('cod')->on('day')->onDelete('cascade');
            $table->foreign('time_id')->references('id')->on('lesson_time')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_instructor');
    }
};
