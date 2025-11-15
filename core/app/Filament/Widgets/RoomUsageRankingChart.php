<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ClassSchedule; // Model responsável por fornecer dados de uso das salas

class RoomUsageRankingChart extends ChartWidget
{
    // View customizada utilizada para padronizar estilo e dimensões
    protected static string $views = 'filament.widgets.size_style_graphics';

    // Título exibido no dashboard, destacando o propósito analítico do gráfico
    protected static ?string $heading = 'Ranking: Salas Mais Utilizadas (Top 10)';

    // Controla a posição deste widget na ordem de carregamento
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Obtém o ranking de salas mais utilizadas, fornecido pelo Model
        $data = ClassSchedule::getUsageRankingByRoom();

        return [
            'datasets' => [
                [
                    // Nome da série de dados apresentada no gráfico
                    'label' => 'Total de Aulas',

                    // Lista de quantidades de aulas por sala
                    'data' => $data->pluck('usage_count'),

                    // Cor de preenchimento das barras (paleta roxa para destaque)
                    'backgroundColor' => 'rgba(153, 102, 255, 0.7)',

                    // Cor da borda para melhor definição visual
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                ],
            ],

            // Lista com os nomes das salas exibidos no eixo Y
            'labels' => $data->pluck('room_name'),
        ];
    }

    protected function getType(): string
    {
        // Gráfico de barras (em combinação com a opção horizontal abaixo)
        return 'bar';
    }

    /**
     * Configurações complementares para transformar o gráfico em formato horizontal,
     * favorecendo a leitura do ranking e o comparativo entre as salas.
     */
    protected function getOptions(): array
    {
        return [
            // Define "y" como eixo principal, convertendo as barras para o formato horizontal
            'indexAxis' => 'y',

            // Configuração do eixo X (quantidade total de aulas)
            'scales' => [
                'x' => [
                    'beginAtZero' => true, // Garante leitura correta desde o zero
                    'title' => [
                        'display' => true,
                        'text' => 'Contagem Total de Aulas', // Título explicativo para o eixo
                    ],
                ],
            ],

            // Configurações de plugins do Chart.js
            'plugins' => [
                'legend' => [
                    'display' => false, // Oculta legenda para reduzir ruído visual
                ],
                'tooltip' => [
                    'callbacks' => [
                        // Formatação personalizada do tooltip no front-end
                        'label' => 'js:function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += context.parsed.x; // Valor da barra (aulas)
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}
