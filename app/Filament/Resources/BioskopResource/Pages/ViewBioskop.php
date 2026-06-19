<?php

namespace App\Filament\Resources\BioskopResource\Pages;

use App\Filament\Resources\BioskopResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBioskop extends ViewRecord
{
    protected static string $resource = BioskopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => BioskopResource::canEdit($this->record)),
            Actions\DeleteAction::make()
                ->visible(fn() => BioskopResource::canDelete($this->record)),
        ];
    }
}
