<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Resources\GroupEquipmentResource\Pages;
use App\Filament\Resources\GroupEquipmentResource\RelationManagers;
use App\Models\GroupEquipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class GroupEquipmentResource extends Resource
{
    protected static ?string $model = GroupEquipment::class;
    protected static ?string $cluster = Inventory::class;

    protected static ?string $navigationLabel = 'Grupos de Equipamentos';
    protected static ?string $pluralModelLabel = 'Grupos de Equipamentos';
    protected static ?string $modelLabel = 'Grupos de Equipamento';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(12)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(6),

                Forms\Components\DatePicker::make('maintenance_date')
                    ->label('Data de manutenção')
                    ->nullable()
                    ->columnSpan(4),

                Forms\Components\Toggle::make('status')
                    ->label('Ativo')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->columnSpan(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('Manutenção')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('equipments_count')
                    ->label('Equipamentos')
                    ->counts('equipments')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EquipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroupEquipment::route('/'),
            'create' => Pages\CreateGroupEquipment::route('/create'),
            'edit' => Pages\EditGroupEquipment::route('/{record}/edit'),
        ];
    }
}
