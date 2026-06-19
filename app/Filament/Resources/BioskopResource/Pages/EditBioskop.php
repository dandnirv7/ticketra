<?php

namespace App\Filament\Resources\BioskopResource\Pages;

use App\Filament\Resources\BioskopResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBioskop extends EditRecord
{
    protected static string $resource = BioskopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn() => BioskopResource::canDelete($this->record)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BioskopResource::sanitizeFormData($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
