<?php

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipments';
    protected static ?string $title = 'Equipamentos';
    protected static ?string $icon = 'heroicon-o-cog-6-tooth';
    protected static ?string $label = 'Equipamento';
    protected static ?string $pluralLabel = 'Equipamentos';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('photos.0')
                    ->label('Imagem')
                    ->height(50)
                    ->width(50)
                    ->circular()
                    ->defaultImageUrl(asset('images/ambiente-padrao.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->sortable(),

                Tables\Columns\TextColumn::make('patrimony')
                    ->label('Patrimônio')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'in_use' => 'warning',
                        'maintenance' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Vincular Equipamento')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn($query) => $query->orderBy('name'))
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->label('Equipamento')
                            ->searchable()
                            ->preload()
                            ->options(
                                \App\Models\Inventory\Equipment::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->hint('Escolha um equipamento ou cadastre um novo.')
                            ->createOptionForm([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nome')
                                        ->placeholder('Ex.: Projetor Epson X400')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('patrimony')
                                        ->label('Patrimônio')
                                        ->placeholder('Ex.: 12345')
                                        ->maxLength(255)
                                        ->nullable(),
                                ]),

                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Select::make('brand_id')
                                        ->label('Marca')
                                        ->options(
                                            \App\Models\Inventory\Brand::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Nome da Marca')
                                                ->required()
                                                ->maxLength(100),
                                        ]),

                                    Forms\Components\Select::make('type_id')
                                        ->label('Tipo')
                                        ->options(
                                            \App\Models\Inventory\Type::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Nome do Tipo')
                                                ->required()
                                                ->maxLength(100),
                                        ]),
                                ]),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'available' => 'Disponível',
                                        'maintenance' => 'Em manutenção',
                                        'broken' => 'Com defeito',
                                        'unavailable' => 'Indisponível',
                                    ])
                                    ->default('available')
                                    ->required(),

                                Forms\Components\Textarea::make('observation')
                                    ->label('Observações')
                                    ->placeholder('Ex.: Equipamento com pequenos arranhões na lateral.')
                                    ->rows(3)
                                    ->nullable(),

                                Forms\Components\FileUpload::make('photos')
                                    ->label('Fotos do Equipamento')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->directory('equipments')
                                    ->nullable(),
                            ]);
                    })
                    ->before(function (Tables\Actions\AttachAction $action, array $data) {
                        $room = $this->getOwnerRecord();
                        $equipmentId = $data['recordId'] ?? null;

                        if ($equipmentId && $room->equipments()->where('equipment_id', $equipmentId)->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Este equipamento já está vinculado a esta sala.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Desvincular'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->label('Desvincular Selecionados'),
            ]);
    }
}
