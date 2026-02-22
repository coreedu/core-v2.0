<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Profile;
use App\Http\Middleware\FilamentSettings;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Rmsramos\Activitylog\ActivitylogPlugin;

// use Filament\Widgets;
use App\Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CentralPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('central')
            ->path('central')
            ->databaseNotifications()
            ->login()
            ->font('Poppins')
            // ->passwordReset()
            ->profile(Profile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                'logout' => \Filament\Navigation\MenuItem::make()->label('Sair do Sistema'),
                \Filament\Navigation\MenuItem::make()
                    ->label('Ir para o Site')
                    ->url('/')
                    ->icon('heroicon-o-globe-alt'),
            ])
            ->favicon(asset('images/favicon.ico'))
            ->brandLogo(url('images/logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverResources(in: app_path('Filament/Central/Resources'), for: 'App\\Filament\\Central\\Resources')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // Widgets\InfoChart::class,
                // Widgets\PredictiveRankingChart::class,
                // Widgets\RoomInventoryChart::class,
                // Widgets\EquipmentUsageChart::class,
                // Widgets\RoomUsageRankingChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                FilamentSettings::class
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                // ActivitylogPlugin::make()->navigation(false)
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
