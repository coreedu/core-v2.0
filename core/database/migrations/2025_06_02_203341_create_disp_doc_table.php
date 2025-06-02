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
        Schema::create('disp_doc', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('codHorario');
            $table->unsignedInteger('usuario');
            $table->unsignedInteger('codDia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disp_doc');
    }
};
