<?php

namespace App\Filament\Resources\KursiResource\Pages;

use App\Filament\Resources\KursiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKursis extends ListRecords
{
    protected static string $resource = KursiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
