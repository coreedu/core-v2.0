<?php

namespace App\Filament\Clusters\Time\Resources\Time\ShiftResource\Pages;

use App\Filament\Clusters\Time\Resources\Time\ShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Components\HelpButton;

class ManageShifts extends ManageRecords
{
    protected static string $resource = ShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            HelpButton::make('shift'),
        ];
    }
}
