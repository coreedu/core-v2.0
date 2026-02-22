<?php

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\GroupEquipment;
use Filament\Support\Enums\Alignment;

class GroupEquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'groupEquipments';

    protected static ?string $title = 'Grupos de Equipamentos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome')
                ->required(),
            Forms\Components\DatePicker::make('maintenance_date')
                ->label('Manutenção'),
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
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignment(Alignment::Start),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Ativo')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignment(Alignment::Center),

                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('Manutenção')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->alignment(Alignment::Center),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Adicionar grupo')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalHeading('Adicionar grupo de equipamentos')
                    ->modalSubmitActionLabel('Salvar')
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->label('Grupos')
                            ->placeholder('Selecione ou clique no +')
                            ->options(
                                GroupEquipment::query()
                                    ->whereNull('room_id')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nome do grupo')
                                        ->required(),
                                    Forms\Components\DatePicker::make('maintenance_date')
                                        ->label('Data de Manutenção'),
                                    Forms\Components\Toggle::make('status')
                                        ->label('Ativo')
                                        ->default(true)
                                        ->inline(false)
                                        ->extraAttributes([
                                            'style' => 'margin-top: 32px;',
                                        ]),
                                ]),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return GroupEquipment::create($data)->id;
                            });
                    })
                    ->action(function (array $data) {
                        $recordId = $data['recordId'];
                        GroupEquipment::where('id', $recordId)->update([
                            'room_id' => $this->getOwnerRecord()->id,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('detach')
                    ->label('Remover')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(GroupEquipment $record) => $record->update(['room_id' => null])),
            ]);
    }
}
