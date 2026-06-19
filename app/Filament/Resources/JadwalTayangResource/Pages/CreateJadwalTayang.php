<?php

namespace App\Filament\Resources\JadwalTayangResource\Pages;

use App\Filament\Resources\JadwalTayangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalTayang extends CreateRecord
{
    protected static string $resource = JadwalTayangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
