<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class TicketCheckin extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Check-in Tiket (Scan)';
    protected static ?int $navigationSort = 7;
    protected static ?string $title = 'Verifikasi & Check-in Tiket';

    protected static string $view = 'filament.pages.ticket-checkin';

    public ?string $searchCode = '';
    public ?Booking $scannedBooking = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'editor']);
    }

    public function processScan(): void
    {
        $code = trim($this->searchCode);
        if (empty($code)) {
            Notification::make()->warning()->title('Kode Kosong')->body('Masukkan Kode Booking / scan QR code')->send();
            return;
        }

        $booking = Booking::with(['user', 'jadwalTayang.film', 'jadwalTayang.studio.bioskop', 'statusKursis.kursi'])
            ->where('booking_id', $code)
            ->first();

        if (!$booking) {
            $this->scannedBooking = null;
            Notification::make()->danger()->title('Tiket Tidak Ditemukan')->body("Booking ID '{$code}' tidak valid atau tidak ada di sistem.")->send();
            return;
        }

        $this->scannedBooking = $booking;

        if ($booking->status !== 'confirmed') {
            Notification::make()->warning()->title('Tiket Belum Dibayar')->body("Status tiket saat ini adalah '{$booking->status}'. Hanya tiket yang sudah terkonfirmasi/paid yang bisa check-in.")->send();
            return;
        }

        if ($booking->is_checked_in) {
            Notification::make()->warning()->title('Tiket Sudah Pernah Check-in')->body("Tiket ini sudah di-scan pada: " . $booking->checked_in_at?->format('d M Y H:i'))->send();
            return;
        }

        Notification::make()->success()->title('Tiket Valid!')->body('Silakan periksa detail di bawah lalu konfirmasi check-in.')->send();
    }

    public function confirmCheckin(): void
    {
        if (!$this->scannedBooking) {
            return;
        }

        if ($this->scannedBooking->is_checked_in) {
            Notification::make()->warning()->title('Sudah Check-in')->send();
            return;
        }

        $this->scannedBooking->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
        ]);

        $this->scannedBooking->refresh();

        Notification::make()->success()->title('Check-in Berhasil!')->body("Tiket {$this->scannedBooking->booking_id} berhasil di-checkin pada " . now()->format('H:i:s'))->send();
    }

    public function resetScan(): void
    {
        $this->searchCode = '';
        $this->scannedBooking = null;
    }
}
