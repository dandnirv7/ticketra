<?php

namespace App\Filament\Resources\FilmResource\Pages;

use App\Filament\Resources\FilmResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFilm extends ViewRecord
{
    protected static string $resource = FilmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => FilmResource::canEdit($this->record)),
            Actions\DeleteAction::make()
                ->visible(fn () => FilmResource::canDelete($this->record)),
        ];
    }
}
