<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
// Importe o Model de Equipamento
use App\Models\Inventory\Equipment; 

class EquipmentUsageChart extends ChartWidget
{
    protected static ?string $heading = 'Fator de Procura: Equipamentos Mais Solicitados';

    protected static string $views = 'filament.widgets.size_style_graphics';

    // public function getColumnSpan(): int | string | array
    // {
    //     return [
    //     'sm' => 12,
    //     'md' => 6, // ocupa 8 das 12 colunas
    //     'lg' => 6, // metade da tela
    // ];
    // }

    protected static ?int $sort = 2; // Ordem 2 (depois do Gráfico 1)

    protected function getData(): array
    {
        // 1. Chamar o método do Model
        $data = Equipment::getUsageByEquipment();

        return [
            'datasets' => [
                [
                    'label' => 'Aulas Alocadas',
                    'data' => $data->pluck('usage_count'),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)', // Verde/Ciano
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        // Barras horizontais são melhores para rankings
        return 'bar';
    }

    /**
     * Adicionamos este método para fazer o gráfico ser HORIZONTAL.
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // <-- Vira as barras para horizontal
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
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