<?php

namespace App\Filament\Resources\TimeConfigResource\Pages;

use App\Filament\Resources\TimeConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Time\TimeConfig;

class EditTimeConfig extends EditRecord
{
    protected static string $resource = TimeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $contextId = $this->record->context_id;

        $configs = TimeConfig::where('context_id', $contextId)
            ->with('slots')
            ->get();

        $slotsData = [];

        foreach ($configs as $config) {
            foreach ($config->slots as $slot) {
                $slotsData[$config->shift_id][$slot->day_id][] = $slot->lesson_time_id;
            }
        }

        $data['slots'] = $slotsData;

        return $data;
    }

    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    //     return DB::transaction(function () use ($record, $data) {
    //         $contextId = $data['context_id'];
    //         $allTabsData = $data['slots'] ?? [];

    //         $record->update(['context_id' => $contextId]);

    //         foreach ($allTabsData as $shiftId => $days) {
    //             $config = TimeConfig::firstOrCreate([
    //                 'context_id' => $contextId,
    //                 'shift_id'   => $shiftId,
    //             ]);

    //             $config->slots()->delete();

    //             foreach ($days as $dayId => $horariosIds) {
    //                 if (is_array($horariosIds)) {
    //                     foreach ($horariosIds as $lessonTimeId) {
    //                         $config->slots()->create([
    //                             'day_id' => $dayId,
    //                             'lesson_time_id' => $lessonTimeId,
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }

    //         return $record;
    //     });
    // }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $contextId = $data['context_id'];
            $allTabsData = $data['slots'] ?? [];

            $record->update(['context_id' => $contextId]);

            foreach ($allTabsData as $shiftId => $days) {
                $config = TimeConfig::firstOrCreate([
                    'context_id' => $contextId,
                    'shift_id'   => $shiftId,
                ]);

                $keptSlotIds = [];

                foreach ($days as $dayId => $horariosIds) {
                    if (is_array($horariosIds)) {
                        foreach ($horariosIds as $lessonTimeId) {
                            $slot = $config->slots()->updateOrCreate(
                                [
                                    'day_id' => $dayId,
                                    'lesson_time_id' => $lessonTimeId,
                                ]
                            );

                            $keptSlotIds[] = $slot->id;
                        }
                    }
                }
                $config->slots()
                    ->whereNotIn('id', $keptSlotIds)
                    ->get()
                    ->each(function ($oldSlot) {
                        
                        $hasClasses = \DB::table('class_schedule')->where('slot_id', $oldSlot->id)->exists();
                        
                        if (!$hasClasses) {
                            $oldSlot->delete();
                        }
                    });
            }

            return $record;
        });
    }
}
