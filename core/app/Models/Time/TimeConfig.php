<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class TimeConfig extends Model
{
    protected $table = 'time_config';

    protected $fillable = ['context_id', 'shift_id'];
}
