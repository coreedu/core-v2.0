<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class LessonTime extends Model
{
    protected $table = 'lesson_time';

    protected $fillable = ['start', 'end'];
}
