<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';

    protected $fillable = ['shift_cod', 'course_id', 'modality_id', 'module_id'];
}
