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
        'brand',
        'type',
        'patrimony',
        'status',
        'observation',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];
}