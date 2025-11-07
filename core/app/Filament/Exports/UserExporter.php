<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('rm')->label('RM'),
            ExportColumn::make('name')->label('Nome'),
            ExportColumn::make('email')->label('E-mail'),
            ExportColumn::make('email_verified_at')->label('Verificado em'),
            ExportColumn::make('photo')->label('Foto'),
            ExportColumn::make('birthdate')->label('Nascimento'),
            ExportColumn::make('course')->label('Curso'),
            ExportColumn::make('semester')->label('Semestre'),
            ExportColumn::make('shift')->label('Turno'),
            ExportColumn::make('is_determined')
                ->label('Contrato Determinado')
                ->formatStateUsing(fn(bool $state): string => $state ? 'Sim' : 'Não'),
            ExportColumn::make('contract_end_at')->label('Fim do Contrato'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de usuários foi concluída com sucesso. ' . number_format($export->successful_rows) . ' linha(s) exportada(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' linha(s) falharam ao exportar.';
        }

        return $body;
    }
}
