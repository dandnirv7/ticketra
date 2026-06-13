<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(3),
            'poster_url' => fake()->imageUrl(300, 450, 'movies'),
            'sinopsis' => fake()->paragraph(3),
            'durasi_menit' => fake()->numberBetween(90, 150),
            'rating' => fake()->randomFloat(1, 6.0, 9.9),
            'genre' => fake()->randomElement(['Action', 'Drama', 'Horror', 'Comedy']),
            'tanggal_rilis' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'sedang_tayang' => true,
        ];
    }
}
