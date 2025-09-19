<?php

namespace App\Filament\Clusters\Time\Resources\Time\LessonTimeResource\Pages;

use App\Filament\Clusters\Time\Resources\Time\LessonTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLessonTimes extends ManageRecords
{
    protected static string $resource = LessonTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
