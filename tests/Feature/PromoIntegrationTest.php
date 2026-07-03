<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\JadwalTayang;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_apply_valid_promo_code_at_checkout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $promo = Promo::create([
            'code' => 'DISKON10K',
            'title' => 'Diskon 10 Ribu',
            'desc' => 'Potongan 10rb',
            'type' => 'fixed',
            'discount_amount' => 10000,
            'min_purchase' => 30000,
            'is_active' => true,
        ]);

        $bioskop = \App\Models\Bioskop::factory()->create();
        $studio = \App\Models\Studio::factory()->create(['bioskop_id' => $bioskop->id]);
        $jadwal = JadwalTayang::factory()->create([
            'studio_id' => $studio->id,
            'harga' => 50000,
        ]);

        $booking = Booking::create([
            'booking_id' => 'TK-TESTPROMO',
            'user_id' => $user->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'locked',
            'total_price' => 50000,
            'service_fee' => 3000,
        ]);

        $response = $this->post(route('checkout.promo.apply', $booking->id), [
            'promo_code' => 'DISKON10K',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $booking->refresh();
        $this->assertEquals($promo->id, $booking->promo_id);
        $this->assertEquals(10000, $booking->discount_amount);
    }

    public function test_cannot_apply_invalid_or_inactive_promo_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bioskop = \App\Models\Bioskop::factory()->create();
        $studio = \App\Models\Studio::factory()->create(['bioskop_id' => $bioskop->id]);
        $jadwal = JadwalTayang::factory()->create([
            'studio_id' => $studio->id,
            'harga' => 50000,
        ]);
        $booking = Booking::create([
            'booking_id' => 'TK-TESTFAIL',
            'user_id' => $user->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'locked',
            'total_price' => 50000,
        ]);

        $response = $this->post(route('checkout.promo.apply', $booking->id), [
            'promo_code' => 'KODE_INVALID',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'promo_id' => null,
            'discount_amount' => 0,
        ]);
    }
}
