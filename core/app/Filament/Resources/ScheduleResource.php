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
                    ->unique(
                        table: 'schedule',  
                        column: 'version',
                        ignoreRecord: true,  // Ignorar o registro atual ao verificar a unicidade
                    ),
                Select::make('course_id')
                    ->label('Curso')
                    ->relationship('course', 'nome')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live(),
                Select::make('shift_cod')
                        ->label('turno')
                        ->relationship('shift', 'name')
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
                Forms\Components\Toggle::make('status')
                    ->label('Publicado')
                    ->default(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->beforeStateUpdated(function ($state, $record) {
                        

                        if ($state && $record) {
                            // Check if there's already a published schedule for the same course and module
                            $existingPublished = \App\Models\Schedule::where('course_id', $record->course_id)
                                ->where('module_id', $record->module_id)
                                ->where('status', true)
                                ->where('id', '!=', $record->id)
                                ->exists();
                            
                            if ($existingPublished) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erro ao publicar')
                                    ->body('Já existe uma grade horária publicada para este curso e módulo.')
                                    ->danger()
                                    ->send();
                                
                                return false; // Prevent the state change
                            }
                        }
                        
                        return $state;
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
                    Tables\Columns\TextColumn::make('shift.name')
                        ->label('Turno')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('modality.name')
                        ->label('Modalidade')
                        ->searchable(),
                    Tables\Columns\ToggleColumn::make('status')
                        ->label('Status')
                        ->onColor('success')
                        ->offColor('danger')
                        ->onIcon('heroicon-o-check-circle')
                        ->offIcon('heroicon-o-x-circle')
                        ->sortable()
                        ->beforeStateUpdated(function ($record, $state) {
                            
                            if ($state) {
                                // Check if there's already a published schedule for the same course and module
                                $existingPublished = \App\Models\Schedule::where('course_id', $record->course_id)
                                    ->where('module_id', $record->module_id)
                                    ->where('status', true)
                                    ->where('id', '!=', $record->id)
                                    ->exists();
                                
                                if ($existingPublished) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Erro ao publicar')
                                        ->body('Já existe uma grade horária publicada para este curso e módulo.')
                                        ->danger()
                                        ->send();
                                    
                                    return false; // Prevent the state change
                                }
                            }
                            
                            return $state;
                        }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativo')
                    ->falseLabel('Inativo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
        ];
    }
}
