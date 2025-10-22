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
    protected static ?string $navigationLabel = 'Horários';
    protected static ?string $pluralModelLabel = 'Horários';
    protected static ?string $modelLabel = 'Horário';

    protected static ?string $cluster = Time::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TimePicker::make('start')
                    ->label('Início')
                    ->seconds(false)
                    ->native(false)
                    ->required()
                    ->columnSpan(1),

                Forms\Components\TimePicker::make('end')
                    ->label('Término')
                    ->seconds(false)
                    ->native(false)
                    ->required()
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('range')
                        ->label('Horário')
                        ->getStateUsing(function ($record) {
                            $s = is_string($record->start) ? substr($record->start, 0, 5) : $record->start?->format('H:i');
                            $e = is_string($record->end) ? substr($record->end, 0, 5) : $record->end?->format('H:i');
                            return "{$s} — {$e}";
                        })
                        ->weight('bold')
                        ->size('lg')
                        ->sortable(query: fn($query, string $direction) => $query->orderBy('start', $direction)),

                    TextColumn::make('shift.name')
                        ->label('Turnos')
                        ->separator(', ')
                        ->limit(40),
                ])->space(1),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
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
