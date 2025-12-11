<?php
namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class GroupEquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'groupEquipments';

    protected static ?string $title = 'Grupos de Equipamentos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome do Grupo')
                ->required(),

            Forms\Components\Toggle::make('status')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Grupo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('equipments_count')
                    ->label('Equipamentos')
                    ->counts('equipments')
                    ->badge(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
