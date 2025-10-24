<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    /**
     * O nome da tabela associada ao Model.
     */
    protected $table = 'class_schedule';

    /**
     * Define o relacionamento: Uma "aula" (ClassSchedule) pertence a uma "sala" (Room).
     */
    public function room()
    {
        // Usa a coluna 'room' que identificamos na sua tabela
        return $this->belongsTo(Room::class, 'room');
    }

    // (Opcional) Adicione outros relacionamentos se precisar
    // public function professor() {
    //    return $this->belongsTo(User::class, 'instructor');
    // }
}