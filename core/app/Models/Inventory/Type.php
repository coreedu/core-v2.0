<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Type extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'types';

    protected $fillable = [
        'name',
        'requires_asset_tag',
    ];

    protected function casts(): array
    {
        return [
            'requires_asset_tag' => 'boolean',
        ];
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
