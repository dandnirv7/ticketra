<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function success(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        
        // Configure Midtrans server key and environment
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.sanitized');

        try {
            // Fetch transaction status directly from Midtrans API
            $status = \Midtrans\Transaction::status($booking->booking_id);
            $transactionStatus = $status->transaction_status ?? null;
            $fraudStatus = $status->fraud_status ?? 'accept';

            // Confirm booking only if Midtrans reports successful payment
            if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                if ($booking->status !== 'confirmed') {
                    $booking->update([
                        'status' => 'confirmed',
                        'paid_at' => now(),
                    ]);
                    $booking->statusKursis()->update(['status' => 'terjual']);
                }
            }
        } catch (\Exception $e) {
            // Ignore API exceptions to just render the view with current status
        }

        $booking->load(['jadwalTayang.film', 'statusKursis.kursi']);

        return view('payment.success', compact('booking'));
    }

    public function pending(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load(['jadwalTayang.film', 'statusKursis.kursi']);

        return view('payment.pending', compact('booking'));
    }

    public function finish()
    {
        return redirect()->route('dashboard');
    }
}
