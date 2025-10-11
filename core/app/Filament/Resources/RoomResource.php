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
                Forms\Components\TextInput::make('name')
                    ->label('Nome (opcional)')
                    ->placeholder('Ex.: Sala Maker, Auditório Principal...')
                    ->maxLength(100)
                    ->columnSpan(8),

                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->placeholder('Ex.: 101')
                    ->maxLength(10)
                    ->columnSpan(4)
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(6),

                Forms\Components\FileUpload::make('img')
                    ->label('Imagem')
                    ->image()
                    ->directory('rooms')
                    ->imageEditor()
                    ->columnSpan(6),

                Forms\Components\Toggle::make('active')
                    ->label('Ativo')
                    ->default(true)
                    ->inline(false)
                    ->required()
                    ->columnSpan(3),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('img')
                        ->label('Imagem')
                        ->height(120)
                        ->extraImgAttributes([
                            'style' => 'width: 100%; object-fit: cover; border-radius: 8px;'
                        ])
                        ->defaultImageUrl(asset('images/ambiente-padrao.jpg')),

                    Tables\Columns\TextColumn::make('full_name')
                        ->label('Nome / Número')
                        ->getStateUsing(
                            fn($record) =>
                            $record->name
                                ? "{$record->name} - {$record->number}"
                                : $record->number
                        )
                        ->weight('bold')
                        ->size('lg')
                        ->wrap(),

                    Tables\Columns\TextColumn::make('category.name')
                        ->label('Categoria')
                        ->badge()
                        ->color('info'),
                ])->space(2),
            ])
            ->contentGrid([
                'md' => 3,
                'xl' => 4,
            ])
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRooms::route('/'),
        ];
    }
}
