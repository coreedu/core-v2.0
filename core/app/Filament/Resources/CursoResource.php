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

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('turno')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('abreviacao')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('qtdModulos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('modalidade')
                    // ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('horas')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('horasEstagio')
                    ->numeric(),
                Forms\Components\TextInput::make('horasTg')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('turno')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abreviacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qtdModulos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modalidade')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('horasEstagio')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('horasTg')
                    ->numeric()
                    ->sortable(),
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
