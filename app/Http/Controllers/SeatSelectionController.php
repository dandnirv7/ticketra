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

        $occupiedSeats = StatusKursi::where('jadwal_tayang_id', $jadwalTayang->id)
            ->where(function ($query) {
                $query->where('status', 'terjual')
                    ->orWhere(function ($q) {
                        $q->where('status', 'dikunci')
                          ->where('lock_expiry', '>', now());
                    });
            })
            ->pluck('kursi_id')
            ->toArray();

        return view('seat-selection.index', compact('jadwalTayang', 'occupiedSeats'));
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

        try {
            $booking = DB::transaction(function () use ($user, $jadwalTayang, $kursiIds, $totalPrice) {
                $lockedJadwal = JadwalTayang::where('id', $jadwalTayang->id)->lockForUpdate()->firstOrFail();

                $existingStatuses = StatusKursi::whereIn('kursi_id', $kursiIds)
                    ->where('jadwal_tayang_id', $lockedJadwal->id)
                    ->where(function ($query) {
                        $query->where('status', 'terjual')
                            ->orWhere(function ($q) {
                                $q->where('status', 'dikunci')
                                  ->where('lock_expiry', '>', now());
                            });
                    })
                    ->with('kursi')
                    ->get();

                if ($existingStatuses->isNotEmpty()) {
                    $kursiTerpakai = $existingStatuses->map(function ($status) {
                        return $status->kursi->label_baris . $status->kursi->nomor_kursi;
                    })->implode(', ');

                    throw new \Exception("Maaf, kursi {$kursiTerpakai} sudah dipilih oleh pengguna lain. Silakan pilih kursi lain.");
                }

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
                $bookingIdString = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);

                $newBooking = Booking::create([
                    'booking_id' => $bookingIdString,
                    'user_id' => $user->id,
                    'jadwal_tayang_id' => $lockedJadwal->id,
                    'status' => 'locked',
                    'total_price' => $totalPrice,
                    'locked_at' => now(),
                    'lock_expiry' => now()->addMinutes(10),
                ]);

                foreach ($kursiIds as $kursiId) {
                    StatusKursi::updateOrCreate(
                        [
                            'kursi_id' => $kursiId,
                            'jadwal_tayang_id' => $lockedJadwal->id,
                        ],
                        [
                            'booking_id' => $newBooking->id,
                            'status' => 'dikunci',
                            'locked_at' => now(),
                            'lock_expiry' => now()->addMinutes(10),
                        ]
                    );
                }

                return $newBooking;
            });

            return redirect()->route('checkout.index', $booking->id)
                ->with('success', "Kursi berhasil dikunci! Silakan selesaikan pembayaran Anda.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
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
