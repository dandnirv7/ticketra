<?php

namespace App\Filament\Resources\FilmResource\Pages;

use App\Filament\Concerns\HasFilmIndonesiaGenerator;
use App\Filament\Resources\FilmResource;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;

class CreateFilm extends CreateRecord
{
    use HasFilmIndonesiaGenerator;

    protected static string $resource = FilmResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return FilmResource::sanitizeFormData($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateFromFilmIndonesia')
                ->label('Generate dari Film Indonesia')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->modalHeading('Generate Metadata Film')
                ->modalDescription('Ketik judul film Indonesia. Sistem akan mengambil data dari filmindonesia.or.id dan menormalisasinya dengan AI. Hasil masih bisa diedit.')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    TextInput::make('judul')
                        ->label('Judul Film')
                        ->required()
                        ->placeholder('Contoh: Agak Laen, Pengabdi Setan, dll.')
                        ->helperText('Pastikan judul mendekati persis. Pencarian berbasis fuzzy match.'),
                ])
                ->action(function (array $data) {
                    $this->runFilmIndonesiaGeneration($data['judul']);
                }),
        ];
    }
}
