<?php

namespace App\Filament\Widgets;

use App\Models\JadwalTayang;
use App\Models\StatusKursi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OccupancyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $jadwalsToday = JadwalTayang::query()
            ->whereBetween('waktu_mulai', [$todayStart, $todayEnd])
            ->get();

        $totalSeats = 0;
        $occupiedSeats = 0;

        foreach ($jadwalsToday as $jadwal) {
            $kapasitas = (int) ($jadwal->studio?->kapasitas ?? 0);
            $totalSeats += $kapasitas;

            $occupiedSeats += StatusKursi::query()
                ->where('jadwal_tayang_id', $jadwal->id)
                ->where('status', 'terjual')
                ->count();
        }

        $percent = $totalSeats > 0
            ? round(($occupiedSeats / $totalSeats) * 100, 1)
            : 0.0;

        $paymentOkToday = DB::table('bookings')
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$todayStart, $todayEnd])
            ->count();

        $paymentPendingToday = DB::table('bookings')
            ->where('status', 'pending_payment')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        $revenueToday = (float) DB::table('bookings')
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$todayStart, $todayEnd])
            ->sum('total_price');

        return [
            Stat::make('Okupansi Hari Ini', $percent . '%')
                ->description($occupiedSeats . ' / ' . $totalSeats . ' kursi terisi')
                ->descriptionIcon('heroicon-m-users')
                ->color($percent >= 70 ? 'success' : ($percent >= 40 ? 'warning' : 'danger')),

            Stat::make('Jadwal Tayang Hari Ini', $jadwalsToday->count())
                ->description('Across ' . $jadwalsToday->pluck('studio_id')->unique()->count() . ' studio')
                ->descriptionIcon('heroicon-m-film')
                ->color('info'),

            Stat::make('Booking Paid Hari Ini', $paymentOkToday)
                ->description($paymentPendingToday . ' masih pending')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Revenue Hari Ini', 'Rp ' . number_format($revenueToday, 0, ',', '.'))
                ->description('Total pembayaran sukses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
