<?php

namespace App\Filament\Pages;

use Faker\Core\Color;
use Filament\Pages\Auth\EditProfile;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;

class Profile extends EditProfile
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        Fieldset::make('settings')
                            ->label('Configurações')
                            ->schema([
                                ColorPicker::make('settings.color')
                                    ->label('Cor Primária')
                                    ->columnSpanFull()
                                    ->inlineLabel()
                                    ->formatStateUsing(fn(?string $state): string => $state ?? config('filament.theme.colors.primary'))
                            ]),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }

    protected function afterSave(): void
    {
        redirect(request()->header('Referer'));
    }
}
