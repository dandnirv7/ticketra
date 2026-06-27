<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Studio;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JadwalTayangSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        JadwalTayang::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $films = Film::where('sedang_tayang', true)->get();

        if ($films->isEmpty()) {
            $this->command?->warn('⚠️ Tidak ada film dengan status sedang_tayang=true. Menggunakan semua film yang ada.');
            $films = Film::all();
        }

        if ($films->isEmpty()) {
            $this->command?->error('❌ Database kosong dari film. Jalankan FilmIndonesiaSeeder dulu!');
            return;
        }

        $studios = Studio::all();
        if ($studios->isEmpty()) {
            $this->command?->error('❌ Database kosong dari studio. Jalankan BioskopStudioKursiSeeder dulu!');
            return;
        }

        $this->command?->info('🎬 Membuat Jadwal Tayang untuk ' . $films->count() . ' film di ' . $studios->count() . ' studio...');

        $timeSlots = ['11:00', '13:30', '16:00', '18:30', '21:00'];

        $startDate = Carbon::today();
        $totalDays = 5;

        $createdCount = 0;

        for ($day = 0; $day < $totalDays; $day++) {
            $currentDate = $startDate->copy()->addDays($day);

            foreach ($studios as $studio) {
                $harga = $this->resolveHarga($studio->tipe, $currentDate->isWeekend());

                $shuffledFilms = $films->shuffle();
                $filmIndex = 0;

                foreach ($timeSlots as $slot) {
                    $film = $shuffledFilms[$filmIndex % $shuffledFilms->count()];
                    
                    $waktuMulai = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $slot);
                    $durasi = ($film->durasi_menit > 0) ? $film->durasi_menit : 120;
                    $waktuSelesai = $waktuMulai->copy()->addMinutes($durasi + 20);

                    JadwalTayang::create([
                        'film_id' => $film->id,
                        'studio_id' => $studio->id,
                        'waktu_mulai' => $waktuMulai,
                        'waktu_selesai' => $waktuSelesai,
                        'harga' => $harga,
                        'status' => 'terjadwal',
                    ]);

                    $createdCount++;
                    $filmIndex++;
                }
            }
        }

        $this->command?->info("✅ Sukses membuat {$createdCount} jadwal tayang baru.");
    }

    private function resolveHarga(string $tipe, bool $isWeekend): float
    {
        $tipe = strtolower($tipe);
        
        $hargaBase = 35000;
        
        if (str_contains($tipe, 'imax')) {
            $hargaBase = 60000;
        } elseif (str_contains($tipe, '4dx')) {
            $hargaBase = 75000;
        } elseif (str_contains($tipe, 'velvet')) {
            $hargaBase = 100000;
        } elseif (str_contains($tipe, 'gold') || str_contains($tipe, 'vip') || str_contains($tipe, 'premiere')) {
            $hargaBase = 80000;
        }

        if ($isWeekend) {
            $hargaBase += (str_contains($tipe, 'velvet') || str_contains($tipe, 'gold') || str_contains($tipe, 'premiere')) ? 20000 : 10000;
        }

        return (float) $hargaBase;
    }
}

