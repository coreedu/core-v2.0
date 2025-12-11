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
}
