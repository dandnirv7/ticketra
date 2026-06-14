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
    }

    public function createTransaction(Booking $booking): string
    {
        $user = $booking->user;
        $jadwalTayang = $booking->jadwalTayang;


        $params = [
            'transaction_details' => [
                'order_id' => $booking->booking_id,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,

            ],
            'item_details' => [
                [
                    'id' => 'TICKET-' . $jadwalTayang->film->id,
                    'price' => (int) $jadwalTayang->harga,
                    'quantity' => $booking->statusKursis->count(),
                    'name' => 'Tiket ' . $jadwalTayang->film->judul,
                ]
            ],
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
