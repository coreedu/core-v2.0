<?php

namespace App\Filament\Imports;

use App\Models\Room;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RoomImporter extends Importer
{
    protected static ?string $model = Room::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome')
                ->rules(['nullable', 'string', 'max:100'])
                ->example('Sala Maker'),

            ImportColumn::make('number')
                ->label('Número')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:10'])
                ->example('101'),
        ];
    }

    public function resolveRecord(): ?Room
    {
        return Room::firstOrNew([
            'number' => $this->data['number'] ?? null,
        ]);
    }

    protected function beforeSave(): void
    {
        $this->record->active = true;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' ambiente(s) importado(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}