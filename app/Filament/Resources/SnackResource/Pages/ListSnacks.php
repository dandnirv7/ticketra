<?php

namespace App\Filament\Resources\SnackResource\Pages;

use App\Filament\Resources\SnackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSnacks extends ListRecords
{
    protected static string $resource = SnackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
