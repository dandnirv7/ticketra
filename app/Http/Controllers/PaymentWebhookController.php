<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\PaymentWebhook;
use App\Models\SnackOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Milon\Barcode\Facades\DNS2DFacade;

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

            $rawOrderId        = $data['order_id'] ?? null;
            $transactionId     = $data['transaction_id'] ?? null;
            $transactionStatus = $data['transaction_status'] ?? null;
            $fraudStatus       = $data['fraud_status'] ?? 'accept';
            $paymentType       = $data['payment_type'] ?? null;

            $orderId = $rawOrderId;
            if ($rawOrderId) {
                $parts = explode('-', $rawOrderId);
                if (count($parts) > 1 && is_numeric(end($parts))) {
                    array_pop($parts);
                    $orderId = implode('-', $parts);
                }
            }

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
                if (str_starts_with($orderId, 'SN-')) {
                    return $this->handleSnackWebhook($data);
                }

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
            $shouldSendEmail = false;

            DB::transaction(function () use ($booking, $data, $transactionId, $paymentType, $finalStatus, &$responseStatus, &$shouldSendEmail) {

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

                        $shouldSendEmail = $this->updateBookingStatus($booking, $finalStatus, $transactionId, $paymentType);
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

                    $shouldSendEmail = $this->updateBookingStatus($booking, $finalStatus, $transactionId, $paymentType);
                    Log::info('Booking status updated');
                }
            });

            if ($shouldSendEmail) {
                $this->sendBookingConfirmationEmail($booking);
            }

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
                return 'pending_payment';
            } elseif ($fraudStatus == 'accept') {
                return 'confirmed';
            }
            return 'pending_payment';
        } elseif ($transactionStatus == 'settlement') {
            return 'confirmed';
        } elseif ($transactionStatus == 'deny') {
            return 'failed';
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
            return 'cancelled';
        } elseif ($transactionStatus == 'failure') {
            return 'failed';
        }

        return 'pending_payment';
    }

    
    private function updateBookingStatus(Booking $booking, string $finalStatus, string $transactionId, string $paymentType): bool
    {
        Log::info('updateBookingStatus called', [
            'booking_id' => $booking->booking_id,
            'current_status' => $booking->status,
            'final_status' => $finalStatus,
        ]);

        if (in_array($booking->status, ['confirmed', 'failed', 'cancelled'], true)) {
            Log::warning('Skipping status update from terminal state', [
                'current' => $booking->status,
                'attempted' => $finalStatus,
            ]);
            return false;
        }

        if ($booking->status === 'pending_payment' && $finalStatus === 'locked') {
            Log::warning('Skipping status update from pending_payment to locked', [
                'current' => $booking->status,
                'attempted' => $finalStatus,
            ]);
            return false;
        }

        $updateData = ['status' => $finalStatus];

        if ($finalStatus === 'confirmed' && $booking->paid_at === null) {
            $updateData['paid_at'] = now();
        }

        $booking->update($updateData);

        
        if ($finalStatus === 'confirmed') {
            $booking->statusKursis()->update(['status' => 'terjual']);
            Log::info('StatusKursi marked as terjual', ['booking_id' => $booking->id]);
        } elseif (in_array($finalStatus, ['failed', 'cancelled'])) {
            $booking->statusKursis()->update(['status' => 'dilepas']);
            Log::info('StatusKursi marked as dilepas', ['booking_id' => $booking->id]);
        }

        $freshBooking = $booking->fresh();

        Log::info('Booking updated', [
            'booking_id' => $booking->booking_id,
            'new_status' => $freshBooking->status,
            'new_paid_at' => $freshBooking->paid_at,
        ]);

        return $finalStatus === 'confirmed';
    }

    private function handleSnackWebhook(array $data): \Illuminate\Http\JsonResponse
    {
        $rawOrderId = $data['order_id'];
        $orderId = $rawOrderId;
        if ($rawOrderId) {
            $parts = explode('-', $rawOrderId);
            if (count($parts) > 1 && is_numeric(end($parts))) {
                array_pop($parts);
                $orderId = implode('-', $parts);
            }
        }

        $transactionId = $data['transaction_id'] ?? null;
        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? 'accept';

        Log::info('=== SNACK WEBHOOK ===', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
        ]);

        $order = SnackOrder::where('order_id', $orderId)->first();

        if (!$order) {
            Log::error('SnackOrder not found', ['order_id' => $orderId]);
            return response()->json(['error' => 'SnackOrder not found'], 404);
        }

        $finalStatus = $this->determineSnackStatus((string) $transactionStatus, (string) $fraudStatus);

        if ($finalStatus === 'paid' && $order->status !== 'paid') {
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            Log::info('SnackOrder paid', ['order_id' => $orderId]);
        } elseif (in_array($finalStatus, ['failed', 'cancelled']) && !in_array($order->status, ['paid', 'cancelled', 'failed'])) {
            $order->update(['status' => $finalStatus]);
            Log::info('SnackOrder failed/cancelled', ['order_id' => $orderId, 'status' => $finalStatus]);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function determineSnackStatus(string $transactionStatus, string $fraudStatus): string
    {
        if ($transactionStatus == 'capture') {
            return $fraudStatus == 'accept' ? 'paid' : 'pending';
        } elseif ($transactionStatus == 'settlement') {
            return 'paid';
        } elseif ($transactionStatus == 'deny') {
            return 'failed';
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
            return 'cancelled';
        } elseif ($transactionStatus == 'failure') {
            return 'failed';
        }

        return 'pending';
    }

    /**
     * 
     */
    private function sendBookingConfirmationEmail(Booking $booking): void
    {
        try {
            $booking->refresh();

            $booking->load([
                'user',
                'jadwalTayang.film',
                'jadwalTayang.studio.bioskop',
                'statusKursis.kursi',
            ]);

            if (!$booking->user || !$booking->user->email) {
                Log::error('Cannot send email: user or email not found', [
                    'booking_id' => $booking->booking_id,
                ]);
                return;
            }

            $qrData = "TICKETRA:{$booking->booking_id}|{$booking->user_id}|{$booking->jadwal_tayang_id}";
            $qrCodePng = DNS2DFacade::getBarcodePNG($qrData, 'QRCODE', 8, 8, [0, 0, 0]);
            $qrCodeBase64 = base64_encode($qrCodePng);

            Mail::to($booking->user->email)
                ->queue(new BookingConfirmationMail($booking, $qrCodeBase64));

            Log::info('Booking confirmation email queued successfully', [
                'email' => $booking->user->email,
                'booking_id' => $booking->booking_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'booking_id' => $booking->booking_id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

