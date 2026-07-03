<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Promo extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $fillable = [
        'code',
        'title',
        'desc',
        'emoji',
        'type',
        'discount_amount',
        'min_purchase',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'max_discount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'max_discount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'title', 'type', 'discount_amount', 'min_purchase', 'max_uses', 'is_active'])
            ->logOnlyDirty();
    }

    public function isValidFor(float $subtotal, ?string &$errorMsg = null): bool
    {
        if (!$this->is_active) {
            $errorMsg = 'Promo sudah tidak aktif.';
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            $errorMsg = 'Promo belum dimulai.';
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            $errorMsg = 'Promo sudah kedaluwarsa.';
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            $errorMsg = 'Batas kuota penggunaan promo ini telah habis.';
            return false;
        }

        if ($subtotal < (float) $this->min_purchase) {
            $errorMsg = 'Minimal transaksi untuk menggunakan promo ini adalah Rp ' . number_format($this->min_purchase, 0, ',', '.');
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ((float) $this->discount_amount / 100);
            return $discount;
        }

        return min((float) $this->discount_amount, $subtotal);
    }
}
