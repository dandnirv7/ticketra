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

        $this->command?->info('🎬 Film Indonesia Seeder — scraping filmindonesia.or.id');

        if ($dryRun) {
            $this->command?->warn('⚠️  DRY RUN MODE — tidak akan menyimpan ke database');
        }

        
        $this->command?->info('📡 Step 1: Mengumpulkan daftar film dari endpoint...');

        try {
            $slugs = $this->scraper->scrapeListSlugs(self::DEFAULT_ENDPOINTS);
        } catch (\Throwable $e) {
            $this->command?->error('❌ Gagal scrape list endpoint: '.$e->getMessage());
            Log::error('FilmIndonesiaSeeder: scrape list gagal', ['error' => $e->getMessage()]);
            return;
        }

        if (empty($slugs)) {
            $this->command?->error('❌ Tidak ada film ditemukan. Cek koneksi internet atau struktur HTML filmindonesia.or.id mungkin berubah.');
            return;
        }

        $this->command?->info("✅ Ditemukan {$this->yellow((string) count($slugs))} film unik dari ".count(self::DEFAULT_ENDPOINTS)." endpoint");

        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;

        $this->command?->info('📥 Step 2: Mengambil detail per film & simpan ke database...');

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

        
        $this->command?->info('📊 Ringkasan:');
        $this->command?->line("   Film di-scrape     : {$this->yellow((string) count($slugs))}");
        $this->command?->line("   {$this->green('✅ Dibuat baru')}     : {$this->green((string) $created)}");
        $this->command?->line("   {$this->yellow('🔄 Di-update')}     : {$this->yellow((string) $updated)}");
        $this->command?->line("   {$this->gray('⏭️  Di-skip')}       : {$this->gray((string) $skipped)}");
        $this->command?->line("   {$this->red('❌ Gagal')}         : {$this->red((string) $failed)}");

        if ($dryRun) {
            $this->command?->warn('ℹ️  DRY RUN — tidak ada data yang disimpan. Jalankan tanpa --dry-run untuk commit.');
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
}
