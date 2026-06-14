<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PaymentWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handleMidtrans(Request $request)
    {
        Log::info('=== WEBHOOK START ===');

        try {
            $payload = $request->getContent();
            Log::info('Raw payload received', ['length' => strlen($payload)]);

            $data = json_decode($payload, true);

            if (!$data) {
                Log::error('Invalid JSON', ['payload' => $payload]);
                return response()->json(['error' => 'Invalid JSON'], 400);
            }

            Log::info('Parsed data', ['order_id' => $data['order_id'] ?? 'N/A']);

            $orderId           = $data['order_id'] ?? null;
            $transactionId     = $data['transaction_id'] ?? null;
            $transactionStatus = $data['transaction_status'] ?? null;
            $fraudStatus       = $data['fraud_status'] ?? 'accept';
            $paymentType       = $data['payment_type'] ?? null;

            if (!$orderId || !$transactionId) {
                Log::error('Missing required fields', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                ]);
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            $skipVerify = config('midtrans.skip_signature_verification', true);
            Log::info('Signature verification', ['skip' => $skipVerify]);

            if (!$skipVerify) {
                $serverKey = config('midtrans.server_key');

                if (empty($serverKey)) {
                    Log::error('MIDTRANS_SERVER_KEY not configured');
                    return response()->json(['error' => 'Server misconfigured'], 500);
                }

                $statusCode   = $data['status_code'] ?? null;
                $grossAmount  = $data['gross_amount'] ?? null;
                $signatureKey = $data['signature_key'] ?? null;

                if (!$statusCode || !$grossAmount || !$signatureKey) {
                    Log::error('Missing signature fields');
                    return response()->json(['error' => 'Missing signature fields'], 400);
                }

                $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

                if (!hash_equals($expected, $signatureKey)) {
                    Log::error('Invalid signature', [
                        'expected' => substr($expected, 0, 20) . '...',
                        'got' => substr($signatureKey, 0, 20) . '...',
                    ]);
                    return response()->json(['error' => 'Invalid signature'], 401);
                }

                Log::info('Signature verified');
            }

            $booking = Booking::where('booking_id', $orderId)->first();

            if (!$booking) {
                Log::error('Booking not found', ['order_id' => $orderId]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            Log::info('Booking found', [
                'booking_id' => $booking->booking_id,
                'current_status' => $booking->status,
            ]);

            $finalStatus = $this->determineStatus((string) $transactionStatus, (string) $fraudStatus);

            Log::info('Status mapping', [
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'final_status' => $finalStatus,
            ]);

            $responseStatus = 'ok';

            DB::transaction(function () use ($booking, $data, $transactionId, $paymentType, $finalStatus, &$responseStatus) {

                $existingWebhook = PaymentWebhook::where('webhook_id', $transactionId)->first();

                if ($existingWebhook) {
                    if ($existingWebhook->status !== $data['transaction_status']) {
                        Log::info('Webhook status changed, updating', [
                            'old_status' => $existingWebhook->status,
                            'new_status' => $data['transaction_status'],
                        ]);

                        $existingWebhook->update([
                            'status' => $data['transaction_status'],
                            'payload' => $data,
                            'processed_at' => now(),
                        ]);

                        $responseStatus = 'updated';

                        $this->updateBookingStatus($booking, $finalStatus, $transactionId, $paymentType);
                    } else {
                        $responseStatus = 'already_processed';
                        Log::info('True duplicate webhook, skipping');
                    }
                } else {
                    PaymentWebhook::create([
                        'webhook_id'     => $transactionId,
                        'booking_id'     => $booking->id,
                        'transaction_id' => $transactionId,
                        'status'         => $data['transaction_status'] ?? 'unknown',
                        'payment_type'   => $paymentType,
                        'payload'        => $data,
                        'processed_at'   => now(),
                    ]);

                    Log::info('PaymentWebhook created');
                    $this->updateBookingStatus($booking, $finalStatus, $transactionId, $paymentType);
                    Log::info('Booking status updated');
                }
            });

            Log::info('=== WEBHOOK SUCCESS ===', ['response' => $responseStatus]);

            return response()->json(['status' => $responseStatus], 200);
        } catch (\Exception $e) {
            Log::error('=== WEBHOOK ERROR ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function determineStatus(string $transactionStatus, string $fraudStatus): string
    {
        Log::info('determineStatus called', [
            'transactionStatus' => $transactionStatus,
            'fraudStatus' => $fraudStatus,
        ]);

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                Log::info('Returning pending_payment (capture + challenge)');
                return 'pending_payment';
            } elseif ($fraudStatus == 'accept') {
                Log::info('Returning confirmed (capture + accept)');
                return 'confirmed';
            }
            Log::info('Returning pending_payment (capture + unknown fraud)');
            return 'pending_payment';
        } elseif ($transactionStatus == 'settlement') {
            Log::info('Returning confirmed (settlement)');
            return 'confirmed';
        } elseif ($transactionStatus == 'deny') {
            Log::info('Returning failed (deny)');
            return 'failed';
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
            Log::info('Returning cancelled (cancel/expire)');
            return 'cancelled';
        } elseif ($transactionStatus == 'failure') {
            Log::info('Returning failed (failure)');
            return 'failed';
        }

        Log::warning('Unknown status combination, returning pending_payment', [
            'transactionStatus' => $transactionStatus,
            'fraudStatus' => $fraudStatus,
        ]);
        return 'pending_payment';
    }

    private function updateBookingStatus(Booking $booking, string $finalStatus, string $transactionId, string $paymentType): void
    {
        Log::info('updateBookingStatus called', [
            'booking_id' => $booking->booking_id,
            'current_status' => $booking->status,
            'final_status' => $finalStatus,
        ]);

        $statusPriority = [
            'confirmed' => 4,
            'pending_payment' => 3,
            'locked' => 2,
            'failed' => 1,
            'cancelled' => 1,
        ];

        $currentPriority = $statusPriority[$booking->status] ?? 0;
        $newPriority = $statusPriority[$finalStatus] ?? 0;

        if ($newPriority < $currentPriority) {
            Log::warning('Skipping status downgrade', [
                'current' => $booking->status,
                'attempted' => $finalStatus,
            ]);
            return;
        }

        $updateData = ['status' => $finalStatus];

        if ($finalStatus === 'confirmed' && $booking->paid_at === null) {
            $updateData['paid_at'] = now();
        }

        $booking->update($updateData);

        Log::info('Booking updated', [
            'booking_id' => $booking->booking_id,
            'new_status' => $booking->fresh()->status,
            'new_paid_at' => $booking->fresh()->paid_at,
        ]);
    }
}
