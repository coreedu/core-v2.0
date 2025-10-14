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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Button;
use Filament\Actions as PageActions;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action as FormAction;

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

        $this->availabilities = \DB::table('availability_instructor')
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('day_id')
            ->map(function ($times) {
                return $times->pluck('time_id')->mapWithKeys(fn ($timeId) => [$timeId => true]);
            })
            ->toArray();

        $timeDay = \DB::table('time_day')->get()->groupBy('day_id');

        $this->form->fill([
            'matrix' => $this->availabilities,
        ]);
    }

    public function form(Form $form): Form
    {
        $days = Day::all();
        $times = LessonTime::orderBy('start')->get();

        $dayNames = $days->pluck('name', 'id');

        return $form->schema([
            Section::make('Disponibilidade Semanal')
                ->description('Marque os horários disponíveis.')
                ->schema(function () use ($days, $times, $dayNames) {
                    $rows = [];

                    foreach ($times as $time) {
                        $columns = [];

                        $start = \Carbon\Carbon::createFromFormat('H:i:s', $time->start)->format('H:i');
                        $end = \Carbon\Carbon::createFromFormat('H:i:s', $time->end)->format('H:i');

                        $columns[] = Placeholder::make("hora_{$time->id}")
                            ->label('')
                            ->content("{$start} - {$end}");

                        foreach ($days as $day) {
                            $columns[] = Checkbox::make("matrix.{$day->id}.{$time->id}")
                                ->label('')
                                ->inline(false)
                                ->columnSpan(1)
                                ->extraAttributes(['class' => 'mx-auto block']);
                        }

                        $rows[] = Grid::make(count($columns))
                            ->schema($columns);
                    }

                    $headerCells = [];
                    $headerCells[] = Placeholder::make('blank_header')->label('');
                    foreach ($dayNames as $name) {
                        $headerCells[] = Placeholder::make("day_header_{$name}")
                            ->label('')
                            ->content($name)
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'mx-auto block']);
                    }

                    $footerCells = [];
                    $footerCells[] = \Filament\Forms\Components\Placeholder::make('footer_blank')->label('');
                    foreach ($days as $day) {
                        $footerCells[] = FormActions::make([
                            FormAction::make("select_all_day_{$day->id}")
                                ->label('Selecionar todos')
                                ->color('primary')
                                ->size('xs')
                                ->action(function () use ($day) {
                                    $this->toggleDay($day->id);
                                })
                                ->extraAttributes(['class' => 'mx-auto block']),
                            ]);
                    }

                    return [
                        Grid::make(count($footerCells))
                            ->schema($footerCells),

                        Grid::make(count($headerCells))
                            ->schema($headerCells),


                        ...$rows,

                    ];
                }),
        ]);
    }

    public function submit(): void
    {
        $user = auth()->user();
        $data = $this->form->getState()['matrix'] ?? [];

        AvailabilityInstructor::where('user_id', $user->id)->delete();

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
            PageActions\Action::make('back')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => UserResource::getUrl('edit', ['record' => $this->record])),

            PageActions\Action::make('list')
                ->label('Voltar para Lista')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->url(fn () => UserResource::getUrl('index')),
                
            PageActions\Action::make('ajuda')
                ->label('Ajuda')
                ->color('primary') // azul padrão do Filament
                ->icon('heroicon-o-question-mark-circle')
                ->button()
                ->modalHeading('Como preencher a disponibilidade')
                ->modalSubheading('Siga as instruções abaixo para configurar sua agenda corretamente:')
                // ->modalContent(fn () => new \Filament\Forms\Components\ViewField(view: '
                //     <div class="space-y-3">
                //         <p>Use esta página para indicar os horários em que você está disponível para aulas.</p>
                //         <ul class="list-disc pl-5 text-sm text-gray-700">
                //             <li>Marque as caixas de seleção para os horários disponíveis.</li>
                //             <li>Use o botão “Selecionar todos” para marcar todos os horários de um dia.</li>
                //             <li>As alterações serão salvas automaticamente ao enviar o formulário.</li>
                //         </ul>
                //         <p class="text-sm text-gray-600 mt-2">
                //             Dica: mantenha seu calendário sempre atualizado para evitar conflitos de agendamento.
                //         </p>
                //     </div>
                // '))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar'),
        ];
    }

    public function toggleDay(int $dayId): void
    {
        $allSelected = collect($this->matrix[$dayId] ?? [])->every(fn($checked) => $checked);

        foreach ($this->matrix[$dayId] ?? [] as $timeId => $checked) {
            $this->matrix[$dayId][$timeId] = !$allSelected;
        }

        // força o Livewire a atualizar o formulário
        $this->form->fill(['matrix' => $this->matrix]);

        $this->dispatch('refreshForm'); // força re-render
    }
}
