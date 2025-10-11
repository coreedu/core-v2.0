<?php

namespace App\Filament\Resources;

use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\CursoResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Stack;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Cursos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Curso')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome do Curso')
                            ->placeholder('Digite o nome do curso')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(8),

                        Forms\Components\TextInput::make('abreviacao')
                            ->label('Abreviação')
                            ->placeholder('Ex.: BCC')
                            ->required()
                            ->maxLength(10)
                            ->columnSpan(4),
                    ]),

                Section::make('Modalidade e Módulos')
                    ->columns(12)
                    ->schema([
                        Forms\Components\Select::make('modalidade')
                            ->label('Modalidade')
                            ->relationship('modality', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('Selecione a modalidade')
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('qtdModulos')
                            ->label('Quantidade de Módulos')
                            ->placeholder('Número de módulos')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                    ]),

                Section::make('Carga Horária')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('horas')
                            ->label('Carga Horária Total')
                            ->placeholder('Digite a carga horária total do curso')
                            ->required()
                            ->numeric()
                            ->columnSpan(6)
                            ->helperText('Ex.: 3200 horas'),

                        Forms\Components\TextInput::make('horasEstagio')
                            ->label('Carga Horária do Estágio')
                            ->placeholder('Ex.: 400')
                            ->numeric()
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('horasTg')
                            ->label('Carga Horária do TCC/TG')
                            ->placeholder('Ex.: 200')
                            ->numeric()
                            ->columnSpan(6),
                    ]),

                Section::make('Turno')
                    ->columns(12)
                    ->schema([
                        Forms\Components\Select::make('turno')
                            ->label('Turno')
                            ->relationship('shift', 'name')
                            ->required()
                            ->preload()
                            ->placeholder('Selecione o turno')
                            ->columnSpan(6),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('nome')
                        ->label('Curso')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable()
                        ->formatStateUsing(fn($state, $record) => "{$record->abreviacao} — {$state}"),

                    Tables\Columns\TextColumn::make('shift.name')
                        ->label('Turno')
                        ->badge()
                        ->color('info')
                        ->placeholder('Sem turno'),

                    Tables\Columns\TextColumn::make('qtdModulos')
                        ->label('Módulos')
                        ->formatStateUsing(fn($state) => "{$state} módulo(s) ")
                        ->color('gray'),
                ])->space(1),
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('componentes')
                    ->label('Componentes')
                    ->icon('heroicon-o-puzzle-piece')
                    ->url(fn($record) => Pages\EditCurso::getUrl(['record' => $record]))
                    ->color('info'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            \App\Filament\Resources\CursoResource\RelationManagers\ComponentesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit'   => Pages\EditCurso::route('/{record}/edit'),
        ];
    }
}