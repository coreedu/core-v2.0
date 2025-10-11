<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Form;
use App\Models\Time\Day;
use App\Models\Time\LessonTime;
use App\Models\AvailabilityInstructor;
use Filament\Notifications\Notification;
use Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Checkbox;
use Filament\Actions;

class ManageAvailability extends Page
{

    public ?\App\Models\User $record = null;

    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.user-resource.pages.manage-availability';
    protected static ?string $title = 'Gerenciar Disponibilidade';

    public $matrix = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->matrix = [];

        $days = Day::all();
        $times = LessonTime::all();

        // foreach ($days as $day) {
        //     foreach ($times as $time) {
        //         $exists = AvailabilityInstructor::where([
        //             'user_id' => $user->id,
        //             'day_id' => $day->id,
        //             'time_id' => $time->id,
        //         ])->exists();

        //         $this->matrix[$day->id][$time->id] = $exists;
        //     }
        // }

        $availabilities = \DB::table('availability_instructor')
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('day_id');

        $timeDay = \DB::table('time_day')->get()->groupBy('day_id');
    }

    public function form(Form $form): Form
    {
        $days = Day::all();
        $times = LessonTime::all();

        return $form->schema([
            Grid::make(count($days) + 1)
                ->schema(function () use ($days, $times) {
                    $columns = [];

                    foreach ($days as $day) {
                        $columns[] = Fieldset::make($day->name)
                            ->schema(array_map(function ($time) use ($day) {
                                return Checkbox::make("matrix.{$day->id}.{$time->id}")
                                    ->label("{$time->start} - {$time->end}");
                            }, $times->all()));
                    }

                    return $columns;
                }),
        ]);
    }

    public function submit(): void
    {
        $user = auth()->user();
        $data = $this->form->getState()['matrix'] ?? [];

        AvailabilityInstructor::where('user_id', $user->id)->delete();

        // Recria as novas disponibilidades
        foreach ($data as $dayId => $times) {
            foreach ($times as $timeId => $checked) {
                if ($checked) {
                    AvailabilityInstructor::create([
                        'user_id' => $user->id,
                        'day_id' => $dayId,
                        'time_id' => $timeId,
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Disponibilidade atualizada com sucesso!')
            ->success()
            ->send();
    }

    public function getRecord()
    {
        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => UserResource::getUrl('edit', ['record' => $this->record])),

            Actions\Action::make('list')
                ->label('Voltar para Lista')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->url(fn () => UserResource::getUrl('index'))
        ];
    }
}
