<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class TimeSlots extends Model
{
    protected $table = 'time_slots';

    protected $fillable = ['time_config_id','lesson_time_id','day_id'];
}
