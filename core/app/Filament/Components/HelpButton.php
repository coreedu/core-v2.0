<?php

namespace App\Filament\Components;

use Filament\Actions\Action;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;

class HelpButton
{
    public static function make(string $key): Action
    {
        $helpData = self::getHelpData($key);

        $title = $helpData['title'] ?? ucfirst($key);
        $text  = $helpData['text'] ?? 'Nenhuma ajuda disponível para esta seção.';

        return Action::make('help')
            ->label('Ajuda')
            ->icon('heroicon-o-question-mark-circle')
            ->color('gray')
            ->outlined()
            ->modalHeading("Ajuda — {$title}")
            ->modalWidth('lg')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fechar')
            ->modalDescription(new HtmlString($text));
    }

    protected static function getHelpData(string $key): array
    {
        $path = resource_path('data/help.json');

        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            return $data[$key] ?? [];
        }

        return [];
    }
}
