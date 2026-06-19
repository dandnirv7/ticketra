<?php

namespace App\Filament\Resources\KursiResource\Pages;

use App\Filament\Resources\KursiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKursi extends ViewRecord
{
    protected static string $resource = KursiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => KursiResource::canEdit($this->record)),
            Actions\DeleteAction::make()
                ->visible(fn() => KursiResource::canDelete($this->record)),
        ];
    }
}
