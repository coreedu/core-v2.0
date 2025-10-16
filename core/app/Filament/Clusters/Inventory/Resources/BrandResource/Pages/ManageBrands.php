<?php

namespace App\Filament\Clusters\Inventory\Resources\BrandResource\Pages;

use App\Filament\Clusters\Inventory\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Components\HelpButton;

class ManageBrands extends ManageRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            HelpButton::make('brand'),
        ];
    }
}