<?php

namespace App\Filament\Exports;

use App\Models\Componente;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class ComponenteExporter extends Exporter
{
    protected static ?string $model = Componente::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nome')
                ->label('Nome do Componente'),

            ExportColumn::make('abreviacao')
                ->label('Abreviação'),

            ExportColumn::make('horasSemanais')
                ->label('Horas Semanais'),

            ExportColumn::make('horasTotais')
                ->label('Horas Totais'),
        ];
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
            ExportFormat::Xlsx,
        ];
    }

    public static function getCsvDelimiter(): string
    {
        return ';';
    }

    public function getFileName(Export $export): string
    {
        return 'componentes-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' componente(s) exportado(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}
