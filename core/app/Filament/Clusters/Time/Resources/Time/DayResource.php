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
                Forms\Components\Section::make('Informações do Dia')
                    ->description('Defina o código e o nome do dia.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('cod')
                            ->label('Código')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(99)
                            ->placeholder('01')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Dia')
                            ->placeholder('Ex.: Segunda-feira')
                            ->required()
                            ->maxLength(20)
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Dia')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable(),
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver horários')
                    ->icon('heroicon-o-eye')
                    ->button()
                    ->color('primary')
                    ->modalHeading(fn($record) => "Horários — {$record->name}")
                    ->modalSubmitActionLabel('Salvar')
                    ->modalCancelActionLabel('Fechar')
                    ->modalWidth('3xl')
                    ->form([
                        Forms\Components\CheckboxList::make('times')
                            ->label('Horários disponíveis')
                            ->relationship('times', 'start')
                            ->getOptionLabelFromRecordUsing(fn($record) => method_exists($record, 'getLabelAttribute') ? $record->getLabelAttribute() : ($record->start ?? ''))
                            ->columns(2)
                            ->bulkToggleable()
                            ->searchable()
                            ->helperText('Selecione os horários que pertencem a este dia.'),
                    ])
                    ->mountUsing(function (Forms\Form $form, Day $record) {
                        $form->fill([
                            'times' => $record->times()->pluck('lesson_time.id')->all(),
                        ]);
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Excluir')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
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
