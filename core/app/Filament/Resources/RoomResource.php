<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Ambientes';
    protected static ?string $pluralModelLabel = 'Ambientes';
    protected static ?string $modelLabel = 'Ambiente';
    protected static ?string $navigationGroup = 'Espaços';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Ambiente')
                    ->description('Defina nome, categoria, imagem e status.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Ex.: Sala de Aula 101')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('type')
                            ->label('Categoria')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\FileUpload::make('img')
                            ->label('Imagem')
                            ->image()
                            ->directory('rooms')
                            ->imageEditor(),

                        Forms\Components\Toggle::make('active')
                            ->label('Ativo')
                            ->default(true)
                            ->inline(false)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('img')
                    ->label('Imagem')
                    ->square()
                    ->size(48)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Ativo')
                    ->trueLabel('Somente ativos')
                    ->falseLabel('Somente inativos')
                    ->placeholder('Todos'),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Cadastrar Ambiente'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRooms::route('/'),
        ];
    }
}
