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
        'locked_at',
        'lock_expiry',
        'paid_at'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'fnb_total' => 'decimal:2',
        'locked_at' => 'datetime',
        'lock_expiry' => 'datetime',
        'paid_at' => 'datetime',
    ];

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
}
