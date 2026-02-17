<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContextResource\Pages;
use App\Filament\Resources\ContextResource\RelationManagers;
use App\Filament\Clusters\Time;
use App\Models\Time\Context;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class ContextResource extends Resource
{
    protected static ?string $model = Context::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Time::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationLabel = 'Contextos';
    protected static ?string $pluralModelLabel = 'Contextos';
    protected static ?string $modelLabel = 'Contexto';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Classificação de Horário')
                    ->description('Defina o nome do contexto (ex: Ensino Médio, Modular, etc.)')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Contexto')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ex: Ensino Médio'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Contexto')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable()
                        ->alignCenter(),
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContexts::route('/'),
            'create' => Pages\CreateContext::route('/create'),
            'edit' => Pages\EditContext::route('/{record}/edit'),
        ];
    }
}
