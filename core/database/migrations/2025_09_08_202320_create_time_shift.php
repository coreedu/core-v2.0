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
        Schema::create('time_shift', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lesson_time_id')
                ->references('id')
                ->on('lesson_time');
            $table->unsignedInteger('shift_cod')
                ->references('cod')
                ->on('shift');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_shift');
    }
};
