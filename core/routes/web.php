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
|
| Aqui pode registar as rotas web para a sua aplicação.
|
*/

Route::get('/', function () {
    $schedule = ScheduleResource::getPublished();

    return view('welcome', [
        'schedule' => $schedule,
    ]);
});


// A rota '/run-prediction' foi removida.
//
// O seu widget (PredictiveRankingChart) agora contém toda a lógica
// de recolha de dados e execução do Python.
//
// Isto foi feito para corrigir o erro de "deadlock" (ConnectionException)
// que acontece quando o 'php artisan serve' (que só tem um "trabalhador")
// recebe uma chamada (do gráfico) enquanto já está ocupado (a carregar o dashboard).
//
// Ao executar o script Python diretamente (com Process::...)
// dentro do widget, evitamos a chamada de rede (Http::get) desnecessária.