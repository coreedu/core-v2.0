<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class PredictiveRankingChart extends ChartWidget
{
    // 1. O título estático (padrão)
    protected static ?string $heading = 'Predição: Ranking de Demanda (Top 5 / Bottom 5)';

    // --- CORREÇÃO (PASSO 1) ---
    // Adicionamos uma propriedade NÃO-ESTÁTICA para guardar o erro
    protected ?string $errorTitle = null;
    // -------------------------

    protected static ?int $sort = 3;

    public function getColumnSpan(): int | string | array
    {
        return 12; // Ocupa a largura total
    }
    
    // --- CORREÇÃO (PASSO 2) ---
    // Adicionamos o método getHeading()
    // Este método é NÃO-ESTÁTICO e é chamado pelo Filament para obter o título
    public function getHeading(): string
    {
        // Se a nossa propriedade de erro foi definida, mostra o erro
        if ($this->errorTitle) {
            return $this->errorTitle;
        }
        // Senão, mostra o título estático padrão
        return self::$heading;
    }
    // -------------------------

    protected function getData(): array
    {
        $cacheKey = 'prediction_ranking_chart_data';
        $cacheDuration = 60 * 60; // 1 hora

        $predictionData = Cache::remember($cacheKey, $cacheDuration, function () {
            try {
                $response = Http::timeout(180)->get(route('run-prediction'));
                if (!$response->successful()) {
                    return ['error' => 'Falha ao buscar predição. Rota retornou erro.'];
                }
                return $response->json();
            } catch (ConnectionException $e) {
                return ['error' => 'Erro de conexão ao chamar a rota /run-prediction.'];
            } catch (\Exception $e) {
                return ['error' => 'Erro geral: ' . $e->getMessage()];
            }
        });

        // --- CORREÇÃO (PASSO 3) ---
        // A linha 63 estava aqui.
        // Mudamos de "$this->heading = ..." para "$this->errorTitle = ..."
        if (isset($predictionData['error']) && is_string($predictionData['error'])) {
            $this->errorTitle = $predictionData['error']; // <-- CORRIGIDO
            return ['datasets' => [], 'labels' => []];
        }
        // -------------------------

        // Se os dados (do cache ou da chamada) não forem um array ou estiverem vazios
        if (!is_array($predictionData) || empty($predictionData)) {
             $this->errorTitle = 'Predição retornou dados vazios ou inválidos.';
             return ['datasets' => [], 'labels' => []];
        }

        // 6. Lógica de Ranking (Top 5 / Bottom 5)
        $dataCollection = collect($predictionData);

        // Ordena do maior para o menor
        $sorted = $dataCollection->sortByDesc('predicted_score');

        // Pega os 5 primeiros
        $top5 = $sorted->take(5);
        
        // Pega os 5 últimos (se houver mais de 10 salas)
        $bottom5 = $sorted->take(-5)->sortBy('predicted_score');

        // Junta os dois grupos
        $finalData = $top5->merge($bottom5)->unique('id_da_sala');

        // 7. Prepara os dados para o Chart.js
        $datasets = [
            [
                'label' => 'Pontuação de Demanda Prevista',
                'data' => $finalData->pluck('predicted_score')->all(),
                
                // Cor: Verde para Top 5, Vermelho para Bottom 5
                'backgroundColor' => $finalData->map(function ($item, $key) use ($top5) {
                    // Verifica se o ID está na lista do Top 5
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
        return 'bar'; // Gráfico de barras
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Faz o gráfico ser Horizontal
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