<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';

    // Campos que podem ser preenchidos em mass-assignment
    protected $fillable = ['name', 'description']; 
}
