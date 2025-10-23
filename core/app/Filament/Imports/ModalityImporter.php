<?php

namespace App\Filament\Imports;

use App\Models\Modality;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ModalityImporter extends Importer
{
    protected static ?string $model = Modality::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome da Modalidade')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:100'])
                ->example('Presencial'),

            ImportColumn::make('description')
                ->label('Descrição')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Aulas totalmente presenciais.'),
        ];
    }

    public function resolveRecord(): ?Modality
    {
        return Modality::firstOrNew(['name' => $this->data['name']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' modalidade(s) importada(s).';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= " {$failed} linha(s) falharam.";
        }

        return $body;
    }
}
