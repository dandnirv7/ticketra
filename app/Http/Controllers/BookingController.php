<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS1DFacade;
use Milon\Barcode\Facades\DNS2DFacade;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

        $bookings = Booking::where('user_id', $userId)
            ->with([
                'jadwalTayang.film',
                'jadwalTayang.studio.bioskop',
                'statusKursis.kursi'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'upcoming' => Booking::where('user_id', $userId)
                ->whereIn('status', ['confirmed', 'pending_payment'])
                ->whereHas('jadwalTayang', function ($q) {
                    $q->where('waktu_mulai', '>', now());
                })
                ->count(),
            'completed' => Booking::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->whereHas('jadwalTayang', function ($q) {
                    $q->where('waktu_mulai', '<', now());
                })
                ->count(),
            'cancelled' => Booking::where('user_id', $userId)
                ->whereIn('status', ['cancelled', 'failed'])
                ->count(),
        ];

        return view('bookings.index', compact('bookings', 'stats'));
    }


    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load([
            'jadwalTayang.film',
            'jadwalTayang.studio.bioskop',
            'statusKursis.kursi'
        ]);

        $qrData = "TICKETRA:{$booking->booking_id}|{$booking->user_id}|{$booking->jadwal_tayang_id}";

        $qrCode = DNS2DFacade::getBarcodeSVG($qrData, 'QRCODE', 4, 4);

        return view('bookings.show', compact('booking', 'qrCode'));
    }


    public function downloadPdf(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, "Unauthorized action.");
        }

        if ($booking->status !== "confirmed") {
            return redirect()->route("bookings.show", $booking->id)
                ->with("error", "E-Ticket hanya tersedia untuk booking yang sudah dibayar.");
        }

        $jadwal = $booking->jadwalTayang;
        if ($jadwal && $jadwal->waktu_selesai && $jadwal->waktu_selesai->copy()->addHours(2)->isPast()) {
            return redirect()->route("bookings.show", $booking->id)
                ->with("error", "E-Ticket tidak dapat diunduh karena jadwal tayang sudah lewat.");
        }

        $booking->load([
            "jadwalTayang.film",
            "jadwalTayang.studio.bioskop",
            "statusKursis.kursi",
            "paymentWebhooks"
        ]);

        $qrData = "TICKETRA:{$booking->booking_id}|{$booking->user_id}|{$booking->jadwal_tayang_id}";
        $qrCode = DNS2DFacade::getBarcodePNG(
            $qrData,
            "QRCODE",
            10,
            10,
            [0, 0, 0],
        );

        $barcode = DNS1DFacade::getBarcodePNG(
            $booking->booking_id,
            "C128",
            2,
            90,
            [0, 0, 0]
        );

        $nomorOrder = $booking->paymentWebhooks()
            ->orderBy("created_at")
            ->value("transaction_id");

        $seatsList = $booking->statusKursis
            ->map(fn($sk) => $sk->kursi->label_baris . $sk->kursi->nomor_kursi)
            ->implode(", ");

        $genre = $jadwal->film->genre;

        $style = request()->query("style", "clean");
        $allowedStyles = ["clean" => "bookings.pdf.clean", "neo" => "bookings.pdf.neobrutalism"];
        $view = $allowedStyles[$style] ?? $allowedStyles["clean"];

        $pdf = Pdf::loadView($view, compact(
            "booking",
            "qrCode",
            "barcode",
            "nomorOrder",
            "seatsList",
            "genre"
        ))
            ->setPaper("a4", "portrait")
            ->setOption("dpi", 150);

        $filename = "e-ticket-{$booking->booking_id}.pdf";
        return $pdf->download($filename);
    }
}
