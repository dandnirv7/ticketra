<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SnackOrder extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'user_id',
        'booking_id',
        'status',
        'fnb_total',
        'paid_at',
    ];

    protected $casts = [
        'fnb_total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                $user = $order->user;
                if ($user) {
                    if ($order->status === 'paid') {
                        $user->notify(new \App\Notifications\GeneralNotification(
                            'Pembayaran Snack Berhasil! 🍿',
                            'Pesanan snack ' . $order->order_id . ' telah berhasil dibayar.',
                            route('bookings.snack.show', $order->id)
                        ));
                    } elseif ($order->status === 'ready') {
                        $user->notify(new \App\Notifications\GeneralNotification(
                            'Snack Siap Diambil! 🌭',
                            'Pesanan snack ' . $order->order_id . ' siap diambil di counter bioskop.',
                            route('bookings.snack.show', $order->id)
                        ));
                    } elseif ($order->status === 'picked_up') {
                        $user->notify(new \App\Notifications\GeneralNotification(
                            'Snack Telah Diambil 🍿',
                            'Pesanan snack ' . $order->order_id . ' telah berhasil diambil. Selamat menikmati!',
                            route('bookings.snack.show', $order->id)
                        ));
                    }
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_id', 'status', 'fnb_total'])
            ->logOnlyDirty();
    }

    public function items(): HasMany
    {
        return $this->hasMany(SnackOrderItem::class);
    }
}
