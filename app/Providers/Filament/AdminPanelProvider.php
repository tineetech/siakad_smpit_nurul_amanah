<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\CustomLogin;
use App\Filament\Navigation\AdminNavigation;
use App\Filament\Navigation\StudentNavigation;
use App\Filament\Navigation\TeacherNavigation;
use App\Filament\Navigation\StaffNavigation;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->brandLogo(asset('images/pp/logo-smp.png'))
            ->favicon(asset('images/pp/logo-smp.png'))
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->navigationGroups([
                'Data Master',
                'Kesiswaan',
                'POS SPP',
                'Pengaturan',
            ])
            ->navigation(function () {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                if (!$user) {
                    return [];
                }

                $dashboardItem = NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard'))
                    ->url(route('filament.admin.pages.dashboard'));

                $profileItem = NavigationItem::make('Profil')
                    ->icon('heroicon-o-user')
                    ->url('#')
                    ->group('Pengaturan');

                $navigationItems = [$dashboardItem];

                if ($user->isAdmin()) {
                    $navigationItems = array_merge(
                        $navigationItems,
                        (new AdminNavigation())->items()
                    );
                } elseif ($user->isGuru()) {
                    $navigationItems = array_merge(
                        $navigationItems,
                        (new TeacherNavigation())->items()
                    );
                } elseif ($user->isSiswa()) {
                    $navigationItems = array_merge(
                        $navigationItems,
                        (new StudentNavigation())->items()
                    );
                } elseif ($user->isTataUsaha()) {
                    $navigationItems = array_merge(
                        $navigationItems,
                        (new StaffNavigation())->items()
                    );
                }

                $navigationItems[] = $profileItem;

                return $navigationItems;
            })
            ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}