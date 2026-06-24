<?php

namespace App\Filament\Concerns;

use App\Services\FilmMetadataAssembler;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

trait HasFilmIndonesiaGenerator
{
    
    protected function runFilmIndonesiaGeneration(string $judul): void
    {
        
        try {
            
            $assembler = app(FilmMetadataAssembler::class);

            $result = $assembler->lookup($judul);

            if ($result === null) {
                Notification::make()
                    ->title('Film tidak ditemukan')
                    ->body("Tidak ada film dengan judul \"{$judul}\" di filmindonesia.or.id. Coba judul lain atau input manual.")
                    ->warning()
                    ->persistent()
                    ->send();
                return;
            }

            $formData = $assembler->toFilmFormData($result);

            $this->form->fill($formData);

            $filledFields = collect($formData)
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->keys()
                ->implode(', ');

            Notification::make()
                ->title('Berhasil generate metadata')
                ->body("Film \"{$result->judul}\" telah diisikan otomatis. Field terisi: {$filledFields}. Silakan review dan lengkapi jika perlu.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Generate gagal')
                ->body('Terjadi error: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
