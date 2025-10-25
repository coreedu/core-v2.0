<?php

namespace App\Filament\Exports;

use App\Models\Inventory\Equipment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EquipmentExporter extends Exporter
{
    protected static ?string $model = Equipment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nome'),
            ExportColumn::make('patrimony')->label('Patrimônio'),
            ExportColumn::make('observation')->label('Observações'),
            ExportColumn::make('status')->label('Disponível')->formatStateUsing(fn($state) => $state ? 'Disponível' : 'Indisponível'),
            ExportColumn::make('brand.name')->label('Marca'),
            ExportColumn::make('type.name')->label('Tipo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' equipamento(s) exportado(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'equipamentos-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }
}
