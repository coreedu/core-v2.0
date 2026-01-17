<?php

namespace App\Filament\Resources\TimeSlotsResource\Pages;

use App\Filament\Resources\TimeSlotsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeSlots extends ListRecords
{
    protected static string $resource = TimeSlotsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
