<?php

namespace App\Filament\Exports;

use App\Models\Inventory\Brand;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BrandExporter extends Exporter
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nome da Marca'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' marca(s) exportada(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'marcas-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }
}