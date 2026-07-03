<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\SnackOrder;
use App\Models\User;
use App\Models\JadwalTayang;
use App\Models\Bioskop;
use App\Models\Studio;
use App\Livewire\NotificationsDropdown;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_gets_notification_when_snack_order_is_ready()
    {
        $user = User::factory()->create();
        
        $snackOrder = SnackOrder::create([
            'order_id' => 'SN-TEST-123',
            'user_id' => $user->id,
            'status' => 'pending',
            'fnb_total' => 50000,
        ]);

        $this->assertEquals(0, $user->unreadNotifications()->count());

        // Update status ke ready
        $snackOrder->update(['status' => 'ready']);

        $this->assertEquals(1, $user->refresh()->unreadNotifications()->count());
        $this->assertEquals('Snack Siap Diambil! 🌭', $user->unreadNotifications()->first()->data['title']);
    }

    public function test_user_gets_notification_when_booking_is_confirmed()
    {
        $user = User::factory()->create();
        $bioskop = Bioskop::factory()->create();
        $studio = Studio::factory()->create(['bioskop_id' => $bioskop->id]);
        $jadwal = JadwalTayang::factory()->create(['studio_id' => $studio->id]);
        
        $booking = Booking::create([
            'booking_id' => 'TK-TEST-123',
            'user_id' => $user->id,
            'jadwal_tayang_id' => $jadwal->id,
            'status' => 'pending',
            'total_price' => 100000,
            'service_fee' => 2000,
            'fnb_total' => 0,
        ]);

        $this->assertEquals(0, $user->unreadNotifications()->count());

        // Update status ke confirmed
        $booking->update(['status' => 'confirmed']);

        $this->assertEquals(1, $user->refresh()->unreadNotifications()->count());
        $this->assertStringContainsString('Pembayaran Tiket Berhasil!', $user->unreadNotifications()->first()->data['title']);
    }

    public function test_livewire_notifications_dropdown_component()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Kirim dummy notification
        $user->notify(new \App\Notifications\GeneralNotification('Test Title', 'Test Message', '/test-url'));

        Livewire::test(NotificationsDropdown::class)
            ->assertSee('Test Title')
            ->assertSee('Test Message')
            ->call('markAllAsRead')
            ->assertSet('unreadCount', 0);
    }
}
