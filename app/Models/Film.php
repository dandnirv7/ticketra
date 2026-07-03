<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Film extends Model
{
    
    use HasFactory, HasUuids, SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'slug',
        'judul',
        'poster_url',
        'sinopsis',
        'durasi_menit',
        'rating',
        'genre',
        'tanggal_rilis',
        'sedang_tayang',
    ];

    protected $casts = [
        'durasi_menit' => 'integer',
        'rating' => 'decimal:1',
        'tanggal_rilis' => 'date',
        'sedang_tayang' => 'boolean',
    ];

    public function getPosterUrlAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}
