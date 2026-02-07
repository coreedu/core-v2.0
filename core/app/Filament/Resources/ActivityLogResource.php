<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Filament\Resources\ActivityLogResource\RelationManagers;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityLogResource extends Resource
{

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $model = Activity::class;
    protected static ?string $navigationLabel = 'Logs';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject_type')
                    ->label('Módulo')
                    ->formatStateUsing(fn ($state) =>
                        class_basename($state)
                    ),

                BadgeColumn::make('event')
                    ->label('Ação')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger'  => 'deleted',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Excluído',
                        default => ucfirst($state),
                    }),

                

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(40),

                TextColumn::make('causer.name')
                    ->label('Usuário')
                    ->default('Sistema'),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Excluído',
                    ]),

                // SelectFilter::make('causer_id')
                //     ->label('Usuário')
                //     ->relationship('causer', 'name')
                //     ->searchable()
                //     ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(fn ($query, $data) =>
                        $query
                            ->when($data['from'], fn ($q) =>
                                $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) =>
                                $q->whereDate('created_at', '<=', $data['until']))
                    ),
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
            'index' => Pages\ListActivityLogs::route('/'),
            // 'create' => Pages\CreateActivityLog::route('/create'),
            // 'edit' => Pages\EditActivityLog::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
