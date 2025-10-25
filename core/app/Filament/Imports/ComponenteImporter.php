<?php

namespace App\Filament\Imports;

use App\Models\Componente;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ComponenteImporter extends Importer
{
    protected static ?string $model = Componente::class;

    /**
     * Colunas que o usuário poderá mapear no CSV.
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nome')
                ->label('Nome do Componente')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:150'])
                ->example('Matemática Aplicada'),

            ImportColumn::make('abreviacao')
                ->label('Abreviação')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:20'])
                ->example('MATAP'),

            ImportColumn::make('horasSemanais')
                ->label('Horas Semanais')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0'])
                ->example(20),

            ImportColumn::make('horasTotais')
                ->label('Horas Totais')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0'])
                ->example(320),
        ];
    }

    /**
     * Define como localizar ou criar o registro.
     */
    public function resolveRecord(): ?Componente
    {
        return Componente::firstOrNew([
            'abreviacao' => $this->data['abreviacao'] ?? null,
        ]);
    }

    /**
     * Mensagem de notificação ao finalizar a importação.
     */
    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' componente(s) importado(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }

        return $body;
    }
}
