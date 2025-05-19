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
}
