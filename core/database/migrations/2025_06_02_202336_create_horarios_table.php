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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('docente');
            $table->unsignedInteger('componente');
            $table->unsignedInteger('turno');
            $table->unsignedInteger('diaSemana');
            $table->unsignedInteger('sala');
            $table->unsignedInteger('curso');
            $table->unsignedInteger('modalidade');
            $table->char('turma', 10);
            $table->unsignedInteger('aula');
            $table->unsignedInteger('modulo');
            $table->unsignedInteger('codHorario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
