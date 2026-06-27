<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\JadwalTayang;
use App\Models\StatusKursi;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Operasional';

    protected static ?string $navigationGroup = 'Ringkasan';

    protected static ?int $navigationSort = -2;

    protected static string $routePath = '/dashboard';

    protected static string $view = 'filament.pages.dashboard';

    public string $filter = 'today';

    public function getSubheading(): ?string
    {
        $label = match ($this->filter) {
            '7_days' => '7 Hari Terakhir',
            '1_month' => '1 Bulan Terakhir',
            '1_year' => '1 Tahun Terakhir',
            default => 'Hari Ini',
        };
        return "Ringkasan performa bioskop ({$label})";
    }

    protected function getViewData(): array
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();
        $periodLabel = 'Hari Ini';

        if ($this->filter === '7_days') {
            $start = now()->subDays(6)->startOfDay();
            $periodLabel = '7 Hari';
        } elseif ($this->filter === '1_month') {
            $start = now()->subDays(29)->startOfDay();
            $periodLabel = '1 Bulan';
        } elseif ($this->filter === '1_year') {
            $start = now()->subDays(364)->startOfDay();
            $periodLabel = '1 Tahun';
        }

        $jadwalsPeriod = JadwalTayang::query()
            ->whereBetween('waktu_mulai', [$start, $end])
            ->get();

        $totalSeats = (int) JadwalTayang::query()
            ->whereBetween('waktu_mulai', [$start, $end])
            ->join('studios', 'jadwal_tayangs.studio_id', '=', 'studios.id')
            ->sum('studios.kapasitas');

        $jadwalIds = $jadwalsPeriod->pluck('id');

        $occupiedSeats = 0;
        if ($jadwalIds->isNotEmpty()) {
            $occupiedSeats = StatusKursi::query()
                ->whereIn('jadwal_tayang_id', $jadwalIds)
                ->where('status', 'terjual')
                ->count();
        }

        $occupancyPercent = $totalSeats > 0
            ? round(($occupiedSeats / $totalSeats) * 100, 1)
            : 0.0;

        $jadwalsCount = $jadwalsPeriod->count();
        $studiosCount = $jadwalsPeriod->pluck('studio_id')->unique()->count();

        $paymentOkToday = Booking::query()
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$start, $end])
            ->count();

        $paymentPendingToday = Booking::query()
            ->where('status', 'pending_payment')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $revenueToday = (float) Booking::query()
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('total_price');

        $bookingsToday = Booking::query()
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$start, $end])
            ->with('paymentWebhooks')
            ->get();

        $transferRevenue = 0;
        $qrisRevenue = 0;
        $vaRevenue = 0;

        foreach ($bookingsToday as $booking) {
            $latestWebhook = $booking->paymentWebhooks->sortByDesc('processed_at')->first();
            $paymentType = $latestWebhook?->payment_type ?? 'bank_transfer';

            if ($paymentType === 'qris') {
                $qrisRevenue += $booking->total_price;
            } elseif (str_contains($paymentType, 'va') || $paymentType === 'echannel') {
                $vaRevenue += $booking->total_price;
            } else {
                $transferRevenue += $booking->total_price;
            }
        }

        if ($revenueToday > 0) {
            $transferPercent = round(($transferRevenue / $revenueToday) * 100);
            $qrisPercent = round(($qrisRevenue / $revenueToday) * 100);
            $vaPercent = round(($vaRevenue / $revenueToday) * 100);
        } else {
            $transferPercent = 100;
            $qrisPercent = 0;
            $vaPercent = 0;
        }

        $recentBookings = Booking::query()
            ->with(['user', 'jadwalTayang.film'])
            ->where('updated_at', '>=', now()->subDay())
            ->latest('updated_at')
            ->limit(15)
            ->get();

        $activityItems = [];

        foreach ($recentBookings as $booking) {
            $filmTitle = $booking->jadwalTayang?->film?->judul ?? 'Film';
            $userName = $booking->user?->name ?? 'User';
            $subtitle = "{$userName} - {$filmTitle}";

            if ($booking->status === 'confirmed') {
                $activityItems[] = [
                    'title' => 'Booking dikonfirmasi',
                    'subtitle' => $subtitle,
                    'time' => $booking->updated_at,
                    'time_formatted' => $booking->updated_at->translatedFormat('d M H:i'),
                    'icon' => 'check',
                    'color' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                ];
                if ($booking->paid_at) {
                    $activityItems[] = [
                        'title' => 'Pembayaran berhasil',
                        'subtitle' => $subtitle,
                        'time' => $booking->paid_at,
                        'time_formatted' => $booking->paid_at->translatedFormat('d M H:i'),
                        'icon' => 'check',
                        'color' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    ];
                }
            } elseif ($booking->status === 'locked') {
                $activityItems[] = [
                    'title' => 'Booking dikunci',
                    'subtitle' => $subtitle,
                    'time' => $booking->locked_at ?? $booking->created_at,
                    'time_formatted' => ($booking->locked_at ?? $booking->created_at)->translatedFormat('d M H:i'),
                    'icon' => 'lock',
                    'color' => 'bg-blue-50 text-blue-600 border-blue-200',
                ];
            } elseif ($booking->status === 'pending_payment') {
                $activityItems[] = [
                    'title' => 'Pending payment',
                    'subtitle' => $subtitle,
                    'time' => $booking->created_at,
                    'time_formatted' => $booking->created_at->translatedFormat('d M H:i'),
                    'icon' => 'clock',
                    'color' => 'bg-amber-50 text-amber-600 border-amber-200',
                ];
            } elseif ($booking->status === 'cancelled' || $booking->status === 'failed') {
                $activityItems[] = [
                    'title' => 'Lock expired',
                    'subtitle' => $subtitle,
                    'time' => $booking->updated_at,
                    'time_formatted' => $booking->updated_at->translatedFormat('d M H:i'),
                    'icon' => 'lock',
                    'color' => 'bg-slate-50 text-slate-500 border-slate-200',
                ];
            }
        }

        usort($activityItems, function ($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        $activityItems = array_slice($activityItems, 0, 5);

        return [
            'occupancyPercent' => $occupancyPercent,
            'occupiedSeats' => $occupiedSeats,
            'totalSeats' => $totalSeats,
            'jadwalsCount' => $jadwalsCount,
            'studiosCount' => $studiosCount,
            'paymentOkToday' => $paymentOkToday,
            'paymentPendingToday' => $paymentPendingToday,
            'revenueToday' => $revenueToday,
            'transferRevenue' => $transferRevenue,
            'qrisRevenue' => $qrisRevenue,
            'vaRevenue' => $vaRevenue,
            'transferPercent' => $transferPercent,
            'qrisPercent' => $qrisPercent,
            'vaPercent' => $vaPercent,
            'activityItems' => $activityItems,
            'periodLabel' => $periodLabel,
        ];
    }
}

