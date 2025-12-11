<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
// use App\Filament\Resources\RoomResource\RelationManagers\EquipmentsRelationManager;
use App\Filament\Resources\RoomResource\RelationManagers\GroupEquipmentsRelationManager;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Imports\RoomImporter;
use App\Filament\Exports\RoomExporter;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;

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
                    ->required(fn($livewire) => $livewire instanceof \App\Filament\Resources\RoomResource\Pages\CreateRoom)
                    ->nullable()
                    ->native(false)
                    ->placeholder('Selecione a categoria')
                    ->columnSpan(6),

                Forms\Components\FileUpload::make('img')
                    ->label('Imagem')
                    ->image()
                    ->disk('public')
                    ->directory('rooms')
                    ->imageEditor()
                    ->columnSpan(6),

                Forms\Components\Toggle::make('active')
                    ->label('Ativo')
                    ->default(true)
                    ->inline(false)
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
                        ->disk('public')
                        ->extraImgAttributes([
                            'style' => 'width: 100%; object-fit: cover; border-radius: 8px;'
                        ])
                        ->defaultImageUrl(asset('images/ambiente-padrao.png')),

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

                    // Tables\Columns\TextColumn::make('equipments_count')
                    //     ->counts('equipments')
                    //     ->label('Equipamentos')
                    //     ->formatStateUsing(fn($state) => "{$state} equipamento" . ($state != 1 ? 's' : ''))
                    //     ->badge()
                    //     ->color('gray'),
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
            ->headerActions([
                ImportAction::make()
                    ->label('Importar')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->importer(RoomImporter::class),

                ExportAction::make()
                    ->label('Exportar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(RoomExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                        ExportFormat::Xlsx,
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('editarCategoria')
                        ->label('Alterar Categoria')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Forms\Components\Select::make('nova_categoria')
                                ->label('Nova Categoria')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('Selecione uma categoria'),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'type' => $data['nova_categoria'],
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Alterar Categoria')
                        ->modalSubmitActionLabel('Salvar Alterações')
                        ->successNotificationTitle('Categorias atualizadas com sucesso!'),
                ]),
                Tables\Actions\BulkAction::make('gerarRelatorio')
                    ->label('Gerar Relatório')
                    ->icon('heroicon-o-document-text')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $rooms = $records;

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rooms-relatorio', [
                            'rooms' => $rooms,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'relatorio-ambientes-' . now()->format('d-m-Y_H\hi') . '.pdf');
                    })
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Relatório')
                    ->modalSubheading('O relatório incluirá as salas selecionadas e seus respectivos equipamentos.')
                    ->modalButton('Gerar PDF')
                    ->successNotificationTitle('Relatório gerado com sucesso!')
            ]);
    }

    public static function getRelations(): array
    {
        return [
            GroupEquipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
