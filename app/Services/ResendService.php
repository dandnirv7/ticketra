<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class ResendService
{
    protected ?string $apiKey;
    protected ?string $fromAddress;
    protected ?string $fromName;

    public function __construct()
    {
        $this->apiKey = config('services.resend.key');
        $this->fromAddress = config('mail.from.address', 'onboarding@resend.dev');
        $this->fromName = config('mail.from.name', 'Ticketra');

        $maskedKey = $this->apiKey ? substr($this->apiKey, 0, 7) . '...' : 'null';
        $keyLen = $this->apiKey ? strlen($this->apiKey) : 0;
        Log::info("ResendService initialized. Key prefix: {$maskedKey}, Length: {$keyLen}, From: {$this->fromName} <{$this->fromAddress}>");
    }

    /**
     * Get the Resend API client instance, or null if the API key is not configured.
     */
    protected function getClient()
    {
        if (empty($this->apiKey)) {
            return null;
        }
        return \Resend::client($this->apiKey);
    }

    /**
     * Send email verification notification.
     */
    public function sendVerification(User $user, string $verificationUrl): void
    {
        $client = $this->getClient();
        $from = "{$this->fromName} <{$this->fromAddress}>";

        if (!$client) {
            Log::warning('Resend API key is not configured. Email verification log:', [
                'to' => $user->email,
                'name' => $user->name,
                'verification_url' => $verificationUrl,
            ]);
            return;
        }

        try {
            $response = $client->emails->send([
                'from' => $from,
                'to' => [$user->email],
                'subject' => 'Verifikasi Email Anda - ' . $this->fromName,
                'template' => [
                    'id' => '06d4d1fb-6c01-4f83-b229-9e245c5ca42c',
                    'variables' => [
                        'NAME' => $user->name,
                        'VERIFICATION_URL' => $verificationUrl,
                    ],
                ],
            ]);
            Log::info('Verification email sent via Resend template to ' . $user->email . ' (Resend ID: ' . ($response->id ?? 'unknown') . ')');
        } catch (\Exception $e) {
            Log::error('Failed to send email verification via Resend', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send welcome email.
     */
    public function sendWelcome(User $user): void
    {
        $client = $this->getClient();
        $from = "{$this->fromName} <{$this->fromAddress}>";

        if (!$client) {
            Log::warning('Resend API key is not configured. Welcome email log:', [
                'to' => $user->email,
                'name' => $user->name,
            ]);
            return;
        }

        try {
            $response = $client->emails->send([
                'from' => $from,
                'to' => [$user->email],
                'subject' => 'Selamat Datang di ' . $this->fromName . '! 🍿',
                'template' => [
                    'id' => 'a3cc2a16-a644-4561-b24e-85dcff437087',
                    'variables' => [
                        'FILM_URL' => route('film.index'),
                        'ON_GOING_URL' => route('film.index'),
                        'NAME' => $user->name,
                    ],
                ],
            ]);
            Log::info('Welcome email sent via Resend template to ' . $user->email . ' (Resend ID: ' . ($response->id ?? 'unknown') . ')');
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email via Resend', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send password reset link.
     */
    public function sendPasswordReset(User $user, string $resetLink): void
    {
        $client = $this->getClient();
        $from = "{$this->fromName} <{$this->fromAddress}>";

        if (!$client) {
            Log::warning('Resend API key is not configured. Password reset log:', [
                'to' => $user->email,
                'name' => $user->name,
                'reset_link' => $resetLink,
            ]);
            return;
        }

        try {
            $response = $client->emails->send([
                'from' => $from,
                'to' => [$user->email],
                'subject' => 'Atur Ulang Kata Sandi - ' . $this->fromName,
                'template' => [
                    'id' => '37d7314b-d8bf-40e1-ba15-4a131e361415',
                    'variables' => [
                        'NAME' => $user->name,
                        'RESET_LINK' => $resetLink,
                    ],
                ],
            ]);
            Log::info('Password reset email sent via Resend template to ' . $user->email . ' (Resend ID: ' . ($response->id ?? 'unknown') . ')');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset via Resend', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send booking / order confirmation.
     */
    public function sendOrderConfirmation(Booking $booking, string $qrCodeBase64): void
    {
        $client = $this->getClient();
        $from = "{$this->fromName} <{$this->fromAddress}>";

        if (!$client) {
            Log::warning('Resend API key is not configured. Order confirmation log:', [
                'to' => $booking->user->email,
                'booking_id' => $booking->booking_id,
            ]);
            return;
        }

        $seatsList = $booking->statusKursis->map(fn($sk) => $sk->kursi->label_baris . $sk->kursi->nomor_kursi)->implode(', ');
        
        $studioName = $booking->jadwalTayang->studio->nama;
        if (!str_starts_with(strtolower($studioName), 'studio')) {
            $studioName = 'Studio ' . $studioName;
        }

        try {
            $response = $client->emails->send([
                'from' => $from,
                'to' => [$booking->user->email],
                'subject' => 'Konfirmasi Pemesanan Tiket ' . $booking->booking_id . ' - ' . $this->fromName,
                'template' => [
                    'id' => 'b49586b3-a1bc-4952-af41-8b9cb92b0cd1',
                    'variables' => [
                        'BOOKING_CODE' => $booking->booking_id,
                        'BOOKING_ID' => $booking->id,
                        'E_TICKET_LINK' => route('bookings.show', $booking->id, true),
                        'NAMA' => $booking->user->name,
                        'QR_CODE' => 'data:image/png;base64,' . $qrCodeBase64,
                        'SEAT' => $seatsList,
                        'SHOWDATE' => $booking->jadwalTayang->waktu_mulai->translatedFormat('D, d M Y'),
                        'SHOW_TIME' => $booking->jadwalTayang->waktu_mulai->format('H:i') . ' WIB',
                        'STUDIO_NAME' => $studioName,
                        'TITLE' => $booking->jadwalTayang->film->judul,
                    ],
                ],
            ]);
            Log::info('Order confirmation email sent via Resend template to ' . $booking->user->email . ' (Resend ID: ' . ($response->id ?? 'unknown') . ')');
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation via Resend', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
