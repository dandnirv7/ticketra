<?php

namespace App\Filament\Resources\JadwalTayangResource\Pages;

use App\Filament\Resources\JadwalTayangResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJadwalTayang extends ViewRecord
{
    protected static string $resource = JadwalTayangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => JadwalTayangResource::canEdit($this->record)),
            Actions\DeleteAction::make()
                ->visible(fn() => JadwalTayangResource::canDelete($this->record)),
        ];
    }
}
