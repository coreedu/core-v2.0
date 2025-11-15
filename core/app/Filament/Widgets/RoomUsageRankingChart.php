<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ClassSchedule; // <-- Importa o Model "cola"

class RoomUsageRankingChart extends ChartWidget
{
    protected static string $views = 'filament.widgets.size_style_graphics';
    protected static ?string $heading = 'Ranking: Salas Mais Utilizadas (Top 10)';

    // Define a ordem para 4 (para aparecer depois dos outros 3)
    protected static ?int $sort = 3;

    // (Opcional) Se você quiser que este gráfico use a sua view de altura fixa
    // protected static string $view = 'filament.widgets.size_style_graphics';

    // (Opcional) Define o layout. 12 = Linha inteira
    // public function getColumnSpan(): int | string | array
    // {
    //     return 12;
    // }

    protected function getData(): array
    {
        // 1. Chamar o novo método do Model
        $data = ClassSchedule::getUsageRankingByRoom();

        return [
            'datasets' => [
                [
                    'label' => 'Total de Aulas',
                    // Pega só os números: [50, 45, 30]
                    'data' => $data->pluck('usage_count'),
                    'backgroundColor' => 'rgba(153, 102, 255, 0.7)', // Roxo
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                ],
            ],
            // Pega só os nomes: ['Sala 101', 'Laboratório B', 'Sala 203']
            'labels' => $data->pluck('room_name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Gráfico de barras
    }

    /**
     * Adicionamos este método para fazer o gráfico ser HORIZONTAL.
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // <-- Vira as barras para horizontal (deitado)
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Contagem Total de Aulas',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Legenda não é necessária
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'js:function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += context.parsed.x; 
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}