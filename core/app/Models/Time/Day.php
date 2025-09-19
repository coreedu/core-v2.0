<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $table = 'day';

    protected $fillable = ['cod', 'name'];
}
