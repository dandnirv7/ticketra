<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'day_type', 'surcharge_type', 'surcharge_amount', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


{
    use HasFactory, HasUuids;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

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

    public static function calculateFinalPrice(float $basePrice, \DateTimeInterface $dateTime): float
    {
        $dayOfWeek = (int) $dateTime->format('N'); // 1 (Mon) - 7 (Sun)
        $isWeekend = ($dayOfWeek >= 6); // Sat or Sun

        $activeRules = static::where('is_active', true)->get();

        $finalPrice = $basePrice;

        foreach ($activeRules as $rule) {
            $applies = false;
            if ($rule->day_type === 'weekend' && $isWeekend) {
                $applies = true;
            } elseif ($rule->day_type === 'weekday' && !$isWeekend) {
                $applies = true;
            }

            if ($applies) {
                if ($rule->surcharge_type === 'percentage') {
                    $finalPrice += ($basePrice * ($rule->surcharge_amount / 100));
                } else {
                    $finalPrice += $rule->surcharge_amount;
                }
            }
        }

        return $finalPrice;
    }
}
