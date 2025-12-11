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
        Schema::create('equipment_room', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('group_equipment')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('room')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_room');
    }
};
