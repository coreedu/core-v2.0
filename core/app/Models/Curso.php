<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'turno',
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

    public function shift()
    {
        return $this->belongsTo(\App\Models\Time\Shift::class, 'turno', 'cod');
    }
}
