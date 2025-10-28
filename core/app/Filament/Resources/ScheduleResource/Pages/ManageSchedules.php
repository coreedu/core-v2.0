<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\Page;
use App\Models\Schedule;
use App\Models\Curso;
use App\Models\Room;
use App\Models\Time\Day;
use App\Models\TimeShift;

class ManageSchedules extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static string $view = 'filament.resources.schedule-resource.pages.manage-schedules';
    protected static ?string $title = 'Gerenciar Grades Horárias';

    // public Collection $timeSlots;
    // public Collection $subjects;
    // public Collection $teachers;
    // public Collection $rooms;
    public array $scheduleData = [];

    public function mount(Schedule $record): void
    {
        $this->record = $record;
        
        $this->days = Day::all()->pluck('name', 'cod')->toArray();
        
        $this->timeSlots = TimeShift::getTimesByShift($record->shift_cod);
        
        $this->subjects = Curso::find($record->course_id)
            ?->getComponentsByModule($record->module_id);
        
        $this->rooms = Room::getRoomsArray();
    }

    public function saveSchedule(): void
    {
        foreach ($this->scheduleData as $day => $times) {
            foreach ($times as $timeId => $data) {
                if (isset($data['subject_id'])) {
                    $this->record->items()->updateOrCreate(
                        [
                            'day_of_week' => $day,
                            'time_id' => $timeId,
                        ],
                        [
                            'subject_id' => $data['subject_id'] ?? null,
                            'teacher_id' => $data['teacher_id'] ?? null,
                            'room_id' => $data['room_id'] ?? null,
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
}
