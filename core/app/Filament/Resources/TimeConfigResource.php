<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeConfigResource\Pages;
use App\Filament\Resources\TimeConfigResource\RelationManagers;
use App\Models\time\TimeConfig;
use App\Filament\Clusters\Time;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\TextColumn;

use App\Models\Time\Shift;
use App\Models\Time\Day;
use App\Models\Time\LessonTime;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;

class TimeConfigResource extends Resource
{
    protected static ?string $model = TimeConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Time::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationLabel = 'Configuração';
    protected static ?string $pluralModelLabel = 'Configuração';
    protected static ?string $modelLabel = 'Configuração';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        $shifts = Shift::all();
        $days = Day::all();
        $lessonTimes = LessonTime::pluck('start', 'id');

        return $form
            ->schema([
                Select::make('context_id')
                    ->label('Modalidade/Contexto')
                    ->relationship('context', 'name')
                    ->required()
                    ->disabled(fn ($context) => $context === 'edit')
                    ->unique(
                        table: 'time_config', 
                        column: 'context_id', 
                        ignoreRecord: true
                    )
                    ->validationMessages([
                        'unique' => 'Esta modalidade já possui uma configuração de horários cadastrada.',
                    ]),

                Tabs::make('Turnos')
                    ->columnSpanFull()
                    ->tabs(
                        $shifts->map(function ($shift) use ($days, $lessonTimes) {
                            return Tabs\Tab::make($shift->name)
                                ->schema([
                                    Grid::make($days->count()) // Colunas dinâmicas por dia
                                        ->schema(
                                            $days->map(function ($day) use ($shift, $lessonTimes) {
                                                return CheckboxList::make("slots.{$shift->id}.{$day->id}")
                                                    ->label($day->name)
                                                    ->options($lessonTimes)
                                                    ->bulkToggleable();
                                            })->toArray()
                                        )
                                ]);
                        })->toArray()
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mostra o Contexto (ex: Médio) através da relação com TimeConfig
                TextColumn::make('context.name')
                    ->label('Contexto/Modalidade')
                    ->sortable()
                    ->searchable(),

                // // Mostra o Turno (ex: Noite) através da relação com TimeConfig
                // TextColumn::make('shift.name')
                //     ->label('Turno')
                //     ->badge()
                //     ->color('info')
                //     ->sortable(),

                // // Mostra o Dia da Semana
                // TextColumn::make('day.name')
                //     ->label('Dia')
                //     ->sortable(),
            ])
            ->defaultSort('context_id')
            // ->defaultGroup('context.name')
            // ->groupRecordsRecordSelect() // Opcional: permite selecionar o grupo todo
            // ->collapsible()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['context'])
            // Pegamos o ID mais alto para cada contexto e agrupamos
            ->selectRaw('MAX(id) as id, context_id')
            ->groupBy('context_id')
            ->reorder();
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
            'index' => Pages\ListTimeConfigs::route('/'),
            'create' => Pages\CreateTimeConfig::route('/create'),
            'edit' => Pages\EditTimeConfig::route('/{record}/edit'),
        ];
    }
}
