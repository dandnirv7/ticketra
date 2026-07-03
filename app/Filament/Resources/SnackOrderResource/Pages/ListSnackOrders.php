<?php

namespace App\Filament\Resources\SnackOrderResource\Pages;

use App\Filament\Resources\SnackOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSnackOrders extends ListRecords
{
    protected static string $resource = SnackOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
