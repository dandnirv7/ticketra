<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\JadwalTayangController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeatSelectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('jadwal', JadwalTayangController::class)->parameters([
        'jadwal' => 'jadwalTayang'
    ]);

    Route::get('/jadwal/{jadwalTayang}/kursi', [SeatSelectionController::class, 'index'])
        ->name('jadwal.kursi');

    Route::post('/jadwal/{jadwalTayang}/kursi', [SeatSelectionController::class, 'store'])
        ->name('jadwal.kursi.store');

    Route::get('/checkout/{booking}', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::get('/payment/success/{booking}', [PaymentController::class, 'success'])
        ->name('payment.success');

    Route::get('/payment/pending/{booking}', [PaymentController::class, 'pending'])
        ->name('payment.pending');

    Route::get('/payment/finish', [PaymentController::class, 'finish'])
        ->name('payment.finish');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
