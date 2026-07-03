<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Bioskop extends Model
{
    /** @use HasFactory<\Database\Factories\BioskopFactory> */
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'fasilitas',
    ];

    protected $casts = [
        'fasilitas' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'kota', 'alamat'])
            ->logOnlyDirty();
    }

    public function studios()
    {
        return $this->hasMany(Studio::class);
    }
}
