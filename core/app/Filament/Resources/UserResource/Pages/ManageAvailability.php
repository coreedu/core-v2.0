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

        $availabilities = \DB::table('availability_instructor')
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('day_id');

        $timeDay = \DB::table('time_day')->get()->groupBy('day_id');
    }

    public function form(Form $form): Form
    {
        $days = Day::all();
        $times = LessonTime::orderBy('start')->get();

        // Cabeçalhos das colunas (dias)
        $dayNames = $days->pluck('name', 'id');

        return $form->schema([
            Section::make('Disponibilidade Semanal')
                ->description('Marque os horários disponíveis. Cada linha é um horário, cada coluna um dia.')
                ->schema(function () use ($days, $times, $dayNames) {
                    $rows = [];

                    foreach ($times as $time) {
                        $columns = [];

                        $start = \Carbon\Carbon::createFromFormat('H:i:s', $time->start)->format('H:i');
                        $end = \Carbon\Carbon::createFromFormat('H:i:s', $time->end)->format('H:i');

                        // Primeira célula: exibe o horário
                        $columns[] = Placeholder::make("hora_{$time->id}")
                            ->label('')
                            ->content("{$start} - {$end}");

                        // Demais células: checkboxes para cada dia
                        foreach ($days as $day) {
                            $columns[] = Checkbox::make("matrix.{$day->id}.{$time->id}")
                                ->label('')
                                ->inline(false);
                        }

                        // Adiciona uma linha (Grid com 1 + qtd dias colunas)
                        $rows[] = Grid::make(count($columns))
                            ->schema($columns);
                    }

                    // Cabeçalho de dias (linha superior)
                    $headerCells = [];
                    $headerCells[] = Placeholder::make('blank_header')->label('');
                    foreach ($dayNames as $name) {
                        $headerCells[] = Placeholder::make("day_header_{$name}")
                            ->label('')
                            ->content($name);
                    }

                    // ✅ Rodapé com botões "Selecionar todos"
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
                                }),
                            ]);
                    }

                    return [
                        // Linha de cabeçalho
                        Grid::make(count($headerCells))
                            ->schema($headerCells),

                        // Linhas de horários
                        ...$rows,

                        // Rodapé com botões
                        Grid::make(count($footerCells))
                            ->schema($footerCells),
                    ];
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
            PageActions\Action::make('back')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => UserResource::getUrl('edit', ['record' => $this->record])),

            PageActions\Action::make('list')
                ->label('Voltar para Lista')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->url(fn () => UserResource::getUrl('index'))
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
