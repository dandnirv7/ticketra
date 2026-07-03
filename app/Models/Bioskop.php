<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bioskop extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'kota', 'alamat'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


{
    /** @use HasFactory<\Database\Factories\BioskopFactory> */
    use HasFactory, HasUuids, SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'fasilitas',
        'jam_buka',
        'jam_tutup',
    ];

    protected $casts = [
        'fasilitas' => 'array',
        'jam_buka' => 'datetime:H:i',
        'jam_tutup' => 'datetime:H:i',
    ];

    public function studios()
    {
        return $this->hasMany(Studio::class);
    }
}
