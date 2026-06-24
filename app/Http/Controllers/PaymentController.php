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

        
        if (in_array($booking->status, ['locked', 'pending_payment'])) {
            $booking->update([
                'status' => 'confirmed',
                'paid_at' => now(),
            ]);
            $booking->statusKursis()->update(['status' => 'terjual']);
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
