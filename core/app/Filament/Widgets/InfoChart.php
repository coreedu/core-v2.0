<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class InfoChart extends ChartWidget
{
    protected static ?string $heading = 'Total de Usuarios';

    protected function getData(): array
    {
        $categories = Category::categories();

        return [
            'datasets' => [
                [
                    'label' => 'Total de categorias',
                    'data' => [$categories],
                    'backgroundColor' => 'rgba(59,130,246,0.5)',
                ],
            ],
            'labels' => 'C',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
