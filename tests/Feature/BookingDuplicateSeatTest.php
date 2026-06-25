<?php

namespace Tests\Feature;

use App\Models\Bioskop;
use App\Models\Booking;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Kursi;
use App\Models\StatusKursi;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingDuplicateSeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_previously_released_seat_updates_status_kursi_without_duplicate_error(): void
    {
        $user = User::factory()->create();
        $bioskop = Bioskop::factory()->create();
        $studio = Studio::factory()->create(['bioskop_id' => $bioskop->id]);
        $film = Film::factory()->create();
        $jadwal = JadwalTayang::factory()->create([
            'film_id' => $film->id,
            'studio_id' => $studio->id,
            'harga' => 50000,
        ]);

        $kursi = Kursi::factory()->create([
            'studio_id' => $studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 1,
        ]);

        $this->actingAs($user);
        $response1 = $this->post(route('jadwal.kursi.store', $jadwal->id), [
            'kursi_ids' => [$kursi->id],
        ]);

        $response1->assertRedirect();
        
        $this->assertDatabaseHas('status_kursis', [
            'kursi_id' => $kursi->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'dikunci',
        ]);

        $booking = Booking::where('user_id', $user->id)->first();
        $this->assertNotNull($booking);

        $booking->update(['status' => 'cancelled']);
        StatusKursi::where('booking_id', $booking->id)->update(['status' => 'dilepas']);

        $this->assertDatabaseHas('status_kursis', [
            'kursi_id' => $kursi->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'dilepas',
        ]);

        $response2 = $this->post(route('jadwal.kursi.store', $jadwal->id), [
            'kursi_ids' => [$kursi->id],
        ]);

        $response2->assertRedirect();
        $response2->assertSessionHasNoErrors();

        $this->assertDatabaseHas('status_kursis', [
            'kursi_id' => $kursi->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'dikunci',
        ]);

        $count = StatusKursi::where('kursi_id', $kursi->id)
            ->where('jadwal_tayang_id', $jadwal->id)
            ->count();
        $this->assertEquals(1, $count);
    }
}
