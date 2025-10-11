<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('availability')
                ->label('Gerenciar Disponibilidade')
                ->icon('heroicon-o-calendar')
                ->url(fn () => static::getResource()::getUrl('manage-availability', ['record' => $this->record])),
        ];
    }
}
