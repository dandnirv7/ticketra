<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\JadwalTayang;
use App\Models\SnackOrder;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class DailyOpsDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Operasional Harian';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.daily-ops-dashboard';

    public ?string $date = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'editor']);
    }

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->live()
                    ->afterStateUpdated(fn() => $this->refresh()),
            ]);
    }

    public function refresh(): void
    {
        // re-render
    }

    public function getDate(): string
    {
        return $this->date ?? now()->format('Y-m-d');
    }

    public function getTodayStats(): array
    {
        $date = $this->getDate();
        $dayStart = $date . ' 00:00:00';
        $dayEnd = $date . ' 23:59:59';

        $totalTickets = Booking::whereBetween('created_at', [$dayStart, $dayEnd])
            ->where('status', 'confirmed')
            ->count();

        $totalRevenue = (float) Booking::whereBetween('paid_at', [$dayStart, $dayEnd])
            ->where('status', 'confirmed')
            ->sum(\DB::raw('total_price + service_fee + fnb_total - discount_amount'));

        $totalDineIn = SnackOrder::whereBetween('created_at', [$dayStart, $dayEnd])
            ->where('status', 'paid')
            ->count();

        $fnbRevenue = (float) SnackOrder::whereBetween('paid_at', [$dayStart, $dayEnd])
            ->where('status', 'paid')
            ->sum('fnb_total');

        $totalSeats = \App\Models\Kursi::count();
        $bookedSeats = \App\Models\StatusKursi::whereHas('booking', function ($q) {
            $q->where('status', 'confirmed');
        })->whereDate('created_at', $date)->count();
        $occupancyRate = $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100, 1) : 0;

        return [
            'total_tickets' => $totalTickets,
            'total_revenue' => $totalRevenue,
            'total_dine_in' => $totalDineIn,
            'fnb_revenue' => $fnbRevenue,
            'occupancy_rate' => $occupancyRate,
            'booked_seats' => $bookedSeats,
            'total_seats' => $totalSeats,
        ];
    }

    public function getUpcomingShowtimes(): array
    {
        $date = $this->getDate();
        $now = now();

        return JadwalTayang::with(['film', 'studio.bioskop'])
            ->whereDate('waktu_mulai', $date)
            ->where('waktu_mulai', '>=', $now->copy()->subHours(1))
            ->orderBy('waktu_mulai', 'asc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function getPendingKitchenOrders(): int
    {
        return SnackOrder::where('status', 'paid')->count();
    }

    public function getRecentCheckedIn(): array
    {
        $date = $this->getDate();
        return Booking::with(['user', 'jadwalTayang.film'])
            ->where('is_checked_in', true)
            ->whereDate('checked_in_at', $date)
            ->orderBy('checked_in_at', 'desc')
            ->limit(15)
            ->get()
            ->toArray();
    }
}
