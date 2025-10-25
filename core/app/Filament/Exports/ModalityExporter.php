<?php

namespace App\Filament\Exports;

use App\Models\Modality;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class ModalityExporter extends Exporter
{
    protected static ?string $model = Modality::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nome da Modalidade'),
            ExportColumn::make('description')->label('Descrição'),
        ];
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
            ExportFormat::Xlsx,
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' modalidade(s) exportada(s).';

        if ($failed = $export->getFailedRowsCount()) {
            $body .= " {$failed} linha(s) falharam.";
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'modalidades-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }
}
