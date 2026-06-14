<?php

namespace Tests\Feature;

use App\Models\Bioskop;
use App\Models\Booking;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\PaymentWebhook;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private string $serverKey = 'SB-Mid-server-test-key-for-feature-tests';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('midtrans.server_key', $this->serverKey);
        config()->set('midtrans.skip_signature_verification', false);
    }

    private function makeBooking(array $overrides = []): Booking
    {
        $bioskop = Bioskop::factory()->create();
        $studio = Studio::factory()->create(['bioskop_id' => $bioskop->id]);
        $film = Film::factory()->create();
        $jadwal = JadwalTayang::factory()->create([
            'film_id' => $film->id,
            'studio_id' => $studio->id,
        ]);
        $user = User::factory()->create();

        return Booking::create(array_merge([
            'booking_id' => 'TKT-' . now()->timestamp . '-TEST',
            'user_id' => $user->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'pending_payment',
            'total_price' => 100000.00,
            'service_fee' => 5000.00,
            'fnb_total' => 0.00,
        ], $overrides));
    }

    private function buildPayload(Booking $booking, string $transactionStatus, string $fraudStatus = 'accept', ?string $transactionId = null): array
    {
        $transactionId ??= 'tx-' . uniqid('', true);

        $grossAmount = (string) (int) round((float) $booking->total_price);
        $statusCode = '200';
        $signatureKey = hash('sha512', $booking->booking_id . $statusCode . $grossAmount . $this->serverKey);

        return [
            'transaction_id' => $transactionId,
            'order_id' => $booking->booking_id,
            'gross_amount' => $grossAmount,
            'status_code' => $statusCode,
            'signature_key' => $signatureKey,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payment_type' => 'bank_transfer',
        ];
    }

    public function test_settlement_marks_booking_as_confirmed_and_records_webhook(): void
    {
        $booking = $this->makeBooking();
        $payload = $this->buildPayload($booking, 'settlement');

        $response = $this->postJson('/webhooks/midtrans', $payload);

        $response->assertOk()->assertJson(['status' => 'ok']);

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertNotNull($booking->paid_at);

        $this->assertDatabaseHas('payment_webhook', [
            'webhook_id' => $payload['transaction_id'],
            'transaction_id' => $payload['transaction_id'],
            'booking_id' => $booking->id,
            'status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ]);
    }

    public function test_invalid_signature_returns_401_and_does_not_change_booking(): void
    {
        $booking = $this->makeBooking();
        $payload = $this->buildPayload($booking, 'settlement');
        $payload['signature_key'] = str_repeat('0', 128);

        $response = $this->postJson('/webhooks/midtrans', $payload);

        $response->assertStatus(401)->assertJson(['error' => 'Invalid signature']);

        $booking->refresh();
        $this->assertSame('pending_payment', $booking->status);
        $this->assertNull($booking->paid_at);
        $this->assertDatabaseCount('payment_webhook', 0);
    }

    public function test_missing_order_id_returns_400(): void
    {
        $payload = [
            'transaction_id' => 'tx-no-order',
            'gross_amount' => '100000',
            'status_code' => '200',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson('/webhooks/midtrans', $payload);

        $response->assertStatus(400);
        $this->assertDatabaseCount('payment_webhook', 0);
    }

    public function test_unknown_order_id_returns_404(): void
    {
        $payload = [
            'transaction_id' => 'tx-unknown',
            'order_id' => 'TKT-DOES-NOT-EXIST',
            'gross_amount' => '100000',
            'status_code' => '200',
            'signature_key' => hash('sha512', 'TKT-DOES-NOT-EXIST' . '200' . '100000' . $this->serverKey),
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson('/webhooks/midtrans', $payload);

        $response->assertStatus(404)->assertJson(['error' => 'Booking not found']);
        $this->assertDatabaseCount('payment_webhook', 0);
    }

    public function test_replay_of_same_settlement_is_idempotent(): void
    {
        $booking = $this->makeBooking();
        $payload = $this->buildPayload($booking, 'settlement');

        $first = $this->postJson('/webhooks/midtrans', $payload);
        $first->assertOk()->assertJson(['status' => 'ok']);

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame(1, PaymentWebhook::where('webhook_id', $payload['transaction_id'])->count());

        $second = $this->postJson('/webhooks/midtrans', $payload);
        $second->assertOk()->assertJson(['status' => 'already_processed']);

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame(1, PaymentWebhook::where('webhook_id', $payload['transaction_id'])->count());
    }

    public function test_deny_status_marks_booking_as_failed(): void
    {
        $booking = $this->makeBooking();
        $payload = $this->buildPayload($booking, 'deny');

        $response = $this->postJson('/webhooks/midtrans', $payload);

        $response->assertOk()->assertJson(['status' => 'ok']);

        $booking->refresh();
        $this->assertSame('failed', $booking->status);
        $this->assertNull($booking->paid_at);
        $this->assertDatabaseHas('payment_webhook', [
            'webhook_id' => $payload['transaction_id'],
            'status' => 'deny',
        ]);
    }

    public function test_cancel_status_marks_booking_as_cancelled(): void
    {
        $booking = $this->makeBooking();
        $payload = $this->buildPayload($booking, 'cancel');

        $response = $this->postJson('/webhooks/midtrans', $payload);

        $response->assertOk()->assertJson(['status' => 'ok']);

        $booking->refresh();
        $this->assertSame('cancelled', $booking->status);
        $this->assertNull($booking->paid_at);
        $this->assertDatabaseHas('payment_webhook', [
            'webhook_id' => $payload['transaction_id'],
            'status' => 'cancel',
        ]);
    }
}
