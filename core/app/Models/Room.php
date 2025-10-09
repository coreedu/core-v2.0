<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';

    protected $fillable = [
        'name',
        'number',
        'img',
        'type',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'type');
    }
}
