<?php

namespace App\Services;

use App\Models\JadwalTayang;
use Illuminate\Database\Eloquent\Collection;

final class BulkJadwalResult
{
    
    public function __construct(
        public readonly Collection $createdJadwals,
        public readonly array $summary,
    ) {}
}
