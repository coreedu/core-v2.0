<?php

namespace App\Filament\Clusters\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Resources\EquipmentResource\Pages;
use App\Models\Inventory\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\SubNavigationPosition;
use App\Filament\Imports\EquipmentImporter;
use App\Filament\Exports\EquipmentExporter;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    protected static ?string $cluster = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Equipamentos';
    protected static ?string $modelLabel = 'Equipamento';
    protected static ?string $pluralModelLabel = 'Equipamentos';
    protected static ?int $navigationSort = 3;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->placeholder('Ex.: Projetor Epson X400')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('patrimony')
                    ->label('Patrimônio')
                    ->placeholder('Ex.: 12345')
                    ->maxLength(255)
                    ->required(fn(Forms\Get $get): bool => filled($get('type_id')))
                    ->validationMessages([
                        'required' => 'O patrimônio é obrigatório para este tipo de equipamento.',
                    ]),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome da Marca')
                            ->required()
                            ->maxLength(255),
                    ]),

                Forms\Components\Select::make('type_id')
                    ->label('Tipo')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Tipo')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]),

            Forms\Components\Select::make('group_equipment_id')
                ->label('Grupo do Equipamento')
                ->relationship('group', 'name')
                ->searchable()
                ->preload()
                ->placeholder('Selecione um grupo')
                ->nullable()
                ->createOptionForm([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Grupo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('status')
                            ->label('Ativo')
                            ->default(true),
                        Forms\Components\DatePicker::make('maintenance_date')
                            ->label('Data de manutenção'),
                    ])
                ]),

            Forms\Components\Toggle::make('status')
                ->label('Disponível')
                ->helperText('Define se o equipamento pode ser reservado ou emprestado no momento.')
                ->onColor('success')
                ->offColor('danger')
                ->onIcon('heroicon-o-check-circle')
                ->offIcon('heroicon-o-x-circle')
                ->default(true)
                ->live()
                ->inline(false),

            Forms\Components\Textarea::make('observation')
                ->label('Observações')
                ->placeholder('Ex.: Equipamento com pequenos arranhões na lateral.')
                ->rows(3)
                ->required(fn(Forms\Get $get): bool => ! $get('status'))
                ->validationMessages([
                    'required' => 'É necessário informar o motivo ou observação quando o equipamento está indisponível.',
                ]),

            Forms\Components\FileUpload::make('photos')
                ->label('Fotos do Equipamento')
                ->image()
                ->multiple()
                ->reorderable()
                ->disk('public')
                ->directory('equipments')
                ->nullable(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Equipamento')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('patrimony')
                        ->label('Patrimônio')
                        ->color('gray')
                        ->sortable()
                        ->formatStateUsing(fn(?string $state): string => $state ? "Patrimônio: {$state}" : 'Patrimônio: —'),

                    Tables\Columns\TextColumn::make('status')
                        ->label('Disponibilidade')
                        ->formatStateUsing(fn(bool $state): string => $state ? 'Disponível' : 'Indisponível')
                        ->badge()
                        ->color(fn(bool $state): string => $state ? 'success' : 'danger'),

                    Tables\Columns\TextColumn::make('group.name')
                        ->label('Grupo')
                        ->badge()
                        ->color('primary')
                        ->sortable(),
                ])->space(2),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Importar')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->importer(EquipmentImporter::class),

                ExportAction::make()
                    ->label('Exportar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(EquipmentExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                        ExportFormat::Xlsx,
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('Excluir')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEquipment::route('/'),
        ];
    }
}
