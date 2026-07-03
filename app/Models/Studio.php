<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Studio extends Model
{
    /** @use HasFactory<\Database\Factories\StudioFactory> */
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'tipe', 'kapasitas', 'bioskop_id'])
            ->logOnlyDirty();
    }

    public function bioskop(): BelongsTo
    {
        return $this->belongsTo(Bioskop::class);
    }

    public function kursis(): HasMany
    {
        return $this->hasMany(Kursi::class)->orderBy('label_baris')->orderBy('nomor_kursi');
    }
}
