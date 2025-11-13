<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use App\Models\Time\LessonTime;

class Day extends Model
{
    protected $table = 'day';

    protected $fillable = ['cod', 'name'];

    public function times()
    {
        return $this->belongsToMany(LessonTime::class, 'time_day', 'day_id', 'time_id')->orderBy('start');
    }

    public static function getWeekDaysSchedule(array $lessonTimeIds)
    {
        return self::whereNotIn('cod', [1, 7])
            ->whereHas('times', function ($query) use ($lessonTimeIds) {
                    $query->whereIn('lesson_time.id', $lessonTimeIds);
                })
            ->pluck('name', 'cod')
            ->toArray();
    }

    public static function getWeekendDays()
    {
        return self::whereIn('cod', [1, 7])
            ->pluck('name', 'cod')
            ->toArray();
    }
}
