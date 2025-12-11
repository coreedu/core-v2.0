<?php

namespace App\Filament\Resources\GroupEquipmentResource\Pages;

use App\Filament\Resources\GroupEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroupEquipment extends EditRecord
{
    protected static string $resource = GroupEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
