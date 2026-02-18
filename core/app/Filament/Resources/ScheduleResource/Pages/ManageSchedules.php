<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\Page;
use App\Models\Schedule;
use App\Models\Curso;
use App\Models\Room;
use App\Models\User;
use App\Models\ClassSchedule;
use App\Models\Componente;
use App\Models\Time\Day;
use App\Models\Time\TimeSlots;
use App\Models\TimeDay;
use Filament\Actions as PageActions;
use App\Filament\Components\HelpButton;
use Illuminate\Support\Facades\DB;

class ManageSchedules extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static string $view = 'filament.resources.schedule-resource.pages.manage-schedules';
    protected static ?string $title = 'Gerenciar Grades Horárias';

    public Schedule $record;
    public array $days = [];
    public array $timeSlots = [];
    public $slots = [];
    public $enabledSlots = [];
    public array $rooms = [];

    protected $listeners = ['subjectChanged' => 'onSubjectChanged'];

    public array $scheduleData = [];
    public array $saturdayTimes = [];
    public array $subjects = [];
    public $scheduleVersion = 0;

    public function mount(Schedule $record): void
    {
        $this->record = $record;
        
        $config = $record->timeConfig;

        $this->slots = $config->slots()
            ->join('lesson_time', 'time_slots.lesson_time_id', '=', 'lesson_time.id')
            ->with(['day', 'lessonTime'])
            ->orderBy('time_slots.day_id', 'asc')
            ->orderBy('lesson_time.start', 'asc')
            ->select('time_slots.*') // Garante que não sobrescreva o ID do slot com o ID do lesson_time
            ->get();

        $this->enabledSlots = $this->slots->map(function ($slot) {
            return "{$slot->day_id}-{$slot->lesson_time_id}";
        })->toArray();

        $this->timeSlots = $this->slots->mapWithKeys(function ($slot) {
            $start = substr($slot->lessonTime->start, 0, 5);
            $end = substr($slot->lessonTime->end, 0, 5);
            $label = "{$start} - {$end}";

            return [$slot->lesson_time_id => $label];
        })
        ->unique() 
        ->toArray();

        $this->days = $this->slots->pluck('day.name', 'day.id')->unique()->sortKeys()->toArray();
        // $this->saturdayTimes = TimeDay::getTimesByDay(7);

        $this->subjects = Curso::find($record->course_id)
            ?->getComponentsByModule($record->module_id);
        
        $this->rooms = Room::getRoomsArray();

        $existingItems = $record->items()
            ->with('timeSlots')
            ->get();

        foreach ($existingItems as $item) {

            $slot = $item->timeSlots;
            $day = $slot->day_id;
            $time = $slot->lesson_time_id;
            $group = $item->group ?? 'A';

            if (!empty($group)) {
                $this->scheduleData[$day][$time]['groups'][$group] = [
                    'subject_id' => $item->component ?? '',
                    'teacher_id' => $item->instructor ?? '',
                    'room_id' => $item->room ?? '',
                ];

                if (!empty($item->component) && isset($this->subjects[$item->component]['instructors'])) {
                    $this->scheduleData[$day][$time]['groups'][$group]['available_teachers'] = $this->subjects[$item->component]['instructors'] ?? [];
                }
            }else{
                $this->scheduleData[$day][$time] = [
                    'subject_id' => $item->component ?? '',
                    'teacher_id' => $item->instructor ?? '',
                    'room_id' => $item->room ?? '',
                ];
    
                if (!empty($item->component) && isset($this->subjects[$item->component]['instructors'])) {
                    $this->scheduleData[$item->day][$item->time]['available_teachers'] = $this->subjects[$item->component]['instructors'] ?? [];
                }
            }
        }

    }

    public function saveSchedule(): void
    {
        DB::transaction(function () {

            $this->getSanitizedScheduleData();

            $scheduleId = $this->record['id'];

            activity()->withoutLogs(function () use ($scheduleId) {
                ClassSchedule::where('schedule_id', $scheduleId)->delete();
            });

            foreach ($this->scheduleData as $day => $times) {
                foreach ($times as $timeId => $data) {
                    $slot = $this->slots->where('day_id', $day)
                                   ->where('lesson_time_id', $timeId)
                                   ->first();
                    
                    if (!$slot) continue;

                    $this->record->items()
                        ->where('slot_id', $slot->id)
                        ->delete();

                    $sanitize = fn($value) => (blank($value) || $value === 'null') ? null : $value;

                    activity()->withoutLogs(function () use ($data, $slot, $sanitize) {
                        foreach ($data['groups'] as $groupLetter => $groupData) {
                            $this->record->items()->create([
                                'slot_id'    => $slot->id,
                                'group'      => $groupLetter,
                                'component'  => $sanitize($groupData['subject_id'] ?? null),
                                'instructor' => $sanitize($groupData['teacher_id'] ?? null),
                                'room'       => $sanitize($groupData['room_id'] ?? null),
                            ]);
                        }
                    });

                    activity()
                        ->performedOn(Schedule::find($scheduleId))
                        ->causedBy(auth()->user())
                        ->event('updated')
                        ->log("Atualizou a grade do horário {$scheduleId}.");
                }
            }
        });

        \Filament\Notifications\Notification::make()
            ->title('Grade salva com sucesso!')
            ->success()
            ->send();
    }

    protected function getSanitizedScheduleData(): void
    {
        foreach ($this->scheduleData as $dayId => &$times) {
            foreach ($times as $timeId => &$data) {
                foreach ($data['groups'] as $groupLetter => &$groupData) {
                    
                    $subjectId = $groupData['subject_id'] ?? null;

                    if (blank($subjectId) || $subjectId === 'null') {
                        $groupData['teacher_id'] = '';
                    }
                }
            }
        }

        unset($groupData, $data, $times);
    }
    
    public function makeSchedule(): void
    {
        DB::transaction(function () {
            $this->scheduleData = [];
            $rooms = \App\Models\Room::where('active', true)->get();
            
            $subjectIds = array_keys($this->subjects);
            $componentesBanco = Componente::whereIn('id', $subjectIds)->get();

            $pendingComponents = collect($this->subjects)->map(function ($subject, $id) use ($componentesBanco) {
                $compData = $componentesBanco->firstWhere('id', $id);

                return [
                    'id' => $id,
                    'name' => $subject['name'],
                    'remaining_hours' => (int) ($compData->horasSemanais ?? 4),
                    'instructors' => $subject['instructors'] ?? [],
                ];

            })->sortByDesc('remaining_hours')->toArray();

            // 3. Percorrer os Slots Disponíveis (Dias e Horários cadastrados na Config)
            foreach ($this->slots as $slot) {
                $dayId = $slot->day_id;
                $timeId = $slot->lesson_time_id;

                // Tentar encaixar cada componente por ordem de prioridade
                foreach ($pendingComponents as &$component) {
                    if ($component['remaining_hours'] <= 0){
                        unset($component);
                        continue;
                    }

                    // 4. Validar Professor Disponível
                    $selectedTeacher = null;
                    foreach ($component['instructors'] as $teacherId => $teacherName) {
                        // Verifica na tabela de disponibilidade se o professor pode nesse dia/hora
                        $isAvailable = \DB::table('availability_instructor')
                            ->where('user_id', $teacherId)
                            ->where('day_id', $dayId)
                            ->where('time_id', $timeId)
                            ->exists();
                        
                        // Verificar se o professor já não foi alocado em outro curso no mesmo horário
                        $isBusy = \App\Models\ClassSchedule::whereHas('timeSlots', function($q) use ($dayId, $timeId) {
                                $q->where('day_id', $dayId)->where('lesson_time_id', $timeId);
                            })
                            ->where('instructor', $teacherId)
                            ->exists();

                        if ($isAvailable && !$isBusy) {
                            $selectedTeacher = $teacherId;
                            break;
                        }
                    }

                    // 5. Atribuir se encontrou professor e tem sala livre
                    if ($selectedTeacher) {
                        // Pega a primeira sala que não esteja ocupada neste slot
                        $occupiedRooms = \App\Models\ClassSchedule::whereHas('timeSlots', function($q) use ($dayId, $timeId) {
                                $q->where('day_id', $dayId)->where('lesson_time_id', $timeId);
                            })->pluck('room')->toArray();

                        $availableRoom = $rooms->whereNotIn('id', $occupiedRooms)->first();

                        if ($availableRoom) {
                            $this->scheduleData[$dayId][$timeId]['groups']['A'] = [
                                'subject_id' => $component['id'],
                                'teacher_id' => $selectedTeacher,
                                'room_id' => $availableRoom->id,
                                'available_teachers' => $component['instructors'],
                            ];

                            $component['remaining_hours']--;

                            continue 2; 
                        }
                    }
                }
            }
        });

        $this->scheduleVersion++;

        \Filament\Notifications\Notification::make()
            ->title('Grade sugerida com sucesso!')
            ->body('Lembre-se de revisar e clicar em Salvar.')
            ->success()
            ->send();
    }

    private function contarAulasAtribuidas($horario, $componenteId) {
        $count = 0;
        foreach ($horario as $dia) {
            foreach ($dia as $hora) {
                if (($hora['groups']['A']['subject_id'] ?? null) == $componenteId) $count++;
            }
        }
        return $count;
    }

    public function splitGroup($day, $timeId)
    {
        if (!isset($this->scheduleData[$day][$timeId]['groups'])) {
            $this->scheduleData[$day][$timeId]['groups'] = ['A' => $this->scheduleData[$day][$timeId]['groups']['A'] ?? []];
        }

        $groups = $this->scheduleData[$day][$timeId]['groups'];

        if (count($groups) === 1) {
            $this->scheduleData[$day][$timeId]['groups'] = ['A' => $this->scheduleData[$day][$timeId]['groups']['A'] ?? [], 'B' => []];
        }
    }

    public function mergeGroups($day, $timeId)
    {
        if (isset($this->scheduleData[$day][$timeId]['groups'])) {
            $this->scheduleData[$day][$timeId]['groups'] = [
                'A' => $this->scheduleData[$day][$timeId]['groups']['A'] ?? []
            ];
        }
    }

    public function onSubjectChanged($day, $timeId, $group, $subjectId)
    {
        $this->updateTeachersForCell($day, $timeId, $group, $subjectId);
    }

    public function updateTeachersForCell($day, $timeId, $group, $subjectId)
    {
        $instructors = $this->subjects[$subjectId]['instructors'] ?? [];
        $this->scheduleData[$day][$timeId]['groups'][$group]['available_teachers'] = $instructors;
    }

    protected function getHeaderActions(): array
    {
        return [
            PageActions\Action::make('back')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => ScheduleResource::getUrl('index', ['record' => $this->record])),
                
            HelpButton::make('manage-schedules'),
        ];
    }
}
