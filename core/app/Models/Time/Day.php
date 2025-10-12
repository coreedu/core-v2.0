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
}
