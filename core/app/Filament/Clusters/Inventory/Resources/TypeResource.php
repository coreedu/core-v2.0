<?php

namespace App\Filament\Clusters\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Resources\TypeResource\Pages;
use App\Models\Inventory\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\SubNavigationPosition;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;
    protected static ?string $cluster = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel = 'Tipos';
    protected static ?string $pluralModelLabel = 'Tipos';
    protected static ?string $modelLabel = 'Tipo';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Tipo')
                    ->placeholder('Ex.: Computador, Câmera, Projetor...')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Tipo')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable(),
                ]),
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
            'index' => Pages\ManageTypes::route('/'),
        ];
    }
}