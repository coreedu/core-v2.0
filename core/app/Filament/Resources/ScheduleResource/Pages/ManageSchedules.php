<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\Page;
use App\Models\Schedule;
use App\Models\Curso;
use App\Models\Room;
use App\Models\Time\Day;
use App\Models\Time\TimeSlots;
use App\Models\TimeDay;
use Filament\Actions as PageActions;
use App\Filament\Components\HelpButton;

class ManageSchedules extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static string $view = 'filament.resources.schedule-resource.pages.manage-schedules';
    protected static ?string $title = 'Gerenciar Grades Horárias';

    public Schedule $record;
    public array $days = [];
    public array $timeSlots = [];
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
            ->get();

        $this->timeSlots = $this->slots->map(function ($slot) {
            return substr($slot->lessonTime->start, 0, 5) . ' - ' . substr($slot->lessonTime->end, 0, 5);
        })->unique()->toArray();

        $this->days = $this->slots->pluck('day.name')->unique()->toArray();
        
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
                    'subject_id' => $item->component,
                    'teacher_id' => $item->instructor,
                    'room_id' => $item->room,
                ];

                if (!empty($item->component) && isset($this->subjects[$item->component]['instructors'])) {
                    $this->scheduleData[$item->day][$item->time]['groups'][$group]['available_teachers'] = $this->subjects[$item->component]['instructors'] ?? [];
                }
            }else{
                $this->scheduleData[$item->day][$item->time] = [
                    'subject_id' => $item->component,
                    'teacher_id' => $item->instructor,
                    'room_id' => $item->room,
                ];
    
                if (!empty($item->component) && isset($this->subjects[$item->component]['instructors'])) {
                    $this->scheduleData[$item->day][$item->time]['available_teachers'] = $this->subjects[$item->component]['instructors'] ?? [];
                }
            }
        }

    }

    public function saveSchedule(): void
    {
        foreach ($this->scheduleData as $day => $times) {
            foreach ($times as $timeId => $data) {
                $this->record->items()
                            ->where('day', $day)
                            ->where('time', $timeId)
                            ->whereNotNull('group')
                            ->delete();

                foreach ($data['groups'] as $groupLetter => $groupData) {
                    $this->record->items()->updateOrCreate(
                        [
                            'day' => $day,
                            'time' => $timeId,
                            'group' => $groupLetter,
                        ],
                        [
                            'component' => !empty($groupData['subject_id']) ? $groupData['subject_id'] :null,
                            'instructor' => !empty($groupData['teacher_id']) ? $groupData['teacher_id'] : null,
                            'room' => !empty($groupData['room_id']) ? $groupData['room_id'] : null,
                        ]
                    );
                }
            }
        }

        \Filament\Notifications\Notification::make()
            ->title('Grade salva com sucesso!')
            ->success()
            ->send();
    }

    public function splitGroup($day, $timeId)
    {
        if (!isset($this->scheduleData[$day][$timeId]['groups'])) {
            $this->scheduleData[$day][$timeId]['groups'] = ['A' => []];
        }

        $groups = $this->scheduleData[$day][$timeId]['groups'];

        if (count($groups) === 1) {
            $this->scheduleData[$day][$timeId]['groups'] = ['A' => [], 'B' => []];
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
