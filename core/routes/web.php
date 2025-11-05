<?php

use Illuminate\Support\Facades\Route;
use App\Models\ClassSchedule; // Importe o Model "cola"
use Illuminate\Http\Request;
use Symfony\Component\Process\Process; 

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

// --- ROTA DE COLETA DE DADOS (PARA DEPURAR) ---
Route::get('/prediction-data', function () {
    $aulas = ClassSchedule::query()
        ->with([
            'schedule', 'schedule.course', 'sala', 'sala.equipments', 'dia', 'lessonTime'
        ])
        ->select(
            'id as aula_id',
            'schedule_id',
            'created_at',
            'room', // <-- Coluna original
            'day',  // <-- Coluna original
            'time'  // <-- Coluna original
        )
        ->get();
    return response()->json($aulas);
});


// --- ROTA DE EXECUÇÃO DO PYTHON (COM CORREÇÃO DE ALIAS) ---
Route::get('/run-prediction', function () {

    // 1. Coleta os dados
    $aulas = ClassSchedule::query()
        ->with([
            'schedule', 'schedule.course', 'sala', 'sala.equipments', 'dia', 'lessonTime'
        ])
        // --- CORREÇÃO AQUI ---
        // Renomeamos as colunas para bater com o que o Python espera
        ->select(
            'id as aula_id',
            'schedule_id',
            'created_at',
            'room as room_id',      // <-- Renomeado
            'day as day_id',        // <-- Renomeado
            'time as lesson_time_id' // <-- Renomeado
        )
        // ---------------------
        ->get();
        
    $jsonData = $aulas->toJson();

    // 2. Define o caminho para o script Python
    $scriptPath = base_path('storage/app/python/predict_demand.py');

    // 3. Define o comando e o ambiente
    // (Usando 'py' para Windows e 'PYTHONIOENCODING' para forçar UTF-8)
    $command = ['py', $scriptPath]; 
    $env = [
        'PYTHONIOENCODING' => 'UTF-8',
        'LANG' => 'en_US.UTF-8',
    ];

    $process = new Process($command, base_path(), $env);

    // 4. Passa o JSON como "entrada" (stdin)
    $process->setInput($jsonData);

    try {
        // 5. Executa o script
        $process->mustRun();
        $outputJson = $process->getOutput();
        
        // 7. Retorna o resultado do Python
        return response($outputJson)->header('Content-Type', 'application/json');

    } catch (\Exception $e) {
        // 8. Se o Python falhar, mostra o erro
        return response()->json([
            'error_laravel' => 'Falha ao executar o script Python.',
            'error_python_output' => $process->getOutput(),
            'error_python_stderr' => $process->getErrorOutput()
        ], 500);
    }
});