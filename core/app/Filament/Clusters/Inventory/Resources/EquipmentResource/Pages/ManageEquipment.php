<?php

namespace App\Filament\Clusters\Inventory\Resources\EquipmentResource\Pages;

use App\Filament\Clusters\Inventory\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Components\HelpButton;

class ManageEquipment extends ManageRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            HelpButton::make('equipment'),
        ];
    }
}
