<?php

namespace App\Filament\Exports;

use App\Models\Curso;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CursoExporter extends Exporter
{
    protected static ?string $model = Curso::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nome')->label('Nome do Curso'),
            ExportColumn::make('abreviacao')->label('Abreviação'),
            ExportColumn::make('qtdModulos')->label('Quantidade de Módulos'),
            ExportColumn::make('modality.name')->label('Modalidade'),
            ExportColumn::make('shift.name')->label('Turno'),
            ExportColumn::make('horas')->label('Carga Horária Total'),
            ExportColumn::make('horasEstagio')->label('Horas Estágio'),
            ExportColumn::make('horasTg')->label('Horas TCC/TG'),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' curso(s) exportado(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'cursos-' . now()->format('Y-m-d_H-i-s') . '.csv';
    }
}