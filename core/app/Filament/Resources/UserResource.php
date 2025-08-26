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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados Pessoais')
                    ->columns(3)
                    ->schema([
                        TextInput::make('id')
                            ->label('RM')
                            ->placeholder('Ex.: 202500123')
                            ->maxLength(20)
                            ->required(),

                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->placeholder('Digite o nome completo')
                            ->required()
                            ->maxLength(191),

                        DatePicker::make('birthdate')
                            ->label('Nascimento')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required()
                            ->closeOnDateSelection(),

                        FileUpload::make('photo')
                            ->label('Foto do Usuário')
                            ->image()
                            ->maxSize(1024)
                            ->disk('public')
                            ->directory('img/users')
                            ->required()
                            ->avatar()
                            ->placeholder('Clique ou arraste para selecionar')
                            ->helperText('Formatos: jpg ou png, até 1MB')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth(300)
                            ->imageResizeTargetHeight(300),
                    ]),

                Section::make('Acesso')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Email de Acesso')
                            ->placeholder('exemplo@email.com')
                            ->email()
                            ->required()
                            ->maxLength(191),

                        TextInput::make('password')
                            ->label('Senha de Acesso')
                            ->placeholder('Digite uma senha segura')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->required(fn(string $context) => $context === 'create'),
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
                    ->columns(3)
                    ->visible(fn(Get $get) => $get('roles') == 3)
                    ->schema([
                        Select::make('course')
                            ->label('Curso')
                            ->relationship('curso', 'nome')
                            ->native(false)
                            ->columnSpan(2),

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
                            ->columnSpan(1),

                        Select::make('shift')
                            ->label('Turno')
                            ->options([
                                'Matutino'   => 'Matutino',
                                'Vespertino' => 'Vespertino',
                                'Noturno'    => 'Noturno',
                                'Integral'   => 'Integral',
                            ])
                            ->native(false)
                            ->columnSpan(1),
                    ]),

                Section::make('Vínculo / Contrato')
                    ->columns(3)
                    ->visible(fn(Get $get) => !empty($get('roles')) && $get('roles') != 3)
                    ->schema([
                        Toggle::make('is_determined')
                            ->label('Contrato determinado?')
                            ->inline(false)
                            ->reactive(),

                        DatePicker::make('contract_end_at')
                            ->label('Data fim de contrato')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->columnSpan(1)
                            ->visible(fn(Get $get) => $get('is_determined')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('rm')
                        ->label('RM')
                        ->weight('bold')
                        ->size('sm'),

                    TextColumn::make('name')
                        ->label('Nome')
                        ->weight('medium')
                        ->size('sm'),

                    TextColumn::make('roles.name')
                        ->label('Função')
                        ->size('sm'),

                    ImageColumn::make('photo')
                        ->label('Foto')
                        ->circular()
                        ->getStateUsing(function ($record) {
                            return asset('storage/' . $record->photo);
                        }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
