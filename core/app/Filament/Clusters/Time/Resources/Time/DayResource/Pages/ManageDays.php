<?php

namespace App\Filament\Clusters\Time\Resources\Time\DayResource\Pages;

use App\Filament\Clusters\Time\Resources\Time\DayResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Components\HelpButton;

class ManageDays extends ManageRecords
{
    protected static string $resource = DayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            HelpButton::make('day'),
        ];
    }
}
