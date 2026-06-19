<?php

namespace App\Filament\Resources\JadwalTayangResource\Pages;

use App\Filament\Resources\JadwalTayangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalTayangs extends ListRecords
{
    protected static string $resource = JadwalTayangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
