<?php

namespace App\Filament\Resources\SnackOrderResource\Pages;

use App\Filament\Resources\SnackOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSnackOrder extends EditRecord
{
    protected static string $resource = SnackOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
