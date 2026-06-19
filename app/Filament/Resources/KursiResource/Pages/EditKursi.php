<?php

namespace App\Filament\Resources\KursiResource\Pages;

use App\Filament\Resources\KursiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKursi extends EditRecord
{
    protected static string $resource = KursiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn() => KursiResource::canDelete($this->record)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
