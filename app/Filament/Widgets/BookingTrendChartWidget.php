<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Booking & Revenue (7 Hari Terakhir)';

    protected static ?string $description = null;

    protected static ?string $maxHeight = '260px';

    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            7 => '7 hari',
            14 => '14 hari',
            30 => '30 hari',
        ];
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?: 7);

        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $rows = Booking::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(total_price) as revenue')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        $revenueRows = Booking::query()
            ->selectRaw('DATE(paid_at) as date, SUM(total_price) as revenue')
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$start, $end])
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->pluck('revenue', 'date')
            ->toArray();

        $labels = [];
        $bookingCounts = [];
        $revenues = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $labels[] = Carbon::parse($date)->translatedFormat('d M');
            $bookingCounts[] = (int) ($rows[$date] ?? 0);
            $revenues[] = (float) ($revenueRows[$date] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Booking',
                    'data' => $bookingCounts,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'tension' => 0.3,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Revenue (IDR)',
                    'data' => $revenues,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'tension' => 0.3,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => ['display' => true, 'text' => 'Booking'],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => ['display' => true, 'text' => 'Revenue'],
                    'beginAtZero' => true,
                    'grid' => ['drawOnChartArea' => false],
                ],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
