<?php

namespace App\Filament\Clusters\Time\Resources\Time;

use App\Filament\Clusters\Time;
use App\Filament\Clusters\Time\Resources\Time\LessonTimeResource\Pages;
use App\Filament\Clusters\Time\Resources\Time\LessonTimeResource\RelationManagers;
use Filament\Resources\Resource;
use App\Models\Time\LessonTime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;

class LessonTimeResource extends Resource
{
    protected static ?string $model = LessonTime::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $cluster = Time::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TimePicker::make('start')
                    ->label('Hora de Início')
                    ->required()
                    ->default('01:00:00')
                    ->columnSpan(4),

                Forms\Components\TimePicker::make('end')
                    ->label('Hora de Término')
                    ->required()
                    ->default('01:00:00')
                    ->columnSpan(4),
                
                Select::make('shift')
                            ->label('Turnos')
                            ->relationship('shift', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->columnSpanFull()
                            ->live()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('start')
                    ->label('Início')
                    ->searchable()
                    ->sortable()
                    ->date('H:i'),

                TextColumn::make('end')
                    ->label('Término')
                    ->searchable()
                    ->date('H:i'),

                TextColumn::make('shift.name')
                    ->label('Turnos')
                    ->separator(', ')
                    ->searchable()
            ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLessonTimes::route('/'),
        ];
    }
}
