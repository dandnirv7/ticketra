<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load([
            'jadwalTayang.film',
            'jadwalTayang.studio.bioskop',
            'statusKursis.kursi'
        ]);

        if ($booking->lock_expiry && $booking->lock_expiry->isPast() && in_array($booking->status, ['locked', 'pending_payment'])) {
            $booking->update(['status' => 'cancelled']);
            $booking->statusKursis()->update(['status' => 'dilepas']);

            return redirect()->route('jadwal.kursi', $booking->jadwal_tayang_id)
                ->with('error', 'Waktu pemesanan Anda telah kedaluwarsa (lebih dari 10 menit). Silakan pilih kursi kembali.');
        }

        $snapToken = null;
        if (in_array($booking->status, ['locked', 'pending_payment'])) {
            $paymentService = new PaymentService();
            $snapToken = $paymentService->createTransaction($booking);
        }

        return view('checkout.index', compact('booking', 'snapToken'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
