<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\JadwalTayangController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeatSelectionController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SnacksController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/', [LandingController::class, 'index'])
    ->name('landing')
    ->metadata([
        'seo_title' => 'Ticketra. - Bebas Antre, Nonton Asyik | Pesan Tiket Bioskop Online',
        'seo_description' => 'Pesan tiket bioskop instan, pilih kursi favorit, dan kumpulkan promo eksklusif tanpa antre.',
    ]);

Route::get('/auth', function () {
    return view('auth');
})->name('auth.page')
    ->metadata([
        'seo_title' => 'Masuk / Daftar - Ticketra',
        'seo_description' => 'Masuk atau daftar akun Ticketra untuk mulai memesan tiket bioskop online.',
    ]);

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/film', [FilmController::class, 'index'])
        ->name('film.index')
        ->metadata([
            'seo_title' => 'Film Sedang & Akan Tayang di Bioskop - Ticketra',
            'seo_description' => 'Lihat daftar film terbaru dan akan tayang di bioskop favoritmu.',
        ]);

    Route::get('/film/{film}', [FilmController::class, 'show'])
        ->name('film.show')
        ->metadata([
            'seo_type' => 'movie',
        ]);

    Route::resource('jadwal', JadwalTayangController::class)->parameters([
        'jadwal' => 'jadwalTayang'
    ])->only(['index', 'show']);

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

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/snack/{snackOrder}', [BookingController::class, 'showSnack'])->name('bookings.snack.show');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/pdf', [BookingController::class, 'downloadPdf'])
        ->name('bookings.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/snacks', [SnacksController::class, 'index'])->name('snacks.index');
    Route::post('/snacks/checkout', [SnacksController::class, 'checkout'])->name('snacks.checkout');
    Route::get('/snacks/checkout/{snackOrder}', [SnacksController::class, 'showCheckout'])->name('snacks.checkout.show');
    Route::get('/snacks/payment/success/{snackOrder}', [SnacksController::class, 'paymentSuccess'])->name('snacks.payment.success');
    Route::get('/snacks/payment/pending/{snackOrder}', [SnacksController::class, 'paymentPending'])->name('snacks.payment.pending');
});

Route::post('/webhooks/midtrans', [App\Http\Controllers\PaymentWebhookController::class, 'handleMidtrans'])
    ->name('webhooks.midtrans');

Route::post('email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

require __DIR__ . '/auth.php';
