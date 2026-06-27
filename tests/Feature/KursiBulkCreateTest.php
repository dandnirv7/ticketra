<?php

namespace Tests\Feature;

use App\Filament\Resources\KursiResource;
use App\Filament\Resources\KursiResource\Pages\CreateKursi;
use App\Models\Bioskop;
use App\Models\Kursi;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KursiBulkCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
    }

    public function test_can_bulk_create_kursis(): void
    {
        $bioskop = Bioskop::factory()->create();
        $studio = Studio::factory()->create(['bioskop_id' => $bioskop->id]);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $studio->id,
                'is_bulk' => true,
                'baris_mulai' => 'A',
                'baris_selesai' => 'C',
                'jumlah_kursi_per_baris' => 5,
                'tipe_kursi' => 'reguler',
                'is_aktif' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertEquals(15, Kursi::where('studio_id', $studio->id)->count());

        $this->assertDatabaseHas('kursis', [
            'studio_id' => $studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 1,
        ]);

        $this->assertDatabaseHas('kursis', [
            'studio_id' => $studio->id,
            'label_baris' => 'C',
            'nomor_kursi' => 5,
        ]);
    }

    public function test_bulk_create_ignores_existing_seats(): void
    {
        $bioskop = Bioskop::factory()->create();
        $studio = Studio::factory()->create(['bioskop_id' => $bioskop->id]);

        Kursi::create([
            'studio_id' => $studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 1,
            'tipe_kursi' => 'premium',
            'is_aktif' => true,
        ]);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $studio->id,
                'is_bulk' => true,
                'baris_mulai' => 'A',
                'baris_selesai' => 'A',
                'jumlah_kursi_per_baris' => 2,
                'tipe_kursi' => 'reguler',
                'is_aktif' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertEquals(2, Kursi::where('studio_id', $studio->id)->count());

        $this->assertDatabaseHas('kursis', [
            'studio_id' => $studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 1,
            'tipe_kursi' => 'premium',
        ]);

        $this->assertDatabaseHas('kursis', [
            'studio_id' => $studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 2,
            'tipe_kursi' => 'reguler',
        ]);
    }
}

