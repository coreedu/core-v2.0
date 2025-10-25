<?php

namespace App\Filament\Imports;

use App\Models\Inventory\Brand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BrandImporter extends Importer
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome da Marca')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Samsung'),
        ];
    }

    public function resolveRecord(): ?Brand
    {
        return Brand::firstOrNew([
            'name' => $this->data['name'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' marca(s) importada(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}
