<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Film extends Model
{
    /** @use HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory, HasUuids, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'judul',
        'poster_url',
        'sinopsis',
        'durasi_menit',
        'rating',
        'genre',
        'tanggal_rilis',
        'sedang_tayang'
    ];

    protected $casts = [
        'durasi_menit' => 'integer',
        'rating' => 'decimal:1',
        'tanggal_rilis' => 'date',
        'sedang_tayang' => 'boolean',
    ];
}
