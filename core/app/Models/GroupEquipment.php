<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupEquipment extends Model
{
    protected $fillable = [
        'name',
        'status',
        'patrimony',
        'maintenance_date'
    ];

    public function equipments()
    {
        return $this->hasMany(
            \App\Models\Inventory\Equipment::class,
            'group_equipment_id'
        );
    }
}
