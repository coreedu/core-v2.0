<?php

namespace App\Filament\Resources\GroupEquipmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use App\Models\Inventory\Equipment;

class EquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipments';

    protected static ?string $title = 'Equipamentos do Grupo';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Equipamento')
                ->required(),

            Forms\Components\Select::make('brand_id')
                ->relationship('brand', 'name')
                ->label('Marca')
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('type_id')
                ->relationship('type', 'name')
                ->label('Tipo')
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('patrimony')
                ->label('Patrimônio'),

            Forms\Components\Toggle::make('status')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->headerActions([
                Action::make('addEquipment')
                    ->label('Adicionar equipamento')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Adicionar equipamento ao grupo')
                    ->modalSubmitActionLabel('Salvar')
                ->form([
                    Forms\Components\Radio::make('mode')
                        ->label('O que deseja fazer?')
                        ->options([
                            'existing' => 'Vincular equipamento existente',
                            'new' => 'Criar novo equipamento',
                        ])
                        ->default('existing')
                        ->reactive()
                        ->required(),

                    // ---- EXISTENTE ----
                    Forms\Components\Select::make('equipment_id')
                        ->label('Equipamento existente')
                        ->options(
                            fn () => Equipment::query()
                                ->whereNull('group_equipment_id')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required()
                        ->visible(fn ($get) => $get('mode') === 'existing'),

                    // ---- NOVO ----
                    Forms\Components\TextInput::make('name')
                        ->label('Nome do equipamento')
                        ->required()
                        ->visible(fn ($get) => $get('mode') === 'new'),

                    Forms\Components\Select::make('brand_id')
                        ->label('Marca')
                        ->relationship('brand', 'name')
                        ->searchable()
                        ->visible(fn ($get) => $get('mode') === 'new'),

                    Forms\Components\Select::make('type_id')
                        ->label('Tipo')
                        ->relationship('type', 'name')
                        ->searchable()
                        ->visible(fn ($get) => $get('mode') === 'new'),

                    Forms\Components\Toggle::make('status')
                        ->label('Ativo')
                        ->default(true)
                        ->visible(fn ($get) => $get('mode') === 'new'),
                ])
                ->action(function (array $data) {
                    if ($data['mode'] === 'existing') {
                        Equipment::where('id', $data['equipment_id'])
                            ->update([
                                'group_equipment_id' => $this->getOwnerRecord()->id,
                            ]);
                    }

                    if ($data['mode'] === 'new') {
                        Equipment::create([
                            'name' => $data['name'],
                            'brand_id' => $data['brand_id'] ?? null,
                            'type_id' => $data['type_id'] ?? null,
                            'status' => $data['status'] ?? true,
                            'group_equipment_id' => $this->getOwnerRecord()->id,
                        ]);
                    }
                }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Equipamento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('patrimony')
                    ->label('Patrimônio'),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca'),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),
            ])
            // ->headerActions([
            //     Tables\Actions\CreateAction::make()
            //         ->label('Adicionar equipamento'),
            // ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Remover do grupo')
                    ->before(function ($record) {
                        $record->update(['group_equipment_id' => null]);
                    }),
            ]);
    }
}