<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BookingAttentionWidget;
use App\Filament\Widgets\BookingTrendChartWidget;
use App\Filament\Widgets\OccupancyStatsWidget;
use App\Filament\Widgets\PaymentMethodChartWidget;
use App\Filament\Widgets\RecentActivityWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Operasional';

    protected static ?string $navigationGroup = 'Ringkasan';

    protected static ?int $navigationSort = -2;

    protected static string $routePath = '/dashboard';

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 4,
        ];
    }

    /**
     * @return array<class-string<Widget> | \Filament\Widgets\WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            OccupancyStatsWidget::class,
            BookingTrendChartWidget::class,
            PaymentMethodChartWidget::class,
            BookingAttentionWidget::class,
            RecentActivityWidget::class,
        ];
    }
}
