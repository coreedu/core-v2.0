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

    public function equipments()
    {
        return $this->belongsToMany(\App\Models\Inventory\Equipment::class, 'equipment_room', 'room_id', 'equipment_id')
            ->withTimestamps();
    }
}
