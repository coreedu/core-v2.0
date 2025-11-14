<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    
    public static function getUsageRankingByRoom()
    {
        // T1 = class_schedule (A aula, o "Fato")
        // T2 = room (A sala, a "Dimensão")

        // Começa a consulta na tabela 'class_schedule' (self)
        return self::query('class_schedule as T1')
            
            // Liga (JOIN) à tabela 'room' (T2)
            // A nossa "cola" é a coluna 'room' (T1.room)
            ->join('room as T2', 'T1.room', '=', 'T2.id')
            
            // Seleciona o nome da sala (T2.name)
            // e a contagem de aulas (COUNT(T1.id))
            ->select('T2.name as room_name', DB::raw('COUNT(T1.id) as usage_count'))
            
            // Agrupa os resultados pelo nome da sala
            ->groupBy('T2.name')
            
            // Ordena do mais usado para o menos usado
            ->orderBy('usage_count', 'desc')
            
            // Pega apenas os 10 primeiros do ranking
            ->take(10)
            
            // Executa a consulta
            ->get();
    }

}