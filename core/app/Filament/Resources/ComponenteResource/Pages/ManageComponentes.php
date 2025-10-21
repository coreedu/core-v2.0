<?php

namespace App\Filament\Resources\ComponenteResource\Pages;

use App\Filament\Resources\ComponenteResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Components\HelpButton;

class ManageComponentes extends ManageRecords
{
    protected static string $resource = ComponenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            HelpButton::make('componente'),
        ];
    }
}
