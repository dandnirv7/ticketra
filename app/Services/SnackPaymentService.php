<?php

namespace App\Services;

use App\Models\SnackOrder;
use Midtrans\Config;
use Midtrans\Snap;

class SnackPaymentService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.sanitized');
        Config::$is3ds = true;
        Config::$curlOptions = config('midtrans.curl_options', []);
    }

    public function createTransaction(SnackOrder $order): string
    {
        $user = $order->user;
        $items = $order->items;

        $itemDetails = $items->map(fn($item) => [
            'id' => 'SNACK-' . $item->snack_id,
            'price' => (int) $item->price,
            'quantity' => $item->qty,
            'name' => $item->snack_name,
        ])->toArray();

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => (int) $order->fnb_total,
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
                'credit_card',
            ],
            'callbacks' => [
                'finish' => route('payment.finish'),
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
