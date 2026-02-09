<?php

namespace App\Filament\Resources\TimeConfigResource\Pages;

use App\Filament\Resources\TimeConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Components\HelpButton;

class ListTimeConfigs extends ListRecords
{
    protected static string $resource = TimeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            HelpButton::make('list-config'),
        ];
    }
}
