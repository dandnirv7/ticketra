<?php

namespace App\Services;

use Carbon\Carbon;

final class BulkJadwalInput
{
    
    public function __construct(
        public readonly string $filmId,
        public readonly array $studioIds,
        public readonly Carbon $tanggalMulai,
        public readonly Carbon $tanggalSelesai,
        public readonly array $hariAktif,
        public readonly array $jamTayang,
        public readonly array $pricing,
        public readonly int $bufferMenit = 30,
        public readonly array $skipDates = [],
    ) {}
}
