<?php

namespace Database\Factories;

use App\Models\Bioskop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bioskop>
 */
class BioskopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->company() . ' Cinema',
            'alamat' => fake()->address(),
            'kota' => fake()->randomElement(['Jakarta Selatan', 'Depok', 'Bogor', 'Tangerang', 'Bekasi']),
            'fasilitas' => ['parkir', 'food_court', 'mushola', 'wifi'],
            'jam_buka' => '10:00:00',
            'jam_tutup' => '23:00:00',
        ];
    }
}
