<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Equipment;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Type;
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

    public function equipmentsCount(): int
    {
        return $this->equipments()->count();
    }

    public function getEquipments()
    {
        return $this->equipments()
            ->with(['brand', 'type']) // já carrega dependências
            ->orderBy('name')
            ->get();
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
