<?php

namespace App\Filament\Imports;

use App\Models\Curso;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CursoImporter extends Importer
{
    protected static ?string $model = Curso::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nome')
                ->label('Nome do Curso')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:150'])
                ->example('Análise e Desenvolvimento de Sistemas'),

            ImportColumn::make('abreviacao')
                ->label('Abreviação')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:10'])
                ->example('ADS'),

            ImportColumn::make('qtdModulos')
                ->label('Quantidade de Módulos')
                ->numeric()
                ->rules(['required', 'integer', 'min:1'])
                ->example(4),

            ImportColumn::make('horas')
                ->label('Carga Horária Total')
                ->numeric()
                ->rules(['required', 'integer', 'min:1'])
                ->example(3200),

            ImportColumn::make('horasEstagio')
                ->label('Carga Horária do Estágio')
                ->numeric()
                ->rules(['nullable', 'integer'])
                ->example(400),

            ImportColumn::make('horasTg')
                ->label('Carga Horária do TCC/TG')
                ->numeric()
                ->rules(['nullable', 'integer'])
                ->example(200),
        ];
    }

    public function resolveRecord(): ?Curso
    {
        return Curso::firstOrNew(['abreviacao' => $this->data['abreviacao']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' curso(s) importado(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}
