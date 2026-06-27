<?php

namespace Tests\Feature;

use App\Models\Bioskop;
use App\Models\Kursi;
use App\Models\Studio;
use App\Models\User;
use App\Filament\Resources\KursiResource;
use App\Filament\Resources\KursiResource\Pages\CreateKursi;
use App\Filament\Resources\KursiResource\Pages\EditKursi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KursiResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Studio $studio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $bioskop = Bioskop::factory()->create();
        $this->studio = Studio::factory()->create(['bioskop_id' => $bioskop->id]);
    }

    public function test_can_render_create_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(KursiResource::getUrl('create'));
        $response->assertSuccessful();
    }

    public function test_can_create_kursi_with_valid_data(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $this->studio->id,
                'label_baris' => 'a',
                'nomor_kursi' => 10,
                'tipe_kursi' => 'reguler',
                'is_aktif' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('kursis', [
            'studio_id' => $this->studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 10,
            'tipe_kursi' => 'reguler',
            'is_aktif' => true,
        ]);
    }

    public function test_cannot_create_kursi_with_invalid_label_baris(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $this->studio->id,
                'label_baris' => 'A,B,C',
                'nomor_kursi' => 10,
            ])
            ->call('create')
            ->assertHasFormErrors(['label_baris' => 'regex']);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $this->studio->id,
                'label_baris' => 'A B',
                'nomor_kursi' => 10,
            ])
            ->call('create')
            ->assertHasFormErrors(['label_baris' => 'regex']);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $this->studio->id,
                'label_baris' => 'ABCDEF',
                'nomor_kursi' => 10,
            ])
            ->call('create')
            ->assertHasFormErrors(['label_baris' => 'max']);
    }

    public function test_cannot_create_duplicate_kursi(): void
    {
        $this->actingAs($this->admin);

        Kursi::create([
            'studio_id' => $this->studio->id,
            'label_baris' => 'A',
            'nomor_kursi' => 5,
            'tipe_kursi' => 'reguler',
            'is_aktif' => true,
        ]);

        Livewire::test(CreateKursi::class)
            ->fillForm([
                'studio_id' => $this->studio->id,
                'label_baris' => 'a',
                'nomor_kursi' => 5,
            ])
            ->call('create')
            ->assertHasFormErrors(['nomor_kursi']);
    }
}

