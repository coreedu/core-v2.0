<?php

namespace App\Filament\Resources\GroupEquipmentResource\Pages;

use App\Filament\Resources\GroupEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroupEquipment extends ListRecords
{
    protected static string $resource = GroupEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
