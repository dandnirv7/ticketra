<?php

namespace App\Filament\Resources\KursiResource\Pages;

use App\Filament\Resources\KursiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKursi extends CreateRecord
{
    protected static string $resource = KursiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
