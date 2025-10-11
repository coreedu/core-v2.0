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
        return $this->belongsToMany(User::class, 'component_instructor', 'component', 'instructor');
    }

    public function cursos()
    {
        return $this->belongsToMany(\App\Models\Curso::class, 'component_course', 'component', 'course')
            ->withPivot('module')
            ->withTimestamps();
    }
}
