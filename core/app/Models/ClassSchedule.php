<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Time\TimeSlots;

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
        'slot_id',
        'room',
        'course',
        'modality',
        'group',
        'lesson',
        'module',
        'time'
    ];

    public function timeSlots()
    {
        return $this->belongsTo(TimeSlots::class, 'slot_id');
    }

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

    public function sala()
    {
        return $this->belongsTo(Room::class, 'room');
    }
    
    public function dia()
    {
        return $this->belongsTo(\App\Models\Time\Day::class, 'day');
    }

    public function lessonTime()
    {
        return $this->belongsTo(\App\Models\Time\LessonTime::class, 'time');
    }
    
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

        // --- CORREÇÃO AQUI ---
        // Começa a consulta na tabela 'class_schedule' (self) com o alias 'T1'
        // Mude de self::query(...) para self::from(...)
        return self::from('class_schedule as T1')
            // --------------------
            
            // Liga (JOIN) à tabela 'room' (T2)
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