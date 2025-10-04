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
                    ->options([
                        1 => '1º',
                        2 => '2º',
                        3 => '3º',
                        4 => '4º',
                        5 => '5º',
                        6 => '6º',
                    ]),
                Select::make('modality_id')
                    ->label('Modalidade')
                    ->relationship('modality', 'name')
                    ->preload()
                    ->searchable()
                    ->live(),
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
                    Tables\Columns\TextColumn::make('shift.name')
                        ->label('Turno')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('modality.name')
                        ->label('Modalidade')
                        ->searchable()
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
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
