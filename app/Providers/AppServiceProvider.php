<?php

namespace App\Providers;

use App\Models\Bioskop;
use App\Models\Booking;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Kursi;
use App\Models\PricingRule;
use App\Models\Promo;
use App\Models\RefundRequest;
use App\Models\Snack;
use App\Models\SnackOrder;
use App\Models\Studio;
use App\Observers\LoggableObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        $models = [
            Booking::class,
            Film::class,
            Snack::class,
            SnackOrder::class,
            Promo::class,
            PricingRule::class,
            JadwalTayang::class,
            Bioskop::class,
            Studio::class,
            Kursi::class,
            RefundRequest::class,
        ];

        foreach ($models as $model) {
            $model::observe(LoggableObserver::class);
        }
    }
}
