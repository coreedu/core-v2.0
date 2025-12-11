<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Equipment;
use App\Models\Room;

class GroupEquipment extends Model
{
    protected $fillable = [
        'name',
        'status',
        'patrimony',
        'maintenance_date',
        'room_id'
    ];

    public function equipments()
    {
        return $this->hasMany(
            \App\Models\Inventory\Equipment::class,
            'group_equipment_id'
        );
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
