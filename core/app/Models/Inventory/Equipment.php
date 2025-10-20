<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'brand_id',
        'type_id',
        'patrimony',
        'status',
        'observation',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Models\Room::class, 'equipment_room', 'equipment_id', 'room_id')
            ->withTimestamps();
    }
}
