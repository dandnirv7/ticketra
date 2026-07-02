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

        $this->command?->info("🎬 Membuat Jadwal Tayang untuk {$films->count()} film di {$studios->count()} studio...");

        $timeSlots = ['10:00', '12:45', '15:30', '18:15', '21:00'];

        $today = Carbon::today();

        $createdCount = 0;

        for ($day = -1; $day <= 6; $day++) {
            $currentDate = $today->copy()->addDays($day);
            $isPast = $day < 0;
            $label = $isPast ? 'kemarin' : ($day === 0 ? 'hari ini' : "H+{$day}");

            foreach ($studios as $studio) {
                $harga = $this->resolveHarga($studio->tipe, $currentDate->isWeekend());

                $slotsForStudio = collect($timeSlots)->map(function ($slot) use ($currentDate) {
                    return Carbon::parse($currentDate->format('Y-m-d') . ' ' . $slot);
                });

                $availableSlots = $slotsForStudio->filter(function ($waktuMulai) {
                    return $waktuMulai->copy()->addMinutes(150) >= Carbon::now();
                });

                if ($availableSlots->isEmpty() && !$isPast) {
                    continue;
                }

                $showingsTotal = $isPast ? count($timeSlots) : $availableSlots->count();
                $rotatedFilms = $films->shuffle();

                for ($i = 0; $i < $showingsTotal; $i++) {
                    $film = $rotatedFilms[$i % $rotatedFilms->count()];

                    $slotTime = $isPast
                        ? $slotsForStudio[$i]
                        : $availableSlots->values()[$i];

                    $durasi = ($film->durasi_menit > 0) ? $film->durasi_menit : 120;
                    $waktuSelesai = $slotTime->copy()->addMinutes($durasi + 20);

                    JadwalTayang::create([
                        'film_id' => $film->id,
                        'studio_id' => $studio->id,
                        'waktu_mulai' => $slotTime,
                        'waktu_selesai' => $waktuSelesai,
                        'harga' => $harga,
                        'status' => $isPast ? 'selesai' : 'terjadwal',
                    ]);

                    $createdCount++;
                }
            }
        }

        $this->command?->info("✅ Sukses membuat {$createdCount} jadwal tayang.");
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
