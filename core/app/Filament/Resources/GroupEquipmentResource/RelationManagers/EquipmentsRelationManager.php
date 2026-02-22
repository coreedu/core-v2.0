<?php

namespace App\Filament\Resources\GroupEquipmentResource\RelationManagers;

use App\Models\Inventory\Equipment;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipments';

    protected static ?string $title = 'Equipamentos';

    protected function getEquipmentFormSchema(bool $isNested = false): array
    {
        return [
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
                    ->required(function (Forms\Get $get): bool {
                        $typeId = $get('type_id');
                        if (!$typeId) return false;
                        return Type::where('id', $typeId)->value('requires_asset_tag') === true;
                    })
                    ->validationMessages([
                        'required' => 'O patrimônio é obrigatório para este tipo de equipamento.',
                    ]),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->relationship(fn() => !$isNested ? 'brand' : null, 'name')
                    ->options(fn() => $isNested ? Brand::pluck('name', 'id') : null)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome da Marca')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(fn(array $data) => Brand::create($data)->id),

                Forms\Components\Select::make('type_id')
                    ->label('Tipo')
                    ->relationship(fn() => !$isNested ? 'type' : null, 'name')
                    ->options(fn() => $isNested ? Type::pluck('name', 'id') : null)
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Tipo')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(fn(array $data) => Type::create($data)->id),
            ]),

            Forms\Components\Toggle::make('status')
                ->label('Disponível')
                ->onColor('success')
                ->offColor('danger')
                ->default(true)
                ->live(),

            Forms\Components\Textarea::make('observation')
                ->label('Observações')
                ->rows(3)
                ->required(fn(Forms\Get $get): bool => ! $get('status'))
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('photos')
                ->label('Fotos do Equipamento')
                ->image()
                ->multiple()
                ->disk('public')
                ->directory('equipments')
                ->columnSpanFull(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->getEquipmentFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('patrimony')->label('Patrimônio')->searchable(),
                Tables\Columns\TextColumn::make('brand.name')->label('Marca'),
                Tables\Columns\TextColumn::make('type.name')->label('Tipo'),
                Tables\Columns\ToggleColumn::make('status')->label('Ativo'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('addEquipment')
                    ->label('Adicionar Equipamento ao Grupo')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\Select::make('equipment_id')
                            ->label('Equipamento')
                            ->options(fn() => Equipment::whereNull('group_equipment_id')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm($this->getEquipmentFormSchema(isNested: true))
                            ->createOptionUsing(fn(array $data) => Equipment::create($data)->id),
                    ])
                    ->action(function (array $data) {
                        Equipment::where('id', $data['equipment_id'])
                            ->update(['group_equipment_id' => $this->getOwnerRecord()->id]);

                        Notification::make()
                            ->title('Equipamento vinculado com sucesso')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('desvincular')
                    ->label('Remover')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remover vínculo')
                    ->modalDescription('O equipamento continuará existindo, mas não fará mais parte deste grupo.')
                    ->action(function ($record) {
                        $record->update(['group_equipment_id' => null]);

                        Notification::make()
                            ->title('Vínculo removido')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
