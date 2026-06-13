<?php

namespace Database\Seeders;

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

        $bioskops = \App\Models\Bioskop::factory()->count(3)->create();

        $films = \App\Models\Film::factory()->count(5)->create();

        $bioskops->each(function ($bioskop) use ($films) {
            $studios = \App\Models\Studio::factory()->count(2)->create([
                'bioskop_id' => $bioskop->id
            ]);

            $studios->each(function ($studio) use ($films) {
                \App\Models\JadwalTayang::factory()->count(5)->create([
                    'studio_id' => $studio->id,
                    'film_id' => fn() => $films->random()->id,
                ]);
            });
        });
    }
}
