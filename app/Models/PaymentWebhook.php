<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentWebhook extends Model
{
    use HasUuids;

    protected $table = 'payment_webhook';

    protected $fillable = [
        'webhook_id',
        'booking_id',
        'transaction_id',
        'status',
        'payment_type',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
