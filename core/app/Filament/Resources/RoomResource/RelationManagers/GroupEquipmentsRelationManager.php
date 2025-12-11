<?php
namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Actions\Action;
use App\Models\GroupEquipment;

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
                Action::make('addGroup')
                    ->label('Adicionar grupo')
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading('Adicionar grupo de equipamentos')
                    ->modalSubmitActionLabel('Salvar')
                    ->form([
                        Forms\Components\Radio::make('mode')
                            ->label('O que deseja fazer?')
                            ->options([
                                'existing' => 'Vincular grupo existente',
                                'new' => 'Criar novo grupo',
                            ])
                            ->default('existing')
                            ->reactive()
                            ->required(),

                        // -------- EXISTENTE --------
                        Forms\Components\Select::make('group_equipment_id')
                            ->label('Grupo existente')
                            ->options(
                                fn () => GroupEquipment::query()
                                    ->whereNull('room_id')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->visible(fn ($get) => $get('mode') === 'existing'),

                        // -------- NOVO --------
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do grupo')
                            ->required()
                            ->visible(fn ($get) => $get('mode') === 'new'),

                        Forms\Components\Toggle::make('status')
                            ->label('Ativo')
                            ->default(true)
                            ->visible(fn ($get) => $get('mode') === 'new'),
                    ])
                    ->action(function (array $data) {
                        $record = $this->getOwnerRecord();

                        if ($data['mode'] === 'existing') {
                            GroupEquipment::where('id', $data['group_equipment_id'])
                                ->update([
                                    'room_id' => $record->id,
                                ]);
                        }

                        if ($data['mode'] === 'new') {
                            GroupEquipment::create([
                                'name' => $data['name'],
                                'patrimony' => $data['patrimony'] ?? null,
                                'maintenance_date' => $data['maintenance_date'] ?? null,
                                'status' => $data['status'] ?? true,
                                'room_id' => $record->id,
                            ]);
                        }
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('detach')
                    ->label('Remover grupo')
                    ->icon('heroicon-o-link-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Remover grupo da sala')
                    ->modalDescription('O grupo será apenas desvinculado desta sala.')
                    ->action(function ($record) {
                        $record->update([
                            'room_id' => null,
                        ]);
                    }),
            ]);
    }
}
