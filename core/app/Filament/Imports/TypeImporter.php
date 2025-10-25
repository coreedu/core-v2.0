<?php

namespace App\Filament\Imports;

use App\Models\Inventory\Type;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TypeImporter extends Importer
{
    protected static ?string $model = Type::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome do Tipo')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Projetor'),
        ];
    }

    public function resolveRecord(): ?Type
    {
        return Type::firstOrNew([
            'name' => $this->data['name'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' tipo(s) importado(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}