<?php

namespace App\Filament\Clusters\Time\Resources\Time;

use App\Filament\Clusters\Time;
use App\Filament\Clusters\Time\Resources\Time\DayResource\Pages;
use App\Filament\Clusters\Time\Resources\Time\DayResource\RelationManagers;
use App\Models\Time\Day;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class DayResource extends Resource
{
    protected static ?string $model = Day::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $cluster = Time::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cod')
                ->label('Código')
                ->integer()
                ->maxlength(2)
                ->placeholder(1)
                ->required()
                ->columnSpan(4),

                Forms\Components\TextInput::make('name')
                ->label('Nome')
                ->required()
                ->columnSpan(4)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cod')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => "Horários {$record->name}")
                    ->form([
                        Forms\Components\CheckboxList::make('times')
                            ->label('Horários')
                            ->relationship('times', 'start')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getLabelAttribute())
                            ->columns(2)
                            ->bulkToggleable()
                    ])
                    ->mountUsing(function (Forms\Form $form, Day $record) {
                        $form->fill([
                            'times' => $record->times()->pluck('lesson_time.id')->all(),
                        ]);
                    })
                    ->modalSubmitActionLabel('Salvar'),
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
            'index' => Pages\ManageDays::route('/'),
        ];
    }
}
