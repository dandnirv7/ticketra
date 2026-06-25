<?php

use App\Models\Booking;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $expiredBookings = Booking::where('status', 'locked')
        ->where('lock_expiry', '<', now())
        ->get();

    foreach ($expiredBookings as $booking) {
        $booking->update(['status' => 'cancelled']);
        $booking->statusKursis()->update(['status' => 'dilepas']);
    }
})->everyMinute();
