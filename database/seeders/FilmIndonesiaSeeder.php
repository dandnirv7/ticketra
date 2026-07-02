<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Services\FilmIndonesiaScraper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class FilmIndonesiaSeeder extends Seeder
{

    private const DEFAULT_ENDPOINTS = ['rilis-terbaru', 'beredar-di-bioskop', 'segera-rilis'];

    public function __construct(
        private readonly FilmIndonesiaScraper $scraper,
    ) {}

    public function run(): void
    {


        $dryRun = filter_var(env('FILM_SEED_DRY_RUN', false), FILTER_VALIDATE_BOOLEAN);

        $this->command?->info('Film Indonesia Seeder — scraping filmindonesia.or.id');

        if ($dryRun) {
            $this->command?->warn('DRY RUN MODE — tidak akan menyimpan ke database');
        }


        $this->command?->info('Step 1: Mengumpulkan daftar film dari endpoint...');

        try {
            $slugs = $this->scraper->scrapeListSlugs(self::DEFAULT_ENDPOINTS);
        } catch (\Throwable $e) {
            $this->command?->error('Gagal scrape list endpoint: ' . $e->getMessage());
            Log::error('FilmIndonesiaSeeder: scrape list gagal', ['error' => $e->getMessage()]);
            return;
        }

        if (empty($slugs)) {
            $this->command?->warn('Scraper failed or returned no data. Seeding with local fallback films instead.');
            $this->seedFallbackFilms();
            return;
        }

        $this->command?->info("Ditemukan {$this->yellow((string) count($slugs))} film unik dari " . count(self::DEFAULT_ENDPOINTS) . " endpoint");


        $created = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;

        $this->command?->info('Step 2: Mengambil detail per film & simpan ke database...');

        $progressBar = $this->command?->getOutput()->createProgressBar(count($slugs));
        $progressBar?->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $progressBar?->start();

        foreach ($slugs as $slug) {
            $progressBar?->setMessage("Processing: {$slug}");
            $progressBar?->advance();

            try {
                $data = $this->scraper->getDetail($slug);

                if ($data === null) {
                    $skipped++;
                    Log::warning('FilmIndonesiaSeeder: detail null', ['slug' => $slug]);
                    continue;
                }


                $formData = [
                    'slug' => $data->slug,
                    'judul' => $data->judul,
                    'poster_url' => $data->posterUrl,
                    'sinopsis' => $data->sinopsis,
                    'durasi_menit' => $data->durasiMenit ?? 0,
                    'rating' => $data->rating,
                    'genre' => implode(', ', $data->genre),
                    'tanggal_rilis' => $data->tanggalRilis?->format('Y-m-d'),
                    'sedang_tayang' => $this->resolveSedangTayang($data),
                    'sutradara' => implode(', ', $data->sutradara),
                    'penulis' => ! empty($data->produser) ? implode(', ', $data->produser) : null,
                    'pemain' => implode(', ', $data->pemain),
                    'bahasa' => 'Indonesia',
                    'negara' => 'Indonesia',
                    'produksi' => $data->produksi,
                    'rating_usia' => $data->ratingUsia ?? '13+',
                ];




                if (! $dryRun) {
                    $existing = Film::where('slug', $slug)->first();

                    if ($existing) {

                        foreach (['judul', 'sinopsis', 'poster_url', 'genre', 'sutradara', 'pemain'] as $field) {
                            if (! empty($existing->{$field})) {
                                $formData[$field] = $existing->{$field};
                            }
                        }



                        $existing->update([
                            'sedang_tayang' => $formData['sedang_tayang'],
                            'tanggal_rilis' => $formData['tanggal_rilis'],
                            'rating_usia' => $formData['rating_usia'],
                            'rating' => $formData['rating'],
                            'durasi_menit' => $formData['durasi_menit'],
                        ]);
                        $updated++;
                    } else {
                        Film::create($formData);
                        $created++;
                    }
                } else {

                    $created++;
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::error('FilmIndonesiaSeeder: gagal proses film', [
                    'slug' => $slug,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        $progressBar?->finish();
        $this->command?->newLine(2);


        $this->command?->info('Ringkasan:');
        $this->command?->line("   Film di-scrape     : {$this->yellow((string) count($slugs))}");
        $this->command?->line("   {$this->green('Dibuat baru')}     : {$this->green((string)$created)}");
        $this->command?->line("   {$this->yellow('Di-update')}     : {$this->yellow((string)$updated)}");
        $this->command?->line("   {$this->gray('Di-skip')}       : {$this->gray((string)$skipped)}");
        $this->command?->line("   {$this->red('Gagal')}         : {$this->red((string)$failed)}");

        if ($dryRun) {
            $this->command?->warn('DRY RUN — tidak ada data yang disimpan. Jalankan tanpa --dry-run untuk commit.');
        }
    }


    private function resolveSedangTayang(\App\DTOs\FilmIndonesiaData $data): bool
    {
        if ($this->scraper->isNowShowing($data->slug)) {
            return true;
        }

        if (! empty($data->lokasiBioskop)) {
            return true;
        }

        if ($data->tanggalRilis === null) {
            return false;
        }

        $today = now()->startOfDay();
        $rilis = $data->tanggalRilis->startOfDay();
        $daysSinceRelease = (int) $today->diffInDays($rilis, false);


        return $daysSinceRelease >= 0 && $daysSinceRelease <= 180;
    }


    private function green(string $text): string
    {
        return "\033[32m{$text}\033[0m";
    }

    private function yellow(string $text): string
    {
        return "\033[33m{$text}\033[0m";
    }

    private function red(string $text): string
    {
        return "\033[31m{$text}\033[0m";
    }

    private function gray(string $text): string
    {
        return "\033[90m{$text}\033[0m";
    }

    private function seedFallbackFilms(): void
    {
        $fallbackFilms = [
            [
                'slug' => 'siksa-kubur',
                'judul' => 'Siksa Kubur',
                'poster_url' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Setelah kedua orang tuanya jadi korban bom bunuh diri, Sita jadi tidak percaya agama. Sita bertekad mencari orang paling berdosa dan ikut masuk ke dalam kuburnya untuk membuktikan siksa kubur tidak ada.',
                'durasi_menit' => 117,
                'rating' => 8.2,
                'genre' => 'Horror, Mystery',
                'tanggal_rilis' => now()->subDays(10)->format('Y-m-d'),
                'sedang_tayang' => true,
                'sutradara' => 'Joko Anwar',
                'penulis' => 'Joko Anwar',
                'pemain' => 'Faradina Mufti, Reza Rahadian, Widuri Puteri',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'Come and See Pictures',
                'rating_usia' => '17+',
            ],
            [
                'slug' => 'agak-laen',
                'judul' => 'Agak Laen',
                'poster_url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Empat sekawan penjaga rumah hantu di pasar malam berusaha mencari cara baru menakuti pengunjung agar tidak bangkrut. Kejadian tak terduga pun terjadi hingga merubah hidup mereka.',
                'durasi_menit' => 119,
                'rating' => 8.5,
                'genre' => 'Comedy, Drama',
                'tanggal_rilis' => now()->subDays(30)->format('Y-m-d'),
                'sedang_tayang' => true,
                'sutradara' => 'Muhadkly Acho',
                'penulis' => 'Muhadkly Acho',
                'pemain' => 'Bene Dion, Boris Bokir, Indra Jegel, Oki Rengga',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'Imajinari',
                'rating_usia' => '13+',
            ],
            [
                'slug' => 'badarawuhi-di-desa-penari',
                'judul' => 'Badarawuhi di Desa Penari',
                'poster_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Mengisahkan asal-usul entitas siluman ular yang meneror sekelompok mahasiswa KKN di sebuah desa terpencil yang misterius.',
                'durasi_menit' => 122,
                'rating' => 7.6,
                'genre' => 'Horror, Thriller',
                'tanggal_rilis' => now()->subDays(15)->format('Y-m-d'),
                'sedang_tayang' => true,
                'sutradara' => 'Kimo Stamboel',
                'penulis' => 'Lele Laila',
                'pemain' => 'Aulia Sarah, Maudy Effrosina, Jourdy Pranata',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'MD Pictures',
                'rating_usia' => '13+',
            ],
            [
                'slug' => 'ipar-adalah-maut',
                'judul' => 'Ipar Adalah Maut',
                'poster_url' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Kehidupan rumah tangga Nisa dan Aris yang bahagia mulai goyah sejak adik kandung Nisa, Rani, tinggal bersama mereka dan memicu benih-benih cinta terlarang.',
                'durasi_menit' => 131,
                'rating' => 7.9,
                'genre' => 'Drama, Romance',
                'tanggal_rilis' => now()->subDays(40)->format('Y-m-d'),
                'sedang_tayang' => true,
                'sutradara' => 'Hanung Bramantyo',
                'penulis' => 'Oka Aurora',
                'pemain' => 'Michelle Ziudith, Deva Mahenra, Davina Karamoy',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'MD Pictures',
                'rating_usia' => '17+',
            ],
            [
                'slug' => 'home-sweet-loan',
                'judul' => 'Home Sweet Loan',
                'poster_url' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Kaluna, seorang pekerja kelas menengah, berjuang keras menabung demi membeli rumah impiannya di tengah tuntutan menjadi sandwich generation.',
                'durasi_menit' => 112,
                'rating' => 8.1,
                'genre' => 'Drama, Family',
                'tanggal_rilis' => now()->subDays(5)->format('Y-m-d'),
                'sedang_tayang' => true,
                'sutradara' => 'Sabrina Rochelle Kalangie',
                'penulis' => 'Sabrina Rochelle Kalangie',
                'pemain' => 'Yunita Siregar, Derby Romero, Risty Tagor',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'Visinema Pictures',
                'rating_usia' => 'SU',
            ],
            [
                'slug' => 'kuasa-gelap',
                'judul' => 'Kuasa Gelap',
                'poster_url' => 'https://images.unsplash.com/photo-1478147427282-58a87a120781?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Pastor menghadapi keraguan iman saat melakukan eksorsisme.',
                'durasi_menit' => 96,
                'rating' => 7.8,
                'genre' => 'Horror, Thriller',
                'tanggal_rilis' => now()->addDays(5)->format('Y-m-d'),
                'sedang_tayang' => false,
                'sutradara' => 'Bobby Prasetyo',
                'penulis' => 'Andi Suryanto',
                'pemain' => 'Jerome Kurnia, Lukman Sardi, Astrid Tiar',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'Paragon Pictures',
                'rating_usia' => '17+',
            ],
            [
                'slug' => 'bila-esok-ibu-tiada',
                'judul' => 'Bila Esok Ibu Tiada',
                'poster_url' => 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Hubungan empat bersaudara diuji ketika sang ibu sakit keras.',
                'durasi_menit' => 104,
                'rating' => 8.0,
                'genre' => 'Drama, Family',
                'tanggal_rilis' => now()->addDays(20)->format('Y-m-d'),
                'sedang_tayang' => false,
                'sutradara' => 'Rudy Soedjarwo',
                'penulis' => 'Oka Aurora',
                'pemain' => 'Christine Hakim, Adinia Wirasti, Fedi Nuril',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'Leo Pictures',
                'rating_usia' => 'SU',
            ],
            [
                'slug' => 'kang-mak',
                'judul' => 'Kang Mak',
                'poster_url' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&q=80&w=400&h=600',
                'sinopsis' => 'Makmur pulang dari medan perang tanpa menyadari istrinya telah meninggal.',
                'durasi_menit' => 124,
                'rating' => 7.5,
                'genre' => 'Comedy, Horror',
                'tanggal_rilis' => now()->addDays(30)->format('Y-m-d'),
                'sedang_tayang' => false,
                'sutradara' => 'Herwin Novianto',
                'penulis' => 'Alim Sudio',
                'pemain' => 'Vino G. Bastian, Marsha Timothy, Indro Warkop',
                'bahasa' => 'Indonesia',
                'negara' => 'Indonesia',
                'produksi' => 'Falcon Pictures',
                'rating_usia' => '13+',
            ],
        ];

        foreach ($fallbackFilms as $film) {
            Film::updateOrCreate(['slug' => $film['slug']], $film);
        }
        $this->command?->info("Seeding selesai. Berhasil membuat ".count($fallbackFilms)." film local fallback.");
    }
}
