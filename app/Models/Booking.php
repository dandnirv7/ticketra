<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory, HasUuids, SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'booking_id',
        'user_id',
        'jadwal_tayang_id',
        'status',
        'total_price',
        'service_fee',
        'fnb_total',
        'is_checked_in',
        'checked_in_at',
        'promo_id',
        'discount_amount',
        'locked_at',
        'lock_expiry',
        'paid_at'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'fnb_total' => 'decimal:2',
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'discount_amount' => 'decimal:2',
        'locked_at' => 'datetime',
        'lock_expiry' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updated(function ($booking) {
            $user = $booking->user;
            if (!$user) return;

            // Cek status pembayaran tiket
            if ($booking->isDirty('status')) {
                if ($booking->status === 'confirmed') {
                    $user->notify(new \App\Notifications\GeneralNotification(
                        'Pembayaran Tiket Berhasil! 🎬',
                        'Tiket ' . $booking->booking_id . ' untuk film "' . optional($booking->jadwalTayang?->film)->title . '" telah berhasil dibayar.',
                        route('bookings.show', $booking->id)
                    ));
                } elseif ($booking->status === 'failed') {
                    $user->notify(new \App\Notifications\GeneralNotification(
                        'Pembayaran Tiket Gagal ❌',
                        'Transaksi untuk pemesanan tiket ' . $booking->booking_id . ' gagal diproses.',
                        route('bookings.show', $booking->id)
                    ));
                } elseif ($booking->status === 'cancelled') {
                    $user->notify(new \App\Notifications\GeneralNotification(
                        'Pemesanan Tiket Dibatalkan ⚠️',
                        'Pemesanan tiket ' . $booking->booking_id . ' telah dibatalkan karena batas waktu pembayaran habis.',
                        route('bookings.show', $booking->id)
                    ));
                }
            }

            // Cek status check-in tiket
            if ($booking->isDirty('is_checked_in') && $booking->is_checked_in) {
                $user->notify(new \App\Notifications\GeneralNotification(
                    'Check-in Berhasil! 🍿',
                    'Tiket ' . $booking->booking_id . ' berhasil di-checkin. Selamat menonton!',
                    route('bookings.show', $booking->id)
                ));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function jadwalTayang(): BelongsTo
    {
        return $this->belongsTo(JadwalTayang::class);
    }
    public function statusKursis(): HasMany
    {
        return $this->hasMany(StatusKursi::class);
    }

    public function paymentWebhooks(): HasMany
    {
        return $this->hasMany(PaymentWebhook::class);
    }

        public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }
}
