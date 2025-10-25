<?php

namespace App\Filament\Imports;

use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CategoryImporter extends Importer
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome da Categoria')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:50'])
                ->example('Salas de Aula'),

            ImportColumn::make('description')
                ->label('Descrição')
                ->rules(['required', 'string', 'max:50'])
                ->example('Espaços destinados a aulas regulares'),
        ];
    }

    public function resolveRecord(): ?Category
    {
        return Category::firstOrNew([
            'name' => $this->data['name'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' categoria(s) importada(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}