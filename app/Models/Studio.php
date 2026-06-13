<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    /** @use HasFactory<\Database\Factories\StudioFactory> */
    use HasFactory, SoftDeletes;

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
