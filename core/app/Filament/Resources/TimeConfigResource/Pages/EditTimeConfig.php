<?php

namespace App\Filament\Resources\TimeConfigResource\Pages;

use App\Filament\Resources\TimeConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Time\TimeConfig;
use App\Models\Time\Context;
use App\Filament\Components\HelpButton;

class EditTimeConfig extends EditRecord
{
    protected static string $resource = TimeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading("Exclusão do Contexto")
                ->modalDescription(fn (TimeConfig $record) => 
                    "Atenção: Isso apagará TUDO vinculado ao contexto '{$record->context?->name}', incluindo todos os slots e horarios existentes. Deseja prosseguir?"
                )
                ->before(function (TimeConfig $record) {
                    DB::transaction(function () use ($record) {
                        $context = $record->context;

                        if ($context) {
                            // 1. Buscamos todas as TimeConfigs deste contexto (os 3 turnos)
                            $allConfigs = $context->timeConfigs;

                            foreach ($allConfigs as $config) {
                                activity()->withoutLogs(function () use ($config, $record) {
                                    $config->schedules->each(function($schedule) {
                                        $schedule->items()->delete(); 
                                        
                                        $schedule->delete();
                                    });

                                    $config->slots()->delete();

                                    // Apagar a configuração do turno (exceto o record atual que o Filament apagará)
                                    if ($config->id !== $record->id) {
                                        $config->delete();
                                    }
                                });
                            }

                            activity()
                                ->performedOn($context)
                                ->event('deleted')
                                ->log("Exclusão completa do contexto {$context->name}.");
                        }
                    });
                })
                ->after(function (TimeConfig $record) {
                    $context = $record->context;
                    if ($context) {
                        $context->delete();
                    }
                }),
            HelpButton::make('edit-config'),
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $context = Context::find($data['context_id']);
            $contextId = $context->id;
            $allTabsData = $data['slots'] ?? [];

            activity()->withoutLogs(function () use ($record, $contextId, $allTabsData) {
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
            });

            activity()
                ->performedOn($record->context)
                ->causedBy(auth()->user())
                ->event('updated')
                ->log("Atualizou a configuração de horários para o contexto {$context->name}.");

            return $record;
        });
    }
}
