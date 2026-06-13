<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kursi extends Model
{
    /** @use HasFactory<\Database\Factories\KursiFactory> */
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'studio_id',
        'label_baris',
        'nomor_kursi',
        'tipe_kursi',
        'is_aktif'
    ];
    protected $casts = [
        'is_aktif' => 'boolean'
    ];

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }
}
