<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $monthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $pendingOrders = Order::where('status', 'pending')->count();

        return [
            Stat::make('Chiffre du jour', number_format($todayRevenue, 2) . ' FCFA')
                ->description('Revenus aujourd\'hui')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Revenu du mois', number_format($monthRevenue, 2) . ' FCFA')
                ->description('Ce mois-ci')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Commandes en attente', $pendingOrders)
                ->description('À traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Total produits', Product::where('is_active', true)->count())
                ->description('Produits actifs')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Clients', User::where('role', 'customer')->count())
                ->description('Clients inscrits')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),

            Stat::make('Stock faible', Product::whereColumn('quantity', '<=', 'low_stock_threshold')->count())
                ->description('Produits à réapprovisionner')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
