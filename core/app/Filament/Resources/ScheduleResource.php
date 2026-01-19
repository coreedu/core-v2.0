<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ScheduleResource\Pages\ManageSchedules;
use Illuminate\Validation\Rule;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Calendário';

    protected static ?string $navigationLabel = 'Grade horária';
    protected static ?string $pluralModelLabel = 'Grades horárias';
    protected static ?string $modelLabel = 'Grade horária';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('version')
                    ->label('Versão')
                    ->required()
                    ->maxLength(50)
                    ->rules(function (Forms\Get $get, ?Forms\Components\Component $component) {
                        return [
                            Rule::unique('schedule', 'version')
                                ->where(fn ($query) => $query
                                    ->where('course_id', $get('course_id'))
                                    ->where('module_id', $get('module_id'))
                                    ->where('time_config_id', $get('time_config'))
                                )
                                ->ignore($component->getRecord()?->id),
                        ];
                    }),

                Select::make('course_id')
                    ->label('Curso')
                    ->relationship('course', 'nome')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live(),
                Select::make('module_id')
                    ->label('Modulo')
                    ->options(function (Forms\Get $get) {
                        $courseId = $get('course_id');
                        if (!$courseId) {
                            return [];
                        }
                        
                        $course = \App\Models\Curso::find($courseId);
                        if (!$course) {
                            return [];
                        }
                        
                        $qtdModulos = (int) $course->qtdModulos;
                        return collect(range(1, max($qtdModulos, 1)))
                            ->mapWithKeys(fn($n) => [$n => "{$n}º Módulo"]);
                    })
                    ->required()
                    ->live(),

                Select::make('modality_id')
                    ->label('Modalidade')
                    ->relationship('modality', 'name')
                    ->preload()
                    ->searchable()
                    ->live(),

                Select::make('time_config_id')
                        ->label('Categoria')
                        ->relationship('timeConfig', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->getFullNameAttribute())
                        ->preload()
                        ->searchable()
                        ->required()
                        ->live(),

                Forms\Components\Toggle::make('status')
                    ->label('Publicado')
                    ->default(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->afterStateUpdated(function ($state, $record, $component) {
                        if ($state && $record) {
                            if (self::existingPublished($record)) {
                                $component->state(false);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Erro ao publicar')
                                    ->body('Já existe uma grade horária publicada para este curso e módulo.')
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('version')
                        ->label('Versão')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('course.nome')
                        ->label('Curso')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('module_id')
                        ->label('Módulo')
                        ->formatStateUsing(fn($state) => "{$state}º Módulo")
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('timeConfig.shift.name')
                        ->label('Turno')
                        ->badge()
                        ->color('info')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('timeConfig.context.name')
                        ->label('Categoria')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('modality.name')
                        ->label('Modalidade')
                        ->searchable(),
                    Tables\Columns\ToggleColumn::make('status')
                        ->label('Publicado')
                        ->onColor('success')
                        ->offColor('danger')
                        ->onIcon('heroicon-o-check-circle')
                        ->offIcon('heroicon-o-x-circle')
                        ->sortable()
                        ->afterStateUpdated(function ($record, $state) {
                            if ($state) {
                                if (self::existingPublished($record)) {
                                    $record->updateQuietly(['status' => false]);
                                    
                                    \Filament\Notifications\Notification::make()
                                        ->title('Erro ao publicar')
                                        ->body('Já existe uma grade horária publicada para este curso e módulo.')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Publicado')
                    ->falseLabel('Não publicado'),
            ])
            ->actions([
                Tables\Actions\Action::make('manage-schedule')
                    ->label('Grade')
                    ->icon('heroicon-o-calendar')
                    ->url(fn ($record) => ManageSchedules::getUrl(['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('gerarPdf')
                    ->label('Gerar PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $schedules = $records;

                        $schedules = Schedule::mountSchedulePdf($schedules);

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.schedule-report', [
                            'schedule' => $schedules,
                        ]);

                        
                        $pdf->setPaper('A4', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'horarios-' . now()->format('d-m-Y_H\hi') . '.pdf');
                    })
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->modalHeading('Gerar PDF')
                    ->modalSubheading('O PDF incluirá todos os horarios selecionados.')
                    ->modalButton('Gerar PDF')
                    ->successNotificationTitle('Horários !'),
                Tables\Actions\BulkAction::make('gerarExcel')
                    ->label('Gerar Excel')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('success')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $data = \App\Models\Schedule::mountSchedulePdf($records);

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ScheduleExport($data), 
                            'horarios-' . now()->format('d-m-Y_H\hi') . '.xlsx'
                        );
                    })
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Planilha Excel')
                    ->modalSubheading('A planilha incluirá todos os horários selecionados com a estrutura de 3 linhas por horário.')
                    ->modalButton('Gerar Excel')
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
            'manage-schedule' => Pages\ManageSchedules::route('/{record}/manage-schedule'),
        ];
    }

    public static function existingPublished($record){
        return Schedule::where('course_id', $record->course_id)
                ->where('module_id', $record->module_id)
                ->where('status', true)
                ->where('id', '!=', $record->id)
                ->where('time_config_id', $record->time_config_id)
                ->exists();
    }

    public static function getPublished()
    {
        return Schedule::getPublished();
    }
}
