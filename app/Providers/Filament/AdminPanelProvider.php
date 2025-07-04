<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\AbsensiChart;
use App\Filament\Widgets\CalonSiswaChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
// use App\Filament\Pages\AbsensiSiswa;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('siakad')
            ->login(CustomLogin::class)
            ->brandLogo(asset('images/pp/logo-smp.png'))
            ->favicon(asset('images/pp/logo-smp.png'))
            // ->user(User::class)
            // ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                 \App\Filament\Pages\AbsensiSiswa::class,
                 \App\Filament\Pages\AbsensiGuru::class,
                 \App\Filament\Pages\ScanAbsensiGuru::class,
                 
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                StatsOverview::class,
                // CalonSiswaChart::class,
                // AbsensiChart::class,
            ])
            ->profile(EditProfile::class)
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
            ->navigationGroups([
                'Data Master',        // Ini akan muncul pertama
                'Kesiswaan', // Ini akan muncul kedua
                'Absensi',
                'POS SPP',       // Ini akan muncul ketiga
                'Portal SPMB',
                'Pengaturan',
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
