<?php

use Illuminate\Support\Facades\Route;
use App\Models\ClassSchedule; // Importe o Model "cola"
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prediction-data', function () {

    // 1. Busca todas as aulas
    $aulas = ClassSchedule::query()
        // 2. Carrega todos os dados relacionados (sem filtrar colunas)
        ->with([
            // Removemos os filtros de colunas (ex: ':id,nome')
            // para trazer o objeto completo.
            'schedule',
            'schedule.course',
            'sala',
            'sala.equipments',
            'dia',
            'lessonTime'
            // --- FIM DA CORREÇÃO ---
        ])
        // 3. Seleciona as colunas principais da tabela 'class_schedule'
        // (Esta parte continua igual, como você pediu)
        ->select(
            'id as aula_id',
            'schedule_id',
            'created_at',
            'room', 
            'day',
            'time'
        )
        // ->limit(100)
        ->get();

    // 4. Retorna como JSON (o que o Python vai receber)
    return response()->json($aulas);
});