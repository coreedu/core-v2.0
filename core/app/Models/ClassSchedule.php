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
     * Define o relacionamento: Uma "aula" pertence a uma "sala".
     * Renomeamos de 'room' para 'sala' para evitar conflito com a COLUNA 'room'.
     */
    public function sala()
    {
        // O método 'sala()' ainda usa a COLUNA 'room' para fazer a ligação.
        return $this->belongsTo(Room::class, 'room');
    }
    
    /**
     * Liga esta aula ao Dia.
     * Renomeamos de 'day' para 'dia' para evitar conflito com a COLUNA 'day'.
     */
    public function dia()
    {
        // O método 'dia()' ainda usa a COLUNA 'day' para fazer a ligação.
        return $this->belongsTo(\App\Models\Time\Day::class, 'day');
    }
    // --- FIM DA CORREÇÃO ---

    public function lessonTime()
    {
        // (Este não tem conflito: método 'lessonTime()' usa a coluna 'time')
        return $this->belongsTo(\App\Models\Time\LessonTime::class, 'time');
    }
    
    // (Deixei os outros relacionamentos que também tinham conflito)
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'course');
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class, 'modality');
    }

}