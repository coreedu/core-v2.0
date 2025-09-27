<?php

namespace App\Filament\Clusters\Time\Resources\Time;

use App\Filament\Clusters\Time;
use App\Filament\Clusters\Time\Resources\Time\ShiftResource\Pages;
use App\Models\Time\Shift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Turnos';
    protected static ?string $pluralModelLabel = 'Turnos';
    protected static ?string $modelLabel = 'Turno';
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = Time::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
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
                    ->label('Nome do Turno')
                    ->placeholder('Ex.: Noturno')
                    ->required()
                    ->maxLength(30)
                    ->columnSpan(3),

                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->placeholder('Breve descrição do turno')
                    ->maxLength(100)
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('cod')
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label('Turno')
                        ->weight('bold')
                        ->size('lg')
                        ->sortable()
                        ->searchable(),

                    TextColumn::make('description')
                        ->label('Descrição')
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
                Tables\Actions\EditAction::make()->label('Editar')->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()->label('Excluir')->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Excluir selecionados'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShifts::route('/'),
        ];
    }
}
