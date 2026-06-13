<?php

namespace Database\Seeders;

use App\Models\Bioskop;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@ticketra.web.id',
        ]);
        $films = Film::factory()->count(5)->create();
        $bioskops = Bioskop::factory()->count(3)->create();

        $bioskops->each(function ($bioskop) use ($films) {
            $studios = Studio::factory()->count(2)->create([
                'bioskop_id' => $bioskop->id,
            ]);

            $studios->each(function ($studio) use ($films) {
                JadwalTayang::factory()->count(5)->create([
                    'studio_id' => $studio->id,
                    'film_id' => $films->random()->id,
                ]);
            });
        });
    }
}
