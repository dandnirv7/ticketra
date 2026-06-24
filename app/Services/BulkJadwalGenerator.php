<?php

namespace App\Services;

use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Studio;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkJadwalGenerator
{
    
    public function generate(BulkJadwalInput $input): BulkJadwalResult
    {
        $this->validateInput($input);

        
        $slots = $this->expandSlots($input);

        if (empty($slots)) {
            throw new \InvalidArgumentException(
                'Tidak ada slot yang bisa di-generate. Cek parameter tanggal, hari aktif, dan jam tayang.'
            );
        }

        
        $conflicts = $this->findConflicts($slots);

        if (! empty($conflicts)) {
            throw new BulkJadwalConflictException(
                'Ditemukan ' . count($conflicts) . ' konflik slot. Jadwal tidak di-generate.',
                $conflicts,
            );
        }

        
        $jadwalsToCreate = $this->buildJadwalData($slots, $input);

        
        $created = $this->bulkInsert($jadwalsToCreate);

        return new BulkJadwalResult(
            createdJadwals: $created,
            summary: [
                'total_requested' => count($slots),
                'total_created' => $created->count(),
                'film' => Film::find($input->filmId)->judul,
                'date_range' => $input->tanggalMulai->format('d M Y') . ' - ' . $input->tanggalSelesai->format('d M Y'),
                'studios' => Studio::whereIn('id', $input->studioIds)->pluck('nama')->all(),
            ],
        );
    }

    
    private function expandSlots(BulkJadwalInput $input): array
    {
        $slots = [];

        
        $period = CarbonPeriod::create($input->tanggalMulai, $input->tanggalSelesai);

        foreach ($period as $date) {
            
            
            if (! in_array($date->dayOfWeekIso, $input->hariAktif, true)) {
                continue;
            }

            
            if (in_array($date->format('Y-m-d'), $input->skipDates, true)) {
                continue;
            }

            
            foreach ($input->studioIds as $studioId) {
                foreach ($input->jamTayang as $time) {
                    [$hour, $minute] = explode(':', $time);
                    $waktuMulai = $date->copy()->setTime((int) $hour, (int) $minute);

                    $slots[] = [
                        'studio_id' => $studioId,
                        'waktu_mulai' => $waktuMulai,
                    ];
                }
            }
        }

        return $slots;
    }

    
    private function findConflicts(array $slots): array
    {
        if (empty($slots)) {
            return [];
        }

        $studioIds = array_unique(array_column($slots, 'studio_id'));
        $minDate = min(array_column($slots, 'waktu_mulai'))->copy()->startOfDay();
        $maxDate = max(array_column($slots, 'waktu_mulai'))->copy()->endOfDay();

        
        $existing = JadwalTayang::with('film:id,judul')
            ->whereIn('studio_id', $studioIds)
            ->whereBetween('waktu_mulai', [$minDate, $maxDate])
            ->get(['id', 'studio_id', 'waktu_mulai', 'film_id']);

        
        $existingIndex = [];
        foreach ($existing as $j) {
            $key = $j->studio_id . '|' . $j->waktu_mulai->format('Y-m-d H:i:s');
            $existingIndex[$key] = $j->film?->judul ?? '(film dihapus)';
        }

        $conflicts = [];
        foreach ($slots as $slot) {
            $key = $slot['studio_id'] . '|' . $slot['waktu_mulai']->format('Y-m-d H:i:s');
            if (isset($existingIndex[$key])) {
                $conflicts[] = [
                    'studio_id' => $slot['studio_id'],
                    'studio_nama' => Studio::find($slot['studio_id'])?->nama ?? '(studio dihapus)',
                    'waktu_mulai' => $slot['waktu_mulai']->format('Y-m-d H:i'),
                    'existing_film' => $existingIndex[$key],
                ];
            }
        }

        return $conflicts;
    }

    
    private function buildJadwalData(array $slots, BulkJadwalInput $input): array
    {
        $film = Film::findOrFail($input->filmId);
        $studios = Studio::whereIn('id', $input->studioIds)->get()->keyBy('id');
        $durasiFilmMenit = $film->durasi_menit ?: 90; 
        $bufferMenit = $input->bufferMenit;

        $data = [];
        foreach ($slots as $slot) {
            
            $studio = $studios[$slot['studio_id']];

            $waktuMulai = $slot['waktu_mulai'];
            $waktuSelesai = $waktuMulai->copy()->addMinutes($durasiFilmMenit + $bufferMenit);

            $harga = $this->calculatePrice(
                $waktuMulai,
                $input->pricing[$studio->id] ?? null,
            );

            $data[] = [
                'film_id' => $film->id,
                'studio_id' => $studio->id,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'harga' => $harga,
                'status' => 'terjadwal',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $data;
    }

    
    private function calculatePrice(Carbon $date, ?array $pricing): float
    {
        if ($pricing === null) {
            return 0;
        }

        $isHoliday = in_array($date->format('Y-m-d'), $this->holidayDates, true);
        $isWeekend = in_array($date->dayOfWeekIso, [5, 6, 7], true);

        
        if ($isHoliday && ($pricing['holiday'] ?? null) !== null) {
            return (float) $pricing['holiday'];
        }

        if ($isWeekend && ($pricing['weekend'] ?? null) !== null) {
            return (float) $pricing['weekend'];
        }

        return (float) $pricing['default'];
    }

    
    private array $holidayDates = [];

    
    private function bulkInsert(array $data): \Illuminate\Database\Eloquent\Collection
    {
        return DB::transaction(function () use ($data) {
            $jadwals = new \Illuminate\Database\Eloquent\Collection();

            
            
            foreach ($data as $row) {
                $jadwal = JadwalTayang::create($row);
                $jadwals->push($jadwal);
            }

            Log::info('BulkJadwalGenerator: bulk insert success', [
                'count' => $jadwals->count(),
            ]);

            return $jadwals;
        });
    }

    
    private function validateInput(BulkJadwalInput $input): void
    {
        if (empty($input->filmId)) {
            throw new \InvalidArgumentException('Film wajib dipilih.');
        }

        if (! Film::where('id', $input->filmId)->exists()) {
            throw new \InvalidArgumentException('Film tidak ditemukan.');
        }

        if (empty($input->studioIds)) {
            throw new \InvalidArgumentException('Minimal 1 studio harus dipilih.');
        }

        
        $existingStudioIds = Studio::whereIn('id', $input->studioIds)->pluck('id')->all();
        $missing = array_diff($input->studioIds, $existingStudioIds);
        if (! empty($missing)) {
            throw new \InvalidArgumentException(
                'Studio tidak ditemukan: ' . implode(', ', $missing)
            );
        }

        if ($input->tanggalSelesai->lt($input->tanggalMulai)) {
            throw new \InvalidArgumentException(
                'Tanggal selesai harus sama dengan atau setelah tanggal mulai.'
            );
        }

        if (empty($input->jamTayang)) {
            throw new \InvalidArgumentException('Minimal 1 jam tayang harus diisi.');
        }

        
        foreach ($input->jamTayang as $time) {
            if (! preg_match('/^\d{2}:\d{2}$/', $time)) {
                throw new \InvalidArgumentException(
                    'Format jam tayang tidak valid: ' . $time . ' (harus HH:MM)'
                );
            }
        }

        if (empty($input->hariAktif)) {
            throw new \InvalidArgumentException('Minimal 1 hari aktif harus dipilih.');
        }

        foreach ($input->hariAktif as $day) {
            if ($day < 1 || $day > 7) {
                throw new \InvalidArgumentException(
                    'Hari aktif tidak valid: ' . $day . ' (harus 1-7)'
                );
            }
        }

        if ($input->bufferMenit < 0) {
            throw new \InvalidArgumentException('Buffer time tidak boleh negatif.');
        }

        
        foreach ($input->studioIds as $studioId) {
            if (! isset($input->pricing[$studioId])) {
                throw new \InvalidArgumentException(
                    "Harga untuk studio ID {$studioId} belum diisi."
                );
            }

            $price = $input->pricing[$studioId];
            if (! isset($price['default']) || $price['default'] <= 0) {
                throw new \InvalidArgumentException(
                    "Harga default untuk studio {$studioId} wajib diisi dan > 0."
                );
            }
        }
    }
}
