<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $fillable = [
        'nome',
        'abreviacao',
        'horasSemanais',
        'horasTotais',
    ];

    public function users()
    {
        return $this->belongsToMany(Users::class, 'component_instructor', 'component', 'instructor');
    }
}