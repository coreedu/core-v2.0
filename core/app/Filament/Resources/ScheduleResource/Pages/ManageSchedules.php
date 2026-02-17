<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\Page;
use App\Models\Schedule;
use App\Models\Curso;
use App\Models\Room;
use App\Models\User;
use App\Models\ClassSchedule;
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
    public array $rooms = [];

    protected $listeners = ['subjectChanged' => 'onSubjectChanged'];

    public array $scheduleData = [];
    public array $saturdayTimes = [];
    public array $subjects = [];

    public function mount(Schedule $record): void
    {
        $this->record = $record;
        
        $config = $record->timeConfig;

        $this->slots = $config->slots()
            ->with(['day', 'lessonTime']) 
            ->get()
            ->sortBy(fn($slot) => $slot->lessonTime->start);

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
    
    public function makeSchedule(): array
    {

        $course = $this->record->course_id;
        $module = $this->record->module_id;

        $config = $this->record->timeConfig;
        $slots = $config->slots()
            ->with(['day', 'lessonTime'])
            ->join('day', 'time_slots.day_id', '=', 'day.id') // Join para garantir a ordenação pelo ID do dia
            ->join('lesson_time', 'time_slots.lesson_time_id', '=', 'lesson_time.id') // Join para ordenar por hora
            ->orderBy('day.id', 'asc') 
            ->orderBy('lesson_time.start', 'asc')
            ->select('time_slots.*')
            ->get();

        $componentes = Curso::find($course)
            ->componentes()
            ->wherePivot('module', $module)
            ->get();

        $instrutores = User::whereHas('components', function($q) use ($componentes) {
                $q->whereIn('componentes.id', $componentes->pluck('id'));
            })
            ->with(['availabilities', 'components'])
            ->get();

            
        $salas = Room::where('active', true)->get();

        $horarioGerado = [];
        foreach ($slots as $slot) {

            $diaId = $slot->day_id;
            $horaId = $slot->lesson_time_id;
            $count = 0;
            foreach ($componentes as $comp) {
            $count++;
                $aulasJaAtribuidas = $this->contarAulasAtribuidas($horarioGerado, $comp->id);
                if ($aulasJaAtribuidas >= $comp->horasSemanais) continue;

                $instrutorElegivel = $instrutores->filter(function($professor) use ($comp, $diaId, $horaId) {
                    $temComponente = $professor->components->contains('id', $comp->id);

                    $estaDisponivel = $professor->availabilities
                        ->where('id', $horaId)
                        ->where('pivot.day_id', $diaId)
                        ->isNotEmpty();
                    
                    return $temComponente && $estaDisponivel;
                })->first();

        
                if ($instrutorElegivel) {
                    $horarioGerado[$diaId][$horaId]['groups']['A'] = [
                        'subject_id' => $comp->id,
                        'teacher_id' => $instrutorElegivel->id,
                        'room_id' => $salas->first()->id ?? null,
                        'slot_id' => $slot->id
                    ];

                    continue 2; 
                }
            }
        }

        $this->scheduleData = $horarioGerado;
        return $this->scheduleData;
        
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
