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

    public static function getCurrentShiftCod()
    {
        $now = Carbon::now()->format('H:i');

        $current = self::join('lesson_time', 'lesson_time.id', '=', 'time_shift.lesson_time_id')
            ->where('lesson_time.start', '<=', $now)
            ->where('lesson_time.end', '>=', $now)
            ->select('time_shift.shift_cod')
            ->first();

        if (!$current) {
            $next = self::join('lesson_time', 'lesson_time.id', '=', 'time_shift.lesson_time_id')
                ->where('lesson_time.start', '>', $now)
                ->orderBy('lesson_time.start')
                ->select('time_shift.shift_cod')
                ->first();

            $previous = self::join('lesson_time', 'lesson_time.id', '=', 'time_shift.lesson_time_id')
                ->where('lesson_time.end', '<', $now)
                ->orderByDesc('lesson_time.end')
                ->select('time_shift.shift_cod')
                ->first();

            return $next->shift_cod ?? $previous->shift_cod ?? null;
        }

        return (int) substr($current->shift_cod, 0, 1);
    }
}
