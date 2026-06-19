<?php

namespace App\Filament\Resources\StudioResource\Pages;

use App\Filament\Resources\StudioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudio extends ViewRecord
{
    protected static string $resource = StudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => StudioResource::canEdit($this->record)),
            Actions\DeleteAction::make()
                ->visible(fn() => StudioResource::canDelete($this->record)),
        ];
    }
}
