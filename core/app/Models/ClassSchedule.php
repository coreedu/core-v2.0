<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    /**
     * O nome da tabela associada ao Model.
     */
    protected $table = 'class_schedule';

    protected $fillable = [
        'schedule_id',
        'instructor',
        'component',
        'shift',
        'day',
        'room',
        'course',
        'modality',
        'group',
        'lesson',
        'module',
        'time'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor');
    }

    public function component()
    {
        return $this->belongsTo(Componente::class, 'component');
    }

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