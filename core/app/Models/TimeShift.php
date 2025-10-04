<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeShift extends Model
{
    protected $table = 'time_shift';

    protected $fillable = ['lesson_time_id', 'shift_cod'];
}
