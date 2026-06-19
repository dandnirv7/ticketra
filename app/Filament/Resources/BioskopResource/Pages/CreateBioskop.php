<?php

namespace App\Filament\Resources\BioskopResource\Pages;

use App\Filament\Resources\BioskopResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBioskop extends CreateRecord
{
    protected static string $resource = BioskopResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BioskopResource::sanitizeFormData($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
