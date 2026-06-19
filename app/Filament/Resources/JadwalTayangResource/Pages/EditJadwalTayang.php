<?php

namespace App\Filament\Resources\JadwalTayangResource\Pages;

use App\Filament\Resources\JadwalTayangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalTayang extends EditRecord
{
    protected static string $resource = JadwalTayangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn() => JadwalTayangResource::canDelete($this->record)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
