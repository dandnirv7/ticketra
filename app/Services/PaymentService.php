<?php

namespace App\Services;

use App\Models\Booking;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.sanitized');
        Config::$is3ds = true;
        Config::$curlOptions = config('midtrans.curl_options', []);
    }

    public function createTransaction(Booking $booking): string
    {
        $user = $booking->user;
        $jadwalTayang = $booking->jadwalTayang;

        $ticketCount = $booking->statusKursis->count();
        $ticketPrice = (int) $jadwalTayang->harga;

        $itemDetails = [];
        if ($ticketCount > 0) {
            $itemDetails[] = [
                'id' => 'TICKET-' . $jadwalTayang->film->id,
                'price' => $ticketPrice,
                'quantity' => $ticketCount,
                'name' => 'Tiket ' . $jadwalTayang->film->judul,
            ];
        }

        $serviceFee = (int) ($booking->service_fee ?? 0);
        if ($serviceFee > 0) {
            $itemDetails[] = [
                'id' => 'SERVICE-FEE',
                'price' => $serviceFee,
                'quantity' => 1,
                'name' => 'Biaya Layanan',
            ];
        }

        $fnbTotal = (int) ($booking->fnb_total ?? 0);
        if ($fnbTotal > 0) {
            $itemDetails[] = [
                'id' => 'FNB-TOTAL',
                'price' => $fnbTotal,
                'quantity' => 1,
                'name' => 'Camilan (F&B)',
            ];
        }

        $grossAmount = ($ticketPrice * $ticketCount) + $serviceFee + $fnbTotal;

        $params = [
            'transaction_details' => [
                'order_id' => $booking->booking_id,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => $itemDetails,
            'enabled_payments' => [
                'gopay',
                'shopeepay',
                'qris',
                'bca_va',
                'bni_va',
                'bri_va',
                'mandiri_va',
                'credit_card'
            ],
            'callbacks' => [
                'finish' => route('payment.finish'),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }
}
