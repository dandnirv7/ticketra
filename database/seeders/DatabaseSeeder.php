<?php

namespace Database\Seeders;

use App\Models\Bioskop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    
    public function run(): void
    {
        
        User::firstOrCreate(
            ['email' => 'user@ticketra.web.id'],
            [
                'name' => 'User',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'dandinirpana7@gmail.com'],
            [
                'name' => 'Dandi',
                'password' => bcrypt('password'),
            ]
        );

        
        
        if (Bioskop::count() === 0) {
            $this->call(BioskopStudioKursiSeeder::class);
        }

        
        
        
        
        $legacyFilmCount = \App\Models\Film::whereNull('slug')->count();
        if ($legacyFilmCount > 0) {
            $this->command?->warn("🗑️  Menghapus {$legacyFilmCount} film legacy (tanpa slug) dari seeder lama...");
            \App\Models\Film::whereNull('slug')->delete();
        }

        $this->call(FilmIndonesiaSeeder::class);
        $this->call(SnackSeeder::class);
        $this->call(JadwalTayangSeeder::class);
    }
}
