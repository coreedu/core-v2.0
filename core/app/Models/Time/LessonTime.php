<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class LessonTime extends Model
{
    protected $table = 'lesson_time';

    protected $fillable = ['start', 'end'];

    // app/Models/Time/LessonTime.php
    public function shift()
    {
        return $this->belongsToMany(
            \App\Models\Time\Shift::class,
            'time_shift',
            'lesson_time_id',
            'shift_cod',
            'id',
            'cod'
        );
    }
}
