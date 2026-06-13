<?php

namespace Database\Factories;

use App\Models\JadwalTayang;
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
            'waktu_mulai' => $mulai,
            'waktu_selesai' => $selesai,
            'harga' => fake()->randomElement([35000, 45000, 60000, 85000]),
            'status' => 'terjadwal',
        ];
    }
}
