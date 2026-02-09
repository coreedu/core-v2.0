<?php

namespace App\Filament\Resources\TimeConfigResource\Pages;

use App\Filament\Resources\TimeConfigResource;
use App\Models\Time\TimeConfig;
use App\Models\Time\TimeSlots;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Filament\Components\HelpButton;

class CreateTimeConfig extends CreateRecord
{
    protected static string $resource = TimeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            HelpButton::make('edit-config'),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $contextId = $data['context_id'];
            $allTabsData = $data['slots'] ?? [];
            $mainRecord = null;
            
            activity()->withoutLogs(function () use ($contextId, $allTabsData, &$mainRecord) {
                foreach ($allTabsData as $shiftId => $days) {
                    
                    $config = TimeConfig::firstOrCreate([
                        'context_id' => $contextId,
                        'shift_id'   => $shiftId,
                    ]);

                    if (!$mainRecord) {
                        $mainRecord = $config;
                    }

                    $config->slots()->delete();

                    foreach ($days as $dayId => $horariosIds) {
                        if (is_array($horariosIds)) {
                            foreach ($horariosIds as $lessonTimeId) {
                                TimeSlots::create([
                                    'time_config_id' => $config->id,
                                    'day_id'         => $dayId,
                                    'lesson_time_id' => $lessonTimeId,
                                ]);
                            }
                        }
                    }
                }

            });

            activity()
                ->performedOn($mainRecord)
                ->causedBy(auth()->user())
                ->event('created')
                ->log("Criou a configuração de horários para o contexto {$contextId}.");
            return $mainRecord;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
