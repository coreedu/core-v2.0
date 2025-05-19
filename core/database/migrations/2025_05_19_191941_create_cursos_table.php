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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('turno');
            $table->string('nome', 150);
            $table->char('abreviacao', 10);
            $table->unsignedInteger('qtdModulos');
            $table->unsignedInteger('modalidade');
            $table->unsignedInteger('horas');
            $table->unsignedInteger('horasEstagio')->nullable();
            $table->unsignedInteger('horasTg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
