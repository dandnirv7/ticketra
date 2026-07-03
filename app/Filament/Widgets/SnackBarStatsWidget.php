<?php

namespace App\Filament\Widgets;

use App\Models\SnackOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SnackBarStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $fnbRevenueToday = (float) SnackOrder::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$todayStart, $todayEnd])
            ->sum('fnb_total');

        $fnbOrdersToday = SnackOrder::query()
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        $fnbPendingKitchen = SnackOrder::query()
            ->where('status', 'paid')
            ->count();

        $fnbReadyPickup = SnackOrder::query()
            ->where('status', 'ready')
            ->count();

        return [
            Stat::make('Omzet F&B Hari Ini', 'Rp ' . number_format($fnbRevenueToday, 0, ',', '.'))
                ->description('Total penjualan snack terkonfirmasi')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Total Pesanan F&B Hari Ini', $fnbOrdersToday . ' pesanan')
                ->description('Termasuk pesanan tiket + standalone')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('info'),

            Stat::make('Antrean Dapur F&B', $fnbPendingKitchen . ' pesanan')
                ->description('Perlu disiapkan')
                ->descriptionIcon('heroicon-m-clock')
                ->color($fnbPendingKitchen > 5 ? 'danger' : 'warning'),

            Stat::make('Siap Diambil (Pickup)', $fnbReadyPickup . ' pesanan')
                ->description('Menunggu diambil pelanggan')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('primary'),
        ];
    }
}
