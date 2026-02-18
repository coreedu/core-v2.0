<?php

namespace App\Filament\Resources\CursoResource\RelationManagers;

use App\Models\Componente;
use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ComponentesRelationManager extends RelationManager
{
    protected static string $relationship = 'componentes';
    protected static ?string $title = 'Componentes';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('abreviacao_nome')
                    ->label('Nome')
                    ->getStateUsing(fn(Componente $record) => "{$record->abreviacao} - {$record->nome}"),
                Tables\Columns\TextColumn::make('module')
                    ->label('Módulo')
                    ->sortable()
                    ->formatStateUsing(fn($state) => "{$state}º - Módulo"),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->modalHeading('Vincular componente')
                    ->modalSubmitActionLabel('Vincular')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl')
                    ->preloadRecordSelect()
                    ->recordTitleAttribute('nome')
                    ->recordSelectSearchColumns(['abreviacao', 'nome'])
                    ->form(function (Tables\Actions\AttachAction $action): array {
                        return [
                            Forms\Components\Grid::make(2)->schema([
                                $action->getRecordSelect()
                                    ->label('Componente')
                                    ->hiddenLabel(false)
                                    ->getOptionLabelFromRecordUsing(fn(\App\Models\Componente $r) => "{$r->abreviacao} - {$r->nome}")
                                    ->searchable(),
                                Forms\Components\Select::make('module')
                                    ->label('Módulo/Semestre')
                                    ->options(function () {
                                        $qtd = (int) $this->ownerRecord->qtdModulos;
                                        return collect(range(1, max($qtd, 1)))
                                            ->mapWithKeys(fn($n) => [$n => "{$n}º - Módulo"])
                                            ->all();
                                    })
                                    ->required(),
                            ]),
                        ];
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('editarModulo')
                    ->label('Editar vínculo')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading('Editar vínculo do componente')
                    ->modalSubmitActionLabel('Salvar alterações')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl')
                    ->mountUsing(function (Componente $record, ComponentContainer $form) {
                        $form->fill([
                            'componente' => "{$record->abreviacao} - {$record->nome}",
                            'module' => $record->pivot->module ?? null,
                        ]);
                    })
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('componente')
                                ->label('Componente')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\Select::make('module')
                                ->label('Módulo/Semestre')
                                ->options(function () {
                                    $qtd = (int) $this->ownerRecord->qtdModulos;
                                    return collect(range(1, max($qtd, 1)))
                                        ->mapWithKeys(fn($n) => [$n => "{$n}º - Módulo"])
                                        ->all();
                                })
                                ->required(),
                        ]),
                    ])
                    ->action(function (Componente $record, array $data) {
                        $this->ownerRecord
                            ->componentes()
                            ->updateExistingPivot($record->getKey(), [
                                'module' => $data['module'],
                            ]);
                    })
                    ->successNotificationTitle('Vínculo atualizado com sucesso!'),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }
}