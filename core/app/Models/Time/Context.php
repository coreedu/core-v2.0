<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class Context extends Model
{
    protected $table = 'context';

    protected $fillable = ['name'];
}
