<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\JadwalTayang;
use App\Models\StatusKursi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeatSelectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(JadwalTayang $jadwalTayang)
    {
        $jadwalTayang->load([
            'film',
            'studio.bioskop',
            'studio.kursis'
        ]);

        return view('seat-selection.index', compact('jadwalTayang'));
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
    public function store(StoreBookingRequest $request, JadwalTayang $jadwalTayang)
    {
        $user = auth()->user();
        $kursiIds = $request->input('kursi_ids');
        $totalPrice = count($kursiIds) * $jadwalTayang->harga;

        $existingStatuses = StatusKursi::whereIn('kursi_id', $kursiIds)
            ->where('jadwal_tayang_id', $jadwalTayang->id)
            ->whereIn('status', ['dikunci', 'terjual'])
            ->with('kursi')
            ->get();

        if ($existingStatuses->isNotEmpty()) {
            $kursiTerpakai = $existingStatuses->map(function ($status) {
                return $status->kursi->label_baris . $status->kursi->nomor_kursi;
            })->implode(', ');

            return redirect()->back()
                ->with('error', "Maaf, kursi {$kursiTerpakai} sudah dipilih oleh pengguna lain. Silakan pilih kursi lain.")
                ->withInput();
        }

        $bookingIdString = DB::transaction(function () {
            $prefix = 'TK-' . now()->format('dmyHis') . '-';

            $lastBooking = Booking::where('booking_id', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $sequence = 1;
            if ($lastBooking) {
                $lastSeq = (int) substr($lastBooking->booking_id, -3);
                $sequence = $lastSeq + 1;
            }

            return $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });

        $booking = DB::transaction(function () use ($user, $jadwalTayang, $kursiIds, $totalPrice, $bookingIdString) {

            $booking = Booking::create([
                'booking_id' => $bookingIdString,
                'user_id' => $user->id,
                'jadwal_tayang_id' => $jadwalTayang->id,
                'status' => 'locked',
                'total_price' => $totalPrice,
                'locked_at' => now(),
                'lock_expiry' => now()->addMinutes(10),
            ]);

            foreach ($kursiIds as $kursiId) {
                StatusKursi::create([
                    'kursi_id' => $kursiId,
                    'jadwal_tayang_id' => $jadwalTayang->id,
                    'booking_id' => $booking->id,
                    'status' => 'dikunci',
                    'locked_at' => now(),
                    'lock_expiry' => now()->addMinutes(10),
                ]);
            }

            return $booking;
        });

        return redirect()->route('checkout.index', $booking->id)
            ->with('success', "Kursi berhasil dikunci! Silakan selesaikan pembayaran Anda.");
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
