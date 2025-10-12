<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Componente;
use Filament\Forms\Components\ViewField;
use App\Filament\Resources\ComponentUserResource\RelationManagers\ComponentsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Administração';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = -10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados Pessoais')
                    ->columns(12)
                    ->schema([
                        TextInput::make('id')
                            ->label('RM')
                            ->placeholder('Ex.: 202500123')
                            ->maxLength(20)
                            ->required()
                            ->numeric()
                            ->columnSpan(4)
                            ->prefixIcon('heroicon-s-identification'),

                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->placeholder('Digite o nome completo')
                            ->required()
                            ->maxLength(191)
                            ->columnSpan(8),

                        DatePicker::make('birthdate')
                            ->label('Nascimento')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required()
                            ->closeOnDateSelection()
                            ->columnSpan(4)
                            ->prefixIcon('heroicon-s-calendar'),

                        FileUpload::make('photo')
                            ->label('Foto do Usuário')
                            ->image()
                            ->maxSize(1024)
                            ->disk('public')
                            ->directory('img/users')
                            ->required()
                            ->avatar()
                            ->placeholder('Clique ou arraste para selecionar')
                            ->helperText('JPG ou PNG, até 1MB')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth(300)
                            ->imageResizeTargetHeight(300)
                            ->columnSpan(8),
                    ]),

                Section::make('Acesso')
                    ->columns(12)
                    ->schema([
                        TextInput::make('email')
                            ->label('Email de Acesso')
                            ->placeholder('exemplo@email.com')
                            ->email()
                            ->required()
                            ->maxLength(191)
                            ->columnSpan(6)
                            ->prefixIcon('heroicon-m-envelope'),

                        TextInput::make('password')
                            ->label('Senha de Acesso')
                            ->placeholder('Digite uma senha segura')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->required(fn(string $context) => $context === 'create')
                            ->columnSpan(6)
                            ->prefixIcon('heroicon-s-lock-closed'),
                    ]),

                Section::make('Papéis / Funções')
                    ->columns(1)
                    ->schema([
                        Select::make('roles')
                            ->label('Papéis do Usuário')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->columnSpanFull()
                            ->live()
                    ]),

                Section::make('Dados Acadêmicos')
                    ->columns(12)
                    ->visible(function (Get $get) {
                        $roleName = \Spatie\Permission\Models\Role::where('id', $get('roles'))->value('name');
                        return $roleName === 'Aluno';
                    })
                    ->schema([
                        Select::make('course')
                            ->label('Curso')
                            ->relationship('curso', 'nome')
                            ->native(false)
                            ->columnSpan(4),

                        Select::make('semester')
                            ->label('Semestre')
                            ->options([
                                1 => '1º',
                                2 => '2º',
                                3 => '3º',
                                4 => '4º',
                                5 => '5º',
                                6 => '6º',
                                7 => '7º',
                                8 => '8º'
                            ])
                            ->native(false)
                            ->columnSpan(4),

                        Select::make('shift')
                            ->label('Turno')
                            ->options([
                                1 => 'Matutino',
                                2 => 'Vespertino',
                                3 => 'Noturno',
                                4 => 'Integral',
                            ])
                            ->native(false)
                            ->columnSpan(4),
                    ]),

                Section::make('Vínculo / Contrato')
                    ->columns(12)
                    ->visible(function (Get $get) {
                        $roleName = \Spatie\Permission\Models\Role::where('id', $get('roles'))->value('name');
                        return  !is_null($roleName) && $roleName != 'Aluno';
                    })
                    ->schema([
                        Toggle::make('is_determined')
                            ->label('Contrato determinado?')
                            ->inline(false)
                            ->reactive()
                            ->columnSpan(4),

                        DatePicker::make('contract_end_at')
                            ->label('Data fim de contrato')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->columnSpan(4)
                            ->visible(fn(Get $get) => $get('is_determined'))
                            ->prefixIcon('heroicon-s-calendar'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('photo')
                        ->label('Foto')
                        ->circular()
                        ->getStateUsing(function ($record) {
                            return asset('storage/' . $record->photo);
                        }),

                    TextColumn::make('id')
                        ->label('RM')
                        ->sortable()
                        ->searchable()
                        ->weight('bold')
                        ->size('sm')
                        ->formatStateUsing(function ($state, $record) {
                            return $record->id . ' - ' . $record->name;
                        }),

                    TextColumn::make('roles.name')
                        ->label('Função')
                        ->size('sm'),
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

    public static function getRelations(): array
    {
        return [
            ComponentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'manage-availability' => Pages\ManageAvailability::route('/{record}/availability'),
        ];
    }
}
