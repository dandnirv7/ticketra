<?php

namespace App\Filament\Resources\FilmResource\Pages;

use App\Filament\Concerns\HasFilmIndonesiaGenerator;
use App\Filament\Resources\FilmResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

class EditFilm extends EditRecord
{
    use HasFilmIndonesiaGenerator;

    protected static string $resource = FilmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Action::make('generateFromFilmIndonesia')
                ->label('Generate dari Film Indonesia')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->modalHeading('Generate Ulang Metadata Film')
                ->modalDescription('Ketik judul film. Sistem akan mengambil data dari filmindonesia.or.id dan menimpa field yang ada saat ini. Field yang sudah diedit manual akan hilang.')
                ->modalSubmitActionLabel('Generate & Timpa')
                ->color('warning')
                ->requiresConfirmation()
                ->form([
                    TextInput::make('judul')
                        ->label('Judul Film')
                        ->required()
                        ->placeholder('Contoh: Agak Laen, Pengabdi Setan, dll.'),
                ])
                ->action(function (array $data) {
                    $this->runFilmIndonesiaGeneration($data['judul']);
                }),
            Actions\DeleteAction::make()
                ->visible(fn () => FilmResource::canDelete($this->record)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return FilmResource::sanitizeFormData($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
