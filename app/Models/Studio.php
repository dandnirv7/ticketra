<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    /** @use HasFactory<\Database\Factories\StudioFactory> */
    use HasFactory, HasUuids, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'bioskop_id',
        'nama',
        'tipe',
        'kapasitas',
        'layout_kursi',
    ];

    protected $casts = [
        'layout_kursi' => 'array',
        'kapasitas' => 'integer',
    ];

    public function bioskop(): BelongsTo
    {
        return $this->belongsTo(Bioskop::class);
    }
}
