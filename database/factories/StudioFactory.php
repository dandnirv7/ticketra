<?php

namespace Database\Factories;

use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Studio>
 */
class StudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Studio ' . fake()->numberBetween(1, 10),
            'tipe' => fake()->randomElement(['reguler', 'premiere', 'imax']),
            'kapasitas' => fake()->randomElement([100, 150, 200, 250]),
            'layout_kursi' => [
                'baris' => 10,
                'kolom' => 15,
            ],
        ];
    }
}
