<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Filament\Resources\CursoResource\RelationManagers;
use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Stack;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Cursos';

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
                            ->options([
                                1 => 'Presencial',
                                2 => 'EAD',
                                3 => 'Semipresencial',
                            ])
                            ->required()
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
                            ->helperText('Exemplo: 3200 horas'),

                        Forms\Components\TextInput::make('horasEstagio')
                            ->label('Carga Horária do Estágio')
                            ->placeholder('Digite a carga horária do estágio')
                            ->numeric()
                            ->columnSpan(6)
                            ->helperText('Exemplo: 400 horas'),

                        Forms\Components\TextInput::make('horasTg')
                            ->label('Carga Horária do TCC/TG')
                            ->placeholder('Digite a carga horária do TCC/TG')
                            ->numeric()
                            ->columnSpan(6)
                            ->helperText('Exemplo: 200 horas'),
                    ]),

                Section::make('Turno')
                    ->columns(12)
                    ->schema([
                        Forms\Components\Select::make('turno')
                            ->label('Turno')
                            ->options([
                                1 => 'Matutino',
                                2 => 'Vespertino',
                                3 => 'Noturno',
                                4 => 'Integral',
                            ])
                            ->required()
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
                        ->label('Nome do Curso')
                        ->searchable()
                        ->sortable()
                        ->formatStateUsing(fn($state, $record) => "{$record->abreviacao} - {$state}"),

                    Tables\Columns\TextColumn::make('qtdModulos')
                        ->label('Quantidade de Semestres')
                        ->formatStateUsing(fn($state) => "{$state} semestre(s)"),
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
            'index' => Pages\ManageCursos::route('/'),
        ];
    }

    // Controle de permissões
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver cursos');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('criar cursos');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->can('ver cursos');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar cursos');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('deletar cursos');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('deletar cursos');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('ver cursos');
    }
}
