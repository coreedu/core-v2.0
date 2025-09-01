<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComponenteResource\Pages;
use App\Models\Componente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Stack;

class ComponenteResource extends Resource
{
    protected static ?string $model = Componente::class;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';
    protected static ?string $navigationGroup = 'Cursos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Componente')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome do Componente')
                            ->placeholder('Digite o nome do componente')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(8),

                        Forms\Components\TextInput::make('abreviacao')
                            ->label('Abreviação')
                            ->placeholder('Ex.: CMP123')
                            ->required()
                            ->maxLength(20)
                            ->columnSpan(4),
                    ]),

                Section::make('Carga Horária')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('horasSemanais')
                            ->label('Horas Semanais')
                            ->placeholder('Digite as horas semanais')
                            ->numeric()
                            ->columnSpan(6)
                            ->helperText('Exemplo: 20 horas'),

                        Forms\Components\TextInput::make('horasTotais')
                            ->label('Horas Totais')
                            ->placeholder('Digite as horas totais')
                            ->numeric()
                            ->columnSpan(6)
                            ->helperText('Exemplo: 320 horas'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('abreviacao')
                        ->label('Nome')
                        ->searchable()
                        ->sortable()
                        ->formatStateUsing(fn($state, $record) => "{$state} - {$record->nome}"),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
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
            'index' => Pages\ManageComponentes::route('/'),
        ];
    }
}
