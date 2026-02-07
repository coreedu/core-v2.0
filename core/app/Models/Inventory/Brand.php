<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Brand extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'brands';

    protected $fillable = [
        'name',
    ];

    public function types()
    {
        return $this->hasMany(Type::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}