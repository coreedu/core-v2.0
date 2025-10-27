<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Schedule; // <-- Importe o Model 'Schedule'

class TrendUsageChart extends ChartWidget
{
    protected static ?string $heading = 'Predição: Demanda por Categoria (por Versão)';
    
    protected static string $views = 'filament.widgets.size_style_graphics';


    // public function getColumnSpan(): int | string | array
    // {
    //     return 4;
    // }

    protected static ?int $sort = 3; // Ordem 3 (o último gráfico)

    protected function getData(): array
    {
        // O método no Model já retorna os dados formatados
        return Schedule::getUsageTrendByCategory();
    }

    protected function getType(): string
    {
        return 'line'; // Gráfico de Linha
    }
}