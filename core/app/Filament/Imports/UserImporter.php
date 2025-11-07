<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('rm')
                ->label('RM')
                ->requiredMapping()
                ->rules(['required', 'numeric'])
                ->example('202500123'),

            ImportColumn::make('name')
                ->label('Nome Completo')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:191'])
                ->example('João da Silva'),

            ImportColumn::make('email')
                ->label('Email de Acesso')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:191'])
                ->example('joao@email.com'),

            ImportColumn::make('password')
                ->label('Senha de Acesso')
                ->rules(['nullable', 'string', 'max:191'])
                ->fillRecordUsing(function (User $record, ?string $state): void {
                    if (!empty($state)) {
                        $record->password = Hash::make($state);
                    }
                })
                ->example('123456'),
        ];
    }

    public function resolveRecord(): ?User
    {
        return User::firstOrNew([
            'rm' => $this->data['rm'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' usuário(s) importado(s).';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " {$failedRowsCount} linha(s) falharam.";
        }
        return $body;
    }
}