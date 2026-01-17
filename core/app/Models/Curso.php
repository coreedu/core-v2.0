<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nome',
        'abreviacao',
        'qtdModulos',
        'modalidade',
        'horas',
        'horasEstagio',
        'horasTg',
    ];

    public function modality()
    {
        return $this->belongsTo(\App\Models\Modality::class, 'modalidade');
    }

    public function componentes()
    {
        return $this->belongsToMany(\App\Models\Componente::class, 'component_course', 'course', 'component')
            ->withPivot('module')
            ->withTimestamps();
    }
    
    public function getComponentsByModule($moduleId)
    {

        return $this->componentes()
            ->wherePivot('module', $moduleId)
            ->with('users:id,name') 
            ->get(['componentes.id', 'componentes.nome'])
            ->mapWithKeys(function ($component) {
                return [
                    $component->id => [
                        'name' => $component->nome,
                        'instructors' => $component->users
                            ->mapWithKeys(function ($user) {
                                return [
                                    $user->id => $user->name,
                                ];
                            })
                            ->toArray(),
                    ],
                ];
            })
            ->toArray();
    }
}
