<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\Page;
use App\Models\Schedule;
use App\Models\Curso;
use App\Models\Room;
use App\Models\Time\Day;
use App\Models\TimeShift;
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
    public array $subjects = [];

    public function mount(Schedule $record): void
    {
        $this->record = $record;
        
        $this->days = Day::all()->pluck('name', 'cod')->toArray();
        $this->timeSlots = TimeShift::getTimesByShift($record->shift_cod);
        
        $this->subjects = Curso::find($record->course_id)
            ?->getComponentsByModule($record->module_id);
        
        $this->rooms = Room::getRoomsArray();

        $existingItems = $record->items()
            ->select('day', 'time', 'component', 'instructor', 'room')
            ->get();

        foreach ($existingItems as $item) {
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

    public function saveSchedule(): void
    {

        dd($this->scheduleData);

        // Formatar dados para salvar
        foreach ($this->scheduleData as $day => $times) {
            foreach ($times as $timeId => $data) {
                if (isset($data['subject_id'])) {
                    $this->record->items()->updateOrCreate(
                        [
                            'day' => $day,
                            'time' => $timeId,
                        ],
                        [
                            'component' => !empty($data['subject_id']) ? $data['subject_id'] : null,
                            'instructor' => !empty($data['teacher_id']) ? $data['teacher_id'] : null,
                            'room' => !empty($data['room_id']) ? $data['room_id'] : null,
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
            // Preserva dados da Turma A (ou mescla se quiser lógica de priorização)
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
