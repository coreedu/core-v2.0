<?php

namespace App\Filament\Exports;

use App\Models\Room;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RoomExporter extends Exporter
{
    protected static ?string $model = Room::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nome'),
            ExportColumn::make('number')->label('Número'),
            ExportColumn::make('category.name')->label('Categoria'),
            ExportColumn::make('active')->label('Ativo')->formatStateUsing(fn($state) => $state ? 'Sim' : 'Não'),
            ExportColumn::make('equipments_count')->counts('equipments')->label('Qtd. Equipamentos'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' ambiente(s) exportado(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'ambientes-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }
}