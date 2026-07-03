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
            'statusKursis.kursi',
            'promo'
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

    public function applyPromo(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($booking->status, ['locked', 'pending_payment'])) {
            return back()->with('error', 'Promo hanya dapat digunakan pada pesanan yang belum dibayar.');
        }

        $request->validate([
            'promo_code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->promo_code));
        $promo = \App\Models\Promo::where('code', $code)->first();

        if (!$promo) {
            return back()->with('error', "Kode promo '{$code}' tidak ditemukan.");
        }

        $subtotal = (float) $booking->total_price + (float) $booking->fnb_total;
        $errorMsg = null;

        if (!$promo->isValidFor($subtotal, $errorMsg)) {
            return back()->with('error', $errorMsg ?? 'Kode promo tidak dapat digunakan.');
        }

        $discount = $promo->calculateDiscount($subtotal);

        $booking->update([
            'promo_id' => $promo->id,
            'discount_amount' => $discount,
        ]);

        return back()->with('success', "Promo '{$promo->title}' berhasil dipasang! Diskon Rp " . number_format($discount, 0, ',', '.'));
    }

    public function removePromo(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->update([
            'promo_id' => null,
            'discount_amount' => 0,
        ]);

        return back()->with('success', 'Promo berhasil dihapus.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
