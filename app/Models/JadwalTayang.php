<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalTayang extends Model
{
    /** @use HasFactory<\Database\Factories\JadwalTayangFactory> */
    use HasFactory, HasUuids, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['film_id', 'studio_id', 'waktu_mulai', 'waktu_selesai', 'harga', 'status'];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'harga' => 'decimal:2',
    ];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class)->withTrashed();
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }
}
