<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Room; // <-- Importe o Model 'Room'

class RoomInventoryChart extends ChartWidget
{
    protected static ?string $heading = 'Inventário: Total de Salas por Categoria';
    
    protected static string $views = 'filament.widgets.size_style_graphics';

    // public function getColumnSpan(): int | string | array
    // {
    //     return [
    //     'sm' => 12,
    //     'md' => 6, // ocupa 6 das 12 colunas
    //     'lg' => 6, // metade da tela
    // ];
    // }

    protected static ?int $sort = 1; // Para aparecer primeiro (opcional)

    protected function getData(): array
    {
        // 1. Chamar o método do Model
        $data = Room::getCountByCategory();

        return [
            'datasets' => [
                [
                    'label' => 'Total de Salas',
                    // Pega só os números: [50, 10, 3]
                    'data' => $data->pluck('room_count'),
                    'backgroundColor' => 'rgba(255, 159, 64, 0.7)', // Laranja
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                ],
            ],
            // Pega só os nomes: ['Sala Comum', 'Laboratório', 'Auditório']
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Gráfico de barras
    }
}