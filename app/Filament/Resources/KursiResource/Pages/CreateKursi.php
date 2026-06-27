<?php

namespace App\Filament\Resources\KursiResource\Pages;

use App\Filament\Resources\KursiResource;
use App\Models\Kursi;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateKursi extends CreateRecord
{
    protected static string $resource = KursiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return KursiResource::sanitizeFormData($data);
    }

    protected function handleRecordCreation(array $data): Model
    {
        if (empty($data['is_bulk'])) {
            return parent::handleRecordCreation($data);
        }

        $studioId = $data['studio_id'];
        $barisMulai = ord($data['baris_mulai']);
        $barisSelesai = ord($data['baris_selesai']);
        $jumlahKursi = (int) $data['jumlah_kursi_per_baris'];
        $tipeKursi = $data['tipe_kursi'];
        $isAktif = (bool) $data['is_aktif'];

        $insertedRecord = null;

        DB::transaction(function () use ($studioId, $barisMulai, $barisSelesai, $jumlahKursi, $tipeKursi, $isAktif, &$insertedRecord) {
            for ($ascii = $barisMulai; $ascii <= $barisSelesai; $ascii++) {
                $labelBaris = chr($ascii);

                for ($nomor = 1; $nomor <= $jumlahKursi; $nomor++) {
                    $exists = Kursi::where('studio_id', $studioId)
                        ->where('label_baris', $labelBaris)
                        ->where('nomor_kursi', $nomor)
                        ->exists();

                    if (!$exists) {
                        $kursi = Kursi::create([
                            'id' => (string) Str::uuid(),
                            'studio_id' => $studioId,
                            'label_baris' => $labelBaris,
                            'nomor_kursi' => $nomor,
                            'tipe_kursi' => $tipeKursi,
                            'is_aktif' => $isAktif,
                        ]);

                        if (!$insertedRecord) {
                            $insertedRecord = $kursi;
                        }
                    }
                }
            }
        });

        if (!$insertedRecord) {
            $insertedRecord = Kursi::where('studio_id', $studioId)->first() ?? new Kursi();
        }

        return $insertedRecord;
    }
}

