<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\SnackOrder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function exportBookings(Request $request): StreamedResponse
    {
        abort_unless(auth()->user()?->role === 'admin', 403, 'Akses ditolak. Hanya admin yang bisa mengekspor laporan.');

        $startDate = $request->query('start_date') ? now()->parse($request->query('start_date'))->startOfDay() : now()->subMonth()->startOfDay();
        $endDate = $request->query('end_date') ? now()->parse($request->query('end_date'))->endOfDay() : now()->endOfDay();

        $bookings = Booking::with(['user', 'jadwalTayang.film', 'jadwalTayang.studio.bioskop'])
            ->where('status', 'confirmed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->orderBy('paid_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-booking-' . now()->format('YmdHis') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper excel reading
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'Booking ID',
                'Nama Pelanggan',
                'Email',
                'Film',
                'Bioskop',
                'Studio',
                'Waktu Tayang',
                'Total Tiket (Rp)',
                'Total F&B (Rp)',
                'Biaya Layanan (Rp)',
                'Total Bayar (Rp)',
                'Tanggal Bayar'
            ]);

            foreach ($bookings as $b) {
                $totalTicketPrice = (float) $b->total_price;
                $fnbTotal = (float) $b->fnb_total;
                $serviceFee = (float) $b->service_fee;
                $grandTotal = $totalTicketPrice + $fnbTotal + $serviceFee;

                fputcsv($file, [
                    $b->booking_id,
                    $b->user?->name ?? 'N/A',
                    $b->user?->email ?? 'N/A',
                    $b->jadwalTayang?->film?->judul ?? 'N/A',
                    $b->jadwalTayang?->studio?->bioskop?->nama ?? 'N/A',
                    $b->jadwalTayang?->studio?->nama ?? 'N/A',
                    $b->jadwalTayang?->waktu_mulai?->format('Y-m-d H:i') ?? 'N/A',
                    $totalTicketPrice,
                    $fnbTotal,
                    $serviceFee,
                    $grandTotal,
                    $b->paid_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSnackOrders(Request $request): StreamedResponse
    {
        abort_unless(auth()->user()?->role === 'admin', 403, 'Akses ditolak. Hanya admin yang bisa mengekspor laporan.');

        $startDate = $request->query('start_date') ? now()->parse($request->query('start_date'))->startOfDay() : now()->subMonth()->startOfDay();
        $endDate = $request->query('end_date') ? now()->parse($request->query('end_date'))->endOfDay() : now()->endOfDay();

        $orders = SnackOrder::with(['user', 'items'])
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-snack-' . now()->format('YmdHis') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'Order ID',
                'Nama Pelanggan',
                'Detail Item',
                'Total F&B (Rp)',
                'Tanggal Diambil/Selesai'
            ]);

            foreach ($orders as $order) {
                $itemDetails = $order->items->map(fn($item) => "{$item->snack_name} ({$item->qty}x)")->implode(', ');

                fputcsv($file, [
                    $order->order_id,
                    $order->user?->name ?? 'N/A',
                    $itemDetails,
                    (float) $order->fnb_total,
                    $order->updated_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
