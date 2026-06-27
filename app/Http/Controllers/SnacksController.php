<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use App\Models\SnackOrder;
use App\Services\SnackPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SnacksController extends Controller
{
    public function index(Request $request): View
    {
        $fnbItems = Snack::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'desc' => $item->desc,
                'price' => (int) $item->price,
                'emoji' => $item->emoji,
                'category' => $item->category,
                'status' => $item->status,
                'popular' => (int) $item->popular,
                'layout' => $item->layout,
            ];
        })->toArray();

        return view('snacks.index', compact('fnbItems'));
    }

    public function checkout(Request $request)
    {
        $cartInput = $request->input('cart');
        $cart = is_string($cartInput) ? json_decode($cartInput, true) : $cartInput;

        if (empty($cart) || !is_array($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong.');
        }

        $fnbTotal = 0;
        $cartItems = [];
        foreach ($cart as $cartItem) {
            $snack = Snack::find($cartItem['id']);
            if ($snack && $snack->status !== 'HABIS') {
                $price = (int) $snack->price;
                $qty = (int) $cartItem['qty'];
                $fnbTotal += $price * $qty;
                $cartItems[] = [
                    'snack' => $snack,
                    'qty' => $qty,
                    'price' => $price,
                ];
            }
        }

        if ($fnbTotal <= 0) {
            return redirect()->back()->with('error', 'Item di keranjang tidak valid.');
        }

        try {
            $order = DB::transaction(function () use ($cartItems, $fnbTotal) {
                $prefix = 'SN-' . now()->format('dmyHis') . '-';
                $lastOrder = SnackOrder::where('order_id', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();

                $sequence = 1;
                if ($lastOrder) {
                    $lastSeq = (int) substr($lastOrder->order_id, -3);
                    $sequence = $lastSeq + 1;
                }
                $orderIdString = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);

                $order = SnackOrder::create([
                    'order_id' => $orderIdString,
                    'user_id' => auth()->id(),
                    'status' => 'draft',
                    'fnb_total' => $fnbTotal,
                ]);

                foreach ($cartItems as $item) {
                    $order->items()->create([
                        'snack_id' => $item['snack']->id,
                        'snack_name' => $item['snack']->name,
                        'snack_emoji' => $item['snack']->emoji,
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                    ]);
                }

                return $order;
            });

            return redirect()->route('snacks.checkout.show', $order->id)
                ->with('success', 'Pesanan camilan berhasil dibuat! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showCheckout(SnackOrder $snackOrder)
    {
        if ($snackOrder->user_id !== auth()->id()) {
            abort(403);
        }

        $snackOrder->load('items');

        $snapToken = null;
        if (in_array($snackOrder->status, ['draft'])) {
            $service = app(SnackPaymentService::class);
            $snapToken = $service->createTransaction($snackOrder);

            $snackOrder->update(['status' => 'locked']);
            $snackOrder = $snackOrder->fresh();
        }

        return view('snacks.checkout', compact('snackOrder', 'snapToken'));
    }

    public function paymentSuccess(SnackOrder $snackOrder)
    {
        if ($snackOrder->user_id !== auth()->id()) {
            abort(403);
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.sanitized');

        try {
            $status = \Midtrans\Transaction::status($snackOrder->order_id);
            $transactionStatus = $status->transaction_status ?? null;
            $fraudStatus = $status->fraud_status ?? 'accept';

            if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                if ($snackOrder->status !== 'paid') {
                    $snackOrder->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
        }

        $snackOrder->load('items');

        return view('snacks.payment-success', compact('snackOrder'));
    }

    public function paymentPending(SnackOrder $snackOrder)
    {
        if ($snackOrder->user_id !== auth()->id()) {
            abort(403);
        }

        $snackOrder->load('items');

        return view('snacks.payment-pending', compact('snackOrder'));
    }
}

