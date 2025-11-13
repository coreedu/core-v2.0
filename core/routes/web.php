<?php

use Illuminate\Support\Facades\Route;
use App\Models\ClassSchedule;
use Symfony\Component\Process\Process; // Importe o Process
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Filament\Resources\ScheduleResource;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $schedule = ScheduleResource::getPublished();

    return view('welcome', [
        'schedule' => $schedule,
    ]);
});


// --- ROTA DE EXECUÇÃO DO PYTHON (COM CORREÇÃO DE TIMEOUT) ---

// Route::get('/run-prediction', function () {
    
//     // --- CORREÇÃO DO TIMEOUT ---
//     @set_time_limit(180);
//     // ---------------------------------

//     try {
//         // 1. Coleta de Dados
//         $aulas = ClassSchedule::query()
//             ->with([
//                 'schedule',
//                 'schedule.course',
//                 'sala', 
//                 'sala.equipments',
//                 'dia', 
//                 'lessonTime'
//             ])
//             ->select(
//                 'id as aula_id',
//                 'schedule_id',
//                 'created_at',
//                 'room',
//                 'room as room_id',
//                 'day',
//                 'day as day_id',
//                 'time',
//                 'time as lesson_time_id'
//             )
//             ->get();
            
//         if ($aulas->isEmpty()) {
//             return response(json_encode(['error_laravel' => 'Nenhuma aula (class_schedule) encontrada. O banco de dados está vazio?']), 500)
//                   ->header('Content-Type', 'application/json');
//         }
            
//         $jsonData = $aulas->toJson();

//         // 2. Preparar o Ambiente e Comando Python
//         $scriptPath = base_path('storage/app/python/predict_demand.py');
        
//         $env = [
//             'PYTHONIOENCODING' => 'UTF-8',
//             'LANG' => 'en_US.UTF-8',
//         ];

//         $process = new Process(['py', $scriptPath], base_path(), $env);
        
//         try {
//             $process->setInput($jsonData);
//             $process->mustRun();
//         } catch (ProcessFailedException $e) {
//             try {
//                 $process = new Process(['python', $scriptPath], base_path(), $env);
//                 $process->setInput($jsonData);
//                 $process->mustRun();
//             } catch (ProcessFailedException $e2) {
//                 return response()->json([
//                     'error_laravel' => 'Falha ao executar o script Python com "py" e "python".',
//                     'error_py_stderr' => $e->getProcess()->getErrorOutput(),
//                     'error_python_stderr' => $e2->getProcess()->getErrorOutput(),
//                 ], 500);
//             }
//         }

//         // 3. Retornar o resultado do Python
//         $output = $process->getOutput();
        
//         if (empty($output)) {
//             return response()->json([
//                 'error_laravel' => 'O script Python foi executado mas não retornou nenhuma saída (output).',
//                 'python_error_raw' => $process->getErrorOutput()
//             ], 500);
//         }

//         return response($output)->header('Content-Type', 'application/json');
        
//     } catch (\Exception $e) {
//         return response()->json([
//             'error_laravel' => 'Um erro ocorreu no PHP antes de executar o Python.',
//             'message' => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//         ], 500);
//     }
// })->name('run-prediction'); 