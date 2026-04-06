<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\BrandResource;
use App\Filament\Admin\Resources\CategoryResource;
use App\Filament\Admin\Resources\OrderResource;
use App\Filament\Admin\Resources\ProductResource;
use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Widgets\LatestOrders;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\TopProducts;
use App\Filament\Admin\Pages\BoutiqueSettings;
use App\Filament\Admin\Pages\WhatsAppSettings;
use App\Filament\Admin\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->resources([
                ProductResource::class,
                CategoryResource::class,
                BrandResource::class,
                ReviewResource::class,
                OrderResource::class,
                UserResource::class,
                // SettingResource supprimé — tout est dans BoutiqueSettings
            ])
            ->pages([
                Dashboard::class,
                BoutiqueSettings::class,
                WhatsAppSettings::class,
            ])
            ->widgets([
                StatsOverview::class,
                RevenueChart::class,
                LatestOrders::class,
                TopProducts::class,
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
            ])
            ->navigationGroups([
                'Catalogue',
                'Ventes',
                'Utilisateurs',
                'Paramètres',
            ]);
    }
}
