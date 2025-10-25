<?php

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Type;
use App\Models\Inventory\Equipment;

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
                    ->disk('public')
                    ->defaultImageUrl(asset('images/ambiente-padrao.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('patrimony')
                    ->label('Patrimônio')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('observation')
                    ->label('Observação')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('—'),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Disponível')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title($state ? 'Equipamento marcado como disponível' : 'Equipamento marcado como indisponível')
                            ->success()
                            ->send();
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
                            ->placeholder('Selecione um equipamento...')
                            ->searchable()
                            ->preload()
                            ->options(
                                Equipment::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->hint('Escolha um equipamento existente ou cadastre um novo.')
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
                                        ->placeholder('Selecione uma marca...')
                                        ->options(
                                            Brand::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Nome da Marca')
                                                ->placeholder('Ex.: Epson, Dell, HP...')
                                                ->required()
                                                ->maxLength(100),
                                        ])
                                        ->createOptionUsing(fn(array $data) => Brand::create($data)->getKey()),

                                    Forms\Components\Select::make('type_id')
                                        ->label('Tipo')
                                        ->placeholder('Selecione um tipo...')
                                        ->options(
                                            Type::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Nome do Tipo')
                                                ->placeholder('Ex.: Projetor, Notebook, Impressora...')
                                                ->required()
                                                ->maxLength(100),
                                        ])
                                        ->createOptionUsing(fn(array $data) => Type::create($data)->getKey()),
                                ]),

                                Forms\Components\Toggle::make('status')
                                    ->label('Disponível')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-x-circle')
                                    ->default(true)
                                    ->inline(false),

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
                                    ->disk('public')
                                    ->directory('equipments')
                                    ->nullable(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return Equipment::create($data)->getKey();
                            });
                    })
                    ->before(function (Tables\Actions\AttachAction $action, array $data) {
                        $room = $this->getOwnerRecord();
                        $equipmentId = $data['recordId'] ?? null;

                        if ($equipmentId && $room->equipments()->where('equipment_id', $equipmentId)->exists()) {
                            Notification::make()
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
