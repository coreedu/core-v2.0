<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
// 1. Importações que estavam na Rota
use App\Models\ClassSchedule;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PredictiveRankingChart extends ChartWidget
{
    protected static ?string $heading = 'Predição: Ranking de Demanda (Top 5 / Bottom 5)';
    protected ?string $errorTitle = null;
    protected static ?int $sort = 3;

    public function getColumnSpan(): int | string | array
    {
        return 12;
    }
    
    public function getHeading(): string
    {
        if ($this->errorTitle) {
            return $this->errorTitle;
        }
        return self::$heading;
    }

    protected function getData(): array
    {
        @set_time_limit(120); // Damos 2 minutos ao widget
        
        $cacheKey = 'prediction_ranking_chart_data';
        $cacheDuration = 60 * 60; // 1 hora

        // --- MUDANÇA PRINCIPAL: A LÓGICA AGORA ESTÁ AQUI DENTRO ---
        $predictionData = Cache::remember($cacheKey, $cacheDuration, function () {
            
            try {
                // --- 1. COPIA DO routes/web.php: Coleta de Dados ---
                $aulas = ClassSchedule::query()
                    ->with([
                        'schedule', 'schedule.course', 'sala', 'sala.equipments', 'dia', 'lessonTime'
                    ])
                    ->select(
                        'id as aula_id', 'schedule_id', 'created_at',
                        'room', 'room as room_id',
                        'day', 'day as day_id',
                        'time', 'time as lesson_time_id'
                    )
                    ->get();
                
                if ($aulas->isEmpty()) {
                    return ['error' => 'Nenhuma aula (class_schedule) encontrada.'];
                }
                
                $jsonData = $aulas->toJson();

                // --- 2. COPIA DO routes/web.php: Execução do Python ---
                $scriptPath = base_path('storage/app/python/predict_demand.py');
                $env = ['PYTHONIOENCODING' => 'UTF-8', 'LANG' => 'en_US.UTF-8'];

                $process = new Process(['py', $scriptPath], base_path(), $env);
                
                try {
                    $process->setInput($jsonData);
                    $process->mustRun();
                } catch (ProcessFailedException $e) {
                    // Tenta 'python' se 'py' falhar
                    $process = new Process(['python', $scriptPath], base_path(), $env);
                    $process->setInput($jsonData);
                    $process->mustRun();
                }

                $output = $process->getOutput();
                
                if (empty($output)) {
                    return ['error' => 'O script Python executou mas não retornou saída.'];
                }

                $result = json_decode($output, true); // true para array
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return ['error' => 'Python retornou JSON inválido.', 'raw_output' => $output];
                }

                return $result; // <-- Salva o JSON do Python no cache

            } catch (ProcessFailedException $e) {
                // Erro se 'py' e 'python' falharem
                return ['error' => 'Script Python não encontrado ou falhou.', 'stderr' => $e->getMessage()];
            } catch (\Exception $e) {
                // Erro geral do PHP (ex: coleta de dados)
                return ['error' => 'Erro PHP: ' . $e->getMessage()];
            }
        });
        // --- FIM DA LÓGICA MOVIDA ---

        // 4. Se os dados (do cache ou da chamada) contiverem um erro
        if (isset($predictionData['error'])) {
            $this->errorTitle = $predictionData['error']; 
            return ['datasets' => [], 'labels' => []];
        }
        
        if (!is_array($predictionData) || empty($predictionData)) {
             $this->errorTitle = 'Predição retornou dados vazios ou inválidos.';
             return ['datasets' => [], 'labels' => []];
        }

        // 6. Lógica de Ranking (Top 5 / Bottom 5)
        $dataCollection = collect($predictionData);

        $sorted = $dataCollection->sortByDesc('predicted_score');
        $top5 = $sorted->take(5);
        $bottom5 = $sorted->take(-5)->sortBy('predicted_score');
        $finalData = $top5->merge($bottom5)->unique('id_da_sala');

        // 7. Prepara os dados para o Chart.js
        $datasets = [
            [
                'label' => 'Pontuação de Demanda Prevista',
                'data' => $finalData->pluck('predicted_score')->all(),
                'backgroundColor' => $finalData->map(function ($item, $key) use ($top5) {
                    return $top5->contains('id_da_sala', $item['id_da_sala'])
                        ? 'rgba(75, 192, 192, 0.7)'  // Verde (Top)
                        : 'rgba(255, 99, 132, 0.7)'; // Vermelho (Bottom)
                })->all(),
            ]
        ];
        
        $labels = $finalData->pluck('sala_name')->all();

        return [
            'datasets' => $datasets,
            'labels' => $labels
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', 
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Pontuação de Demanda (Prevista pelo Python)'
                    ]
                ],
                'y' => [
                    'ticks' => ['autoskip' => false] 
                ]
            ],
            'plugins' => [
                'legend' => ['display' => false]
            ]
        ];
    }
}