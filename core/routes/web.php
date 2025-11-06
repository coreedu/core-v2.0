<?php

use Illuminate\Support\Facades\Route;
use App\Models\ClassSchedule; // Importe o Model "cola"
use Symfony\Component\Process\Process; // Importe o Process
use Symfony\Component\Process\Exception\ProcessFailedException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


// --- ROTA PRINCIPAL DE PREDIÇÃO (COM A CORREÇÃO DO SELECT) ---

Route::get('/run-prediction', function () {
    
    try {
        // 1. Coleta de Dados
        $aulas = ClassSchedule::query()
            // Carrega todos os dados relacionados
            ->with([
                'schedule',
                'schedule.course',
                'sala', // O método 'sala()' (antigo 'room()')
                'sala.equipments',
                'dia', // O método 'dia()' (antigo 'day()')
                'lessonTime'
            ])
            // --- A CORREÇÃO ESTÁ AQUI ---
            // Seleciona as colunas originais (para o ->with() funcionar)
            // E também os aliases (para o Python funcionar)
            ->select(
                'id as aula_id',
                'schedule_id',
                'created_at',
                
                'room',                 // Coluna original
                'room as room_id',      // Alias para o Python
                
                'day',                  // Coluna original
                'day as day_id',        // Alias para o Python
                
                'time',                 // Coluna original
                'time as lesson_time_id'  // Alias para o Python
            )
            // --------------------------
            ->get();
            
        // Se não houver dados, retorna um erro amigável
        if ($aulas->isEmpty()) {
            return response()->json(['error_laravel' => 'Nenhuma aula (class_schedule) encontrada com todos os dados. O banco de dados está vazio?']);
        }
            
        $jsonData = $aulas->toJson();

        // 2. Preparar o Ambiente e Comando Python
        $scriptPath = base_path('storage/app/python/predict_demand.py');
        
        // Define o ambiente, forçando UTF-8 e passando o PATH
        $env = [
            'PYTHONIOENCODING' => 'UTF-8',
            'PATH' => getenv('PATH') 
        ];

        // Tenta 'py' (Windows) e depois 'python' (Linux/Mac/WSL)
        $process = new Process(['py', $scriptPath], null, $env);
        
        try {
            $process->setInput($jsonData);
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            // Se 'py' falhar, tenta 'python'
            try {
                $process = new Process(['python', $scriptPath], null, $env);
                $process->setInput($jsonData);
                $process->mustRun();
            } catch (ProcessFailedException $e2) {
                // Se ambos falharem
                return response()->json([
                    'error_laravel' => 'Falha ao executar o script Python com "py" e "python". Verifique se o Python está instalado e no PATH do sistema.',
                    'error_py_stderr' => $e->getProcess()->getErrorOutput(),
                    'error_python_stderr' => $e2->getProcess()->getErrorOutput(),
                ], 500);
            }
        }

        // 3. Retornar o resultado do Python
        $output = $process->getOutput();
        
        // Verifica se o Python retornou algo
        if (empty($output)) {
            return response()->json([
                'error_laravel' => 'O script Python foi executado mas não retornou nenhuma saída (output).',
                'python_error_raw' => $process->getErrorOutput()
            ], 500);
        }

        $result = json_decode($output);
        
        // Verifica se o Python retornou um JSON válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'error_laravel' => 'O script Python retornou uma saída que não é um JSON válido (provavelmente um erro/warning do Python).',
                'python_output_raw' => $output,
                'python_error_raw' => $process->getErrorOutput()
            ], 500);
        }

        return $result;
        
    } catch (\Exception $e) {
        // Captura erros do Laravel (ex: coleta de dados)
        return response()->json([
            'error_laravel' => 'Um erro ocorreu no PHP antes de executar o Python.',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});