<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Time\LessonTime;
use Carbon\Carbon;

class TimeShift extends Model
{
    protected $table = 'time_shift';

    protected $fillable = ['lesson_time_id', 'shift_cod'];

    public function lessonTime()
    {
        return $this->belongsTo(LessonTime::class, 'lesson_time_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_cod', 'cod');
    }

    public static function getTimesByShift($shiftCod)
    {
        return self::with('lessonTime')
            ->where('shift_cod', 'like', "%{$shiftCod}%")
            ->get()
            ->mapWithKeys(function ($timeShift) {
                $start = $timeShift->lessonTime
                    ? Carbon::parse($timeShift->lessonTime->start)->format('H:i')
                    : '';
                $end = $timeShift->lessonTime
                    ? Carbon::parse($timeShift->lessonTime->end)->format('H:i')
                    : '';
                return [$timeShift->id => "{$start} - {$end}"];
            })
            ->toArray();
    }
    
    public static function getTimesMap()
    {
        return self::with('lessonTime')
            ->get()
            ->groupBy('shift_cod') // 1. Agrupa os registros pelo código do turno (ex: 1, 2, 3...)
            ->map(function ($timeShifts) { 
                
                return $timeShifts->mapWithKeys(function ($timeShift) {
                    
                    $lessonTime = $timeShift->lessonTime;
                    
                    $start = optional($lessonTime)->start
                        ? Carbon::parse($lessonTime->start)->format('H:i')
                        : '';
                        
                    $end = optional($lessonTime)->end
                        ? Carbon::parse($lessonTime->end)->format('H:i')
                        : '';

                    return [$timeShift->lesson_time_id => "{$start} - {$end}"];
                });
            })
            ->toArray();
    }

    public static function getCurrentShiftCodByHour($time)
    {

        $current = self::join('lesson_time', 'lesson_time.id', '=', 'time_shift.lesson_time_id')
            ->where('lesson_time.start', '<=', $time)
            ->where('lesson_time.end', '>=', $time)
            ->select('time_shift.shift_cod')
            ->first();

        if (!$current) {
            $next = self::join('lesson_time', 'lesson_time.id', '=', 'time_shift.lesson_time_id')
                ->where('lesson_time.start', '>', $time)
                ->orderBy('lesson_time.start')
                ->select('time_shift.shift_cod')
                ->first();

            $previous = self::join('lesson_time', 'lesson_time.id', '=', 'time_shift.lesson_time_id')
                ->where('lesson_time.end', '<', $time)
                ->orderByDesc('lesson_time.end')
                ->select('time_shift.shift_cod')
                ->first();

            return $next->shift_cod ?? $previous->shift_cod ?? null;
        }

        return (int) substr($current->shift_cod, 0, 1);
    }
    
    public static function getShiftCodById($time)
    {

        $shift = self::where('lesson_time_id', $time)->first();

        return (int) substr($shift->shift_cod, 0, 1);
    }
}
