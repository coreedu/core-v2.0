<?php

namespace App\Filament\Exports;

use App\Models\Inventory\Type;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TypeExporter extends Exporter
{
    protected static ?string $model = Type::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nome do Tipo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' tipo(s) exportado(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'tipos-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }
}