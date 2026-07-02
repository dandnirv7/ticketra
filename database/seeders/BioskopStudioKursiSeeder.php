<?php

namespace Database\Seeders;

use App\Models\Bioskop;
use App\Models\Kursi;
use App\Models\Studio;
use Illuminate\Database\Seeder;

class BioskopStudioKursiSeeder extends Seeder
{
    public function run(): void
    {
        $studios = collect();
        $bioskops = [
            [
                'nama' => 'CGV Grand Indonesia',
                'alamat' => 'Grand Indonesia Mall, Lantai 8, Jl. M.H. Thamrin No.1, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'fasilitas' => ['parkir', 'food_court', 'mushola', 'wifi'],
                'jam_buka' => '10:00:00',
                'jam_tutup' => '23:00:00',
                'studios' => [
                    ['nama' => 'Studio 1 (IMAX)', 'tipe' => 'IMAX', 'kapasitas' => 150],
                    ['nama' => 'Studio 2 (4DX)', 'tipe' => '4DX', 'kapasitas' => 120],
                    ['nama' => 'Velvet Class', 'tipe' => 'Velvet', 'kapasitas' => 40],
                    ['nama' => 'Gold Class', 'tipe' => 'Gold', 'kapasitas' => 40],
                ],
            ],
            [
                'nama' => 'Plaza Senayan XXI',
                'alamat' => 'Plaza Senayan Lantai 5, Jl. Asia Afrika, Senayan, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'fasilitas' => ['parkir', 'food_court', 'mushola'],
                'jam_buka' => '11:00:00',
                'jam_tutup' => '22:30:00',
                'studios' => [
                    ['nama' => 'Studio 1', 'tipe' => 'Reguler', 'kapasitas' => 150],
                    ['nama' => 'Studio 2', 'tipe' => 'Reguler', 'kapasitas' => 150],
                    ['nama' => 'The Premiere', 'tipe' => 'Premiere', 'kapasitas' => 40],
                ],
            ],
            [
                'nama' => 'Cinepolis Senayan Park',
                'alamat' => 'Senayan Park Mall, Jl. Gerbang Pemuda No.3, Tanah Abang, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'fasilitas' => ['parkir', 'food_court', 'wifi'],
                'jam_buka' => '10:00:00',
                'jam_tutup' => '23:00:00',
                'studios' => [
                    ['nama' => 'Studio 1', 'tipe' => 'Reguler', 'kapasitas' => 150],
                    ['nama' => 'Macro XE', 'tipe' => 'MacroXE', 'kapasitas' => 150],
                    ['nama' => 'VIP Class', 'tipe' => 'VIP', 'kapasitas' => 40],
                ],
            ],
        ];

        foreach ($bioskops as $bioData) {
            $studiosData = $bioData['studios'];
            unset($bioData['studios']);

            $bioskop = Bioskop::create($bioData);

            foreach ($studiosData as $stuData) {
                $studio = Studio::create([
                    'bioskop_id' => $bioskop->id,
                    'nama' => $stuData['nama'],
                    'tipe' => $stuData['tipe'],
                    'kapasitas' => $stuData['kapasitas'],
                    'layout_kursi' => [
                        'rows' => 10,
                        'cols' => 15,
                    ],
                ]);

                $studios->push($studio);

                
                $rows = range('A', 'J');
                foreach ($rows as $row) {
                    for ($i = 1; $i <= 15; $i++) {
                        Kursi::create([
                            'studio_id' => $studio->id,
                            'label_baris' => $row,
                            'nomor_kursi' => $i,
                            'tipe_kursi' => 'reguler',
                            'is_aktif' => true,
                        ]);
                    }
                }
            }
        }

        $this->command?->info("Bioskop: ".count($bioskops)." bioskop, {$studios->count()} studio siap");
    }
}
