<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Time\LessonTime;
use Carbon\Carbon;
use App\Traits\Auditable;

class TimeDay extends Model
{
    use Auditable;
    protected $table = 'time_day';

    protected $fillable = ['time_id', 'day_id'];

    public function lessonTime()
    {
        return $this->belongsTo(LessonTime::class, 'time_id');
    }

    public static function getTimesByDay($dayId)
    {
        return self::with('lessonTime')
            ->where('day_id', $dayId)
            ->get()
            ->mapWithKeys(function ($timeShift) {
                $start = $timeShift->lessonTime
                    ? Carbon::parse($timeShift->lessonTime->start)->format('H:i')
                    : '';
                $end = $timeShift->lessonTime
                    ? Carbon::parse($timeShift->lessonTime->end)->format('H:i')
                    : '';
                return [$timeShift->time_id => "{$start} - {$end}"];
            })
            ->toArray();
    }
}
