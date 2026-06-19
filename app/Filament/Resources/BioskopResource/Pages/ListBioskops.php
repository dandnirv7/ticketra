<?php

namespace App\Filament\Resources\BioskopResource\Pages;

use App\Filament\Resources\BioskopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBioskops extends ListRecords
{
    protected static string $resource = BioskopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
