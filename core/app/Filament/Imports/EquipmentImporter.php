<?php

namespace App\Filament\Imports;

use App\Models\Inventory\Equipment;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EquipmentImporter extends Importer
{
    protected static ?string $model = Equipment::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Projetor Epson X400'),

            ImportColumn::make('patrimony')
                ->label('Patrimônio')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('12345'),

            ImportColumn::make('observation')
                ->label('Observações')
                ->rules(['nullable', 'string'])
                ->example('Equipamento com leves arranhões.'),
        ];
    }

    public function resolveRecord(): ?Equipment
    {
        return Equipment::firstOrNew([
            'name' => $this->data['name'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' equipamento(s) importado(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}
