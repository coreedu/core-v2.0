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
}
