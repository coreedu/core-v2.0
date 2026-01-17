<?php

namespace App\Filament\Resources\TimeSlotsResource\Pages;

use App\Filament\Resources\TimeSlotsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeSlots extends EditRecord
{
    protected static string $resource = TimeSlotsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
