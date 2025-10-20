<?php

namespace App\Filament\Clusters\Inventory\Resources\TypeResource\Pages;

use App\Filament\Clusters\Inventory\Resources\TypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Components\HelpButton;

class ManageTypes extends ManageRecords
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            HelpButton::make('type'),
        ];
    }
}