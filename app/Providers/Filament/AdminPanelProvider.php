<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->default()
      ->id('dashboard')
      ->path('dashboard')
      ->login()
      ->colors([
        'danger' => Color::Red,
        'gray' => Color::Slate,
        'info' => Color::Blue,
        'primary' => Color::Indigo,
        'success' => Color::Emerald,
        'warning' => Color::Orange,
      ])
      ->font('Source Code Pro')
      ->globalSearchKeyBindings(['ctrl+k', 'command+k'])
      ->navigationItems([
        NavigationItem::make('Youtube')
          ->url('https://youtube.com')
          ->openUrlInNewTab()
          ->icon('heroicon-o-video-camera')
          ->group('External')
          ->sort(2),
      ])
      ->userMenuItems([
        MenuItem::make()
          ->Label('Settings')
          ->url('')->icon('heroicon-o-cog-6-tooth'),
        'logout' => MenuItem::make()
          ->label('Log Out')
      ])
      // ->breadcrumbs(false) default true
      ->favicon(asset('images/logo.svg'))
      ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
      ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
      ->pages([
        Pages\Dashboard::class,
      ])
      ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
      ->widgets([
        Widgets\AccountWidget::class,
        // Uncomment the next line to include the Filament Info Widget
        // Widgets\FilamentInfoWidget::class,
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
      ])
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
