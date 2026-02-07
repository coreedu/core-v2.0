<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class Componente extends Model
{

    use HasFactory;
    use Auditable;

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
