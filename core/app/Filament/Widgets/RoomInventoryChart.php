<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Room; // Responsável por acessar os dados de salas no banco

class RoomInventoryChart extends ChartWidget
{
    // Título do widget exibido no dashboard
    protected static ?string $heading = 'Recursos Disponíveis: Salas por Tipo';
    
    // View customizada para estilização e layout do gráfico
    protected static string $views = 'filament.widgets.size_style_graphics';

    // Prioriza este widget na ordem de exibição
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Recupera, a partir do Model, a quantidade de salas agrupadas por categoria
        $data = Room::getCountByCategory();

        return [
            'datasets' => [
                [
                    // Nome da série apresentada no gráfico
                    'label' => 'Total de Salas',

                    // Quantidades por categoria (ex.: 10 salas comuns, 5 laboratórios etc.)
                    'data' => $data->pluck('room_count'),

                    // Cor de preenchimento das barras para melhor visualização
                    'backgroundColor' => 'rgba(255, 159, 64, 0.7)',

                    // Cor da borda, reforçando contraste e clareza gráfica
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                ],
            ],

            // Nome das categorias exibidas no eixo X (ex.: Sala Comum, Laboratório, Auditório)
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        // Indica que o widget vai renderizar um gráfico de barras
        return 'bar';
    }
}