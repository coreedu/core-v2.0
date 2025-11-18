<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
// Model responsável por fornecer dados de uso de equipamentos
use App\Models\Inventory\Equipment; 

class EquipmentUsageChart extends ChartWidget
{
    // Título institucional exibido na área de dashboards
    protected static ?string $heading = 'Fator de Procura: Equipamentos Mais Solicitados';

    // View customizada para controle visual e responsividade
    protected static string $views = 'filament.widgets.size_style_graphics';

    // Controla a posição deste widget na ordem de exibição
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Recupera a métrica de uso de cada equipamento a partir do Model
        $data = Equipment::getUsageByEquipment();

        return [
            'datasets' => [
                [
                    // Rótulo da série de dados exibida no gráfico
                    'label' => 'Aulas Alocadas',

                    // Quantidade de utilizações por equipamento
                    'data' => $data->pluck('usage_count'),

                    // Cor principal das barras para reforçar visual analítico
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)',

                    // Destaque da borda das barras para melhorar contraste
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],

            // Lista com os nomes dos equipamentos exibidos no eixo Y
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        // Gráfico de barras (usado em conjunto com a opção horizontal)
        return 'bar';
    }

    /**
     * Configurações adicionais do gráfico,
     * garantindo barras horizontais e melhor experiência visual para ranking.
     */
    protected function getOptions(): array
    {
        return [
            // Define que o eixo principal será o Y, tornando o gráfico horizontal
            'indexAxis' => 'y',

            // Garante que o valor mínimo no eixo X seja zero
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],

            // Customização dos tooltips para exibição mais clara dos valores
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        // Callback JS executado no front-end para formatar o tooltip
                        'label' => 'js:function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += context.parsed.x; // Valor da barra
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}
