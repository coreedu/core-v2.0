<?php

namespace App\Filament\Clusters\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Resources\EquipmentResource\Pages;
use App\Models\Inventory\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\SubNavigationPosition;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    protected static ?string $cluster = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Equipamentos';
    protected static ?string $modelLabel = 'Equipamento';
    protected static ?string $pluralModelLabel = 'Equipamentos';
    protected static ?int $navigationSort = 3;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('type_id')
                    ->label('Tipo')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->preload(),
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
                ->directory('equipments')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Equipamento')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('patrimony')
                        ->label('Patrimônio')
                        ->color('gray')
                        ->sortable()
                        ->formatStateUsing(fn(?string $state): string => $state ? "Patrimônio: {$state}" : 'Patrimônio: —'),

                    Tables\Columns\TextColumn::make('status')
                        ->label('Disponibilidade')
                        ->formatStateUsing(fn(bool $state): string => $state ? 'Disponível' : 'Indisponível')
                        ->badge()
                        ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                ])->space(2),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->actions([
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
            'index' => Pages\ManageEquipment::route('/'),
        ];
    }
}
