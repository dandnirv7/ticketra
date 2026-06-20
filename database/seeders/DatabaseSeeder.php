<?php

namespace Database\Seeders;

use App\Models\Bioskop;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Kursi;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'user@ticketra.web.id'],
            [
                'name' => 'User',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'dandinirpana7@gmail.com'],
            [
                'name' => 'Dandi',
                'password' => bcrypt('password'),
            ]
        );

        DB::table('jadwal_tayangs')->delete();
        DB::table('kursis')->delete();
        DB::table('studios')->delete();
        DB::table('bioskops')->delete();
        DB::table('films')->delete();

        $indonesianFilms = [
            [
                'judul' => 'Siksa Kubur',
                'genre' => 'Horror, Mystery',
                'durasi_menit' => 117,
                'rating' => 8.2,
                'sinopsis' => 'Setelah kedua orang tuanya jadi korban bom bunuh diri, Sita jadi tidak percaya agama. Sita bertekad mencari orang paling berdosa dan ikut masuk ke dalam kuburnya untuk membuktikan siksa kubur tidak ada.',
                'poster_url' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-04-11',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Agak Laen',
                'genre' => 'Comedy, Drama',
                'durasi_menit' => 119,
                'rating' => 8.5,
                'sinopsis' => 'Empat sekawan penjaga rumah hantu di pasar malam berusaha mencari cara baru menakuti pengunjung agar tidak bangkrut. Kejadian tak terduga pun terjadi hingga merubah hidup mereka.',
                'poster_url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-02-01',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Badarawuhi di Desa Penari',
                'genre' => 'Horror, Thriller',
                'durasi_menit' => 122,
                'rating' => 7.6,
                'sinopsis' => 'Mengisahkan asal-usul entitas siluman ular yang meneror sekelompok mahasiswa KKN di sebuah desa terpencil yang misterius.',
                'poster_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-04-11',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Ipar Adalah Maut',
                'genre' => 'Drama, Romance',
                'durasi_menit' => 131,
                'rating' => 7.9,
                'sinopsis' => 'Kehidupan rumah tangga Nisa dan Aris yang bahagia mulai goyah sejak adik kandung Nisa, Rani, tinggal bersama mereka dan memicu benih-benih cinta terlarang.',
                'poster_url' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-06-13',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Home Sweet Loan',
                'genre' => 'Drama, Family',
                'durasi_menit' => 112,
                'rating' => 8.1,
                'sinopsis' => 'Kaluna, seorang pekerja kelas menengah, berjuang keras menabung demi membeli rumah impiannya di tengah tuntutan menjadi sandwich generation.',
                'poster_url' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-06-18',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Kuasa Gelap',
                'genre' => 'Horror, Thriller',
                'durasi_menit' => 96,
                'rating' => 7.8,
                'sinopsis' => 'Kisah seorang pastor yang menghadapi keraguan iman saat harus melakukan eksorsisme pada seorang remaja yang dirasuki iblis jahat.',
                'poster_url' => 'https://images.unsplash.com/photo-1478147427282-58a87a120781?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-07-05',
                'sedang_tayang' => false,
            ],
            [
                'judul' => 'Bila Esok Ibu Tiada',
                'genre' => 'Drama, Family',
                'durasi_menit' => 104,
                'rating' => 8.0,
                'sinopsis' => 'Hubungan empat bersaudara diuji ketika sang ibu yang selalu menjadi perekat keluarga tiba-tiba jatuh sakit keras dan menghadapi ajal.',
                'poster_url' => 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-07-26',
                'sedang_tayang' => false,
            ],
            [
                'judul' => 'Kang Mak',
                'genre' => 'Comedy, Horror',
                'durasi_menit' => 124,
                'rating' => 7.5,
                'sinopsis' => 'Makmur, seorang tentara, pulang dari medan perang bersama kawan-kawannya, tanpa menyadari bahwa istri tercintanya, Sari, sebenarnya telah meninggal dunia dan menjadi hantu.',
                'poster_url' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-08-15',
                'sedang_tayang' => false,
            ],
            [
                'judul' => 'Dua Hati Biru',
                'genre' => 'Drama, Family',
                'durasi_menit' => 106,
                'rating' => 8.0,
                'sinopsis' => 'Kelanjutan kisah cinta Bima dan Dara yang kini harus menghadapi tantangan baru dalam membina rumah tangga dan membesarkan anak mereka.',
                'poster_url' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-04-17',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Sekawan Limo',
                'genre' => 'Comedy, Horror',
                'durasi_menit' => 112,
                'rating' => 7.8,
                'sinopsis' => 'Lima sahabat tersesat saat mendaki Gunung Madyopuro dan menyadari salah satu dari mereka bukanlah manusia.',
                'poster_url' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-07-04',
                'sedang_tayang' => true,
            ],
            [
                'judul' => 'Laura',
                'genre' => 'Drama, Biography',
                'durasi_menit' => 104,
                'rating' => 7.9,
                'sinopsis' => 'Kisah perjuangan hidup Laura Anna, seorang influencer muda yang berjuang mencari keadilan setelah lumpuh akibat kecelakaan mobil bersama kekasihnya.',
                'poster_url' => 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?auto=format&fit=crop&q=80&w=400&h=600',
                'tanggal_rilis' => '2026-09-12',
                'sedang_tayang' => true,
            ],
        ];

        $films = collect();
        foreach ($indonesianFilms as $filmData) {
            $films->push(Film::create($filmData));
        }

        $indonesianBioskops = [
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

        foreach ($indonesianBioskops as $bioData) {
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

                $hours = [11, 14, 17, 20];
                foreach ($hours as $h) {
                    $film = $films->random();
                    $waktuMulai = now()->setTime($h, 0, 0)->addDays(rand(0, 3));
                    $waktuSelesai = (clone $waktuMulai)->addMinutes($film->durasi_menit);

                    JadwalTayang::create([
                        'film_id' => $film->id,
                        'studio_id' => $studio->id,
                        'waktu_mulai' => $waktuMulai,
                        'waktu_selesai' => $waktuSelesai,
                        'harga' => in_array($studio->tipe, ['Premiere', 'VIP', 'Velvet', 'Gold']) ? 75000.00 : 40000.00,
                        'status' => 'terjadwal',
                    ]);
                }

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
    }
}
