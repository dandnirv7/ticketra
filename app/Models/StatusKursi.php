<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusKursi extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kursi_id',
        'jadwal_tayang_id',
        'status',
        'booking_id',
        'locked_at',
        'lock_expiry'
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'lock_expiry' => 'datetime',
    ];

    public function kursi(): BelongsTo
    {
        return $this->belongsTo(Kursi::class);
    }
    public function jadwalTayang(): BelongsTo
    {
        return $this->belongsTo(JadwalTayang::class);
    }
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
