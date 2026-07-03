<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PricingRule extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $fillable = [
        'name',
        'day_type',
        'surcharge_type',
        'surcharge_amount',
        'is_active',
    ];

    protected $casts = [
        'surcharge_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'day_type', 'surcharge_type', 'surcharge_amount', 'is_active'])
            ->logOnlyDirty();
    }
}
