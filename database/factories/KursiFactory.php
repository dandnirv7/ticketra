<?php

namespace Database\Factories;

use App\Models\Kursi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kursi>
 */
class KursiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label_baris' => fake()->randomLetter(),
            'nomor_kursi' => fake()->numberBetween(1, 20),
            'tipe_kursi' => 'reguler',
            'is_aktif' => true,
        ];
    }
}
