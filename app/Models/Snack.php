<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Snack extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'desc',
        'price',
        'emoji',
        'category',
        'status',
        'popular',
        'layout',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'status', 'category', 'popular'])
            ->logOnlyDirty();
    }

    public function bioskops(): BelongsToMany
    {
        return $this->belongsToMany(Bioskop::class, 'bioskop_snack', 'snack_id', 'bioskop_id');
    }
}
