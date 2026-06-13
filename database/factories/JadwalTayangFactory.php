<?php

namespace Database\Factories;

use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalTayang>
 */
class JadwalTayangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $durasi = fake()->numberBetween(90, 150);
        $mulai = fake()->dateTimeBetween('now', '+2 weeks');
        $selesai = (clone $mulai)->modify("+{$durasi} minutes");

        return [
            'film_id' => Film::factory(),
            'studio_id' => Studio::factory(),
            'waktu_mulai' => $mulai,
            'waktu_selesai' => $selesai,
            'harga' => fake()->randomElement([35000, 45000, 60000, 85000]),
            'status' => 'terjadwal',
        ];
    }
}
