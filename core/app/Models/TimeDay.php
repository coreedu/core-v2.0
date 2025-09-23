<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeDay extends Model
{
    protected $table = 'time_day';

    protected $fillable = ['time_id', 'day_id'];
}
