<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModalityResource\Pages;
use App\Models\Modality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

class ModalityResource extends Resource
{
    protected static ?string $model = Modality::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationGroup = 'Cursos';
    protected static ?string $navigationLabel = 'Modalidades';
    protected static ?string $pluralModelLabel = 'Modalidades';
    protected static ?string $modelLabel = 'Modalidade';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->placeholder('Ex.: Presencial, EAD, Semipresencial')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->placeholder('Opcional')
                    ->rows(3)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label('Modalidade')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('description')
                        ->label('Descrição')
                        ->color('gray')
                        ->wrap()
                        ->limit(160)
                        ->placeholder('Sem descrição'),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->filters([
                //
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
            'index' => Pages\ManageModalities::route('/'),
        ];
    }
}
