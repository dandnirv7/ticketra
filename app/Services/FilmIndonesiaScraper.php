<?php

namespace App\Services;

use App\DTOs\FilmIndonesiaData;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class FilmIndonesiaScraper
{
    private const BASE_URL = 'https://filmindonesia.or.id';

    private const CACHE_TTL = 3600; 

    
    private const NOW_SHOWING_CACHE_KEY = 'filmindo:now_showing_slugs';

    public function __construct(
        private readonly Client $http,
    ) {}

    
    public function getNowShowingSlugs(): array
    {
        return Cache::remember(self::NOW_SHOWING_CACHE_KEY, self::CACHE_TTL, function () {
            $html = $this->fetchPage('/film/beredar-di-bioskop');

            if ($html === null) {
                return [];
            }

            $crawler = new Crawler($html);
            $jsonLdNode = $crawler->filter('script[type="application/ld+json"]');

            if (! $jsonLdNode->count()) {
                return [];
            }

            $jsonLd = json_decode($jsonLdNode->first()->text(), true);

            
            $slugs = [];
            foreach (($jsonLd['itemListElement'] ?? []) as $entry) {
                $url = $entry['item']['url'] ?? null;
                if ($url && preg_match('#/film/(lf-[a-z0-9-]+)$#', $url, $m)) {
                    $slugs[] = $m[1];
                }
            }

            return array_values(array_unique($slugs));
        });
    }

    
    public function isNowShowing(string $slug): bool
    {
        return in_array($slug, $this->getNowShowingSlugs(), true);
    }

    
    public function scrapeListSlugs(array $endpoints = ['rilis-terbaru', 'beredar-di-bioskop', 'segera-rilis']): array
    {
        $slugs = [];
        $seen = [];

        foreach ($endpoints as $endpoint) {
            $films = $this->scrapeList($endpoint);

            foreach ($films as $film) {
                if (isset($seen[$film->slug])) {
                    continue;
                }
                $seen[$film->slug] = true;
                $slugs[] = $film->slug;
            }
        }

        return $slugs;
    }

    
    public function searchByJudul(string $judul, int $limit = 5): array
    {
        $judul = trim($judul);

        if (empty($judul)) {
            return [];
        }

        $candidates = [];

        
        foreach (['rilis-terbaru', 'beredar-di-bioskop', 'segera-rilis'] as $type) {
            $films = $this->scrapeList($type);
            $candidates = array_merge($candidates, $films);
        }

        
        $unique = [];
        foreach ($candidates as $film) {
            $unique[$film->slug] = $film;
        }
        $candidates = array_values($unique);

        
        $matches = array_filter($candidates, fn ($f) => $f->similarityWith($judul) >= 0.5);

        
        usort($matches, fn ($a, $b) => $b->similarityWith($judul) <=> $a->similarityWith($judul));

        return array_slice($matches, 0, $limit);
    }

    
    public function getDetail(string $slug): ?FilmIndonesiaData
    {
        
        if (! preg_match('/^lf-[a-z]\d{3}-\d{2}-\d{6}$/', $slug)) {
            Log::warning('FilmIndonesiaScraper: invalid slug format', ['slug' => $slug]);
            return null;
        }

        $html = $this->fetchPage("/film/{$slug}");

        if ($html === null) {
            return null;
        }

        return $this->parseDetailPage($html, $slug);
    }

    
    public function searchAndGetDetail(string $judul): ?FilmIndonesiaData
    {
        $candidates = $this->searchByJudul($judul, limit: 3);

        if (empty($candidates)) {
            return null;
        }

        
        return $this->getDetail($candidates[0]->slug);
    }

    
    private function scrapeList(string $type): array
    {
        $html = $this->fetchPage("/film/{$type}");

        if ($html === null) {
            return [];
        }

        $films = [];
        $crawler = new Crawler($html);

        
        $crawler->filter('div.panel__heading__title a[href*="/film/lf-"]')->each(function (Crawler $node) use (&$films, $type) {
            $href = $node->attr('href');
            $judul = trim($node->text());

            
            if (! preg_match('#/film/(lf-[a-z0-9-]+)$#', $href, $matches)) {
                return;
            }
            $slug = $matches[1];

            
            $panel = $node->closest('.panel');
            if (! $panel->count()) {
                return;
            }

            
            $genres = $panel->filter('.panel__heading__tags .tag a')->each(
                fn (Crawler $a) => trim($a->text())
            );

            
            $posterUrl = null;
            $prevCard = $panel->filterXPath('preceding-sibling::div[contains(@class, "mdc-card")]')->first();
            if ($prevCard->count()) {
                $bgset = $prevCard->filter('.lazyload')->attr('data-bgset') ?? '';
                
                if (preg_match('#(https://filmindonesia\.or\.id/f/img/movie/poster/[^,?\s]+)#', $bgset, $m)) {
                    $posterUrl = $m[1] . '?p=1319-3c';
                }
            }

            
            $content = $panel->filter('.panel__content')->first();
            $tanggalRilis = null;
            $sinopsis = null;
            $sutradara = [];
            $pemain = [];

            if ($content->count()) {
                $text = $content->text();

                if (preg_match('/Tanggal rilis:\s*(\d{2}-\d{2}-\d{4})/', $text, $m)) {
                    try {
                        $tanggalRilis = Carbon::createFromFormat('d-m-Y', $m[1]);
                    } catch (\Exception) {
                        
                    }
                }

                
                $firstP = $content->filter('p')->first();
                if ($firstP->count()) {
                    $firstPText = trim($firstP->text());
                    if (! str_starts_with($firstPText, 'Tanggal rilis:')) {
                        $sinopsis = $firstPText;
                    } else {
                        
                        $secondP = $content->filter('p')->eq(1);
                        if ($secondP->count()) {
                            $sinopsis = trim($secondP->text());
                        }
                    }
                }

                
                $sutradaraNode = $content->filterXPath('//p[starts-with(., "Sutradara:")]');
                if ($sutradaraNode->count()) {
                    $sutradara = $sutradaraNode->filter('a')->each(
                        fn (Crawler $a) => trim($a->text())
                    );
                }

                
                $pemainNode = $content->filterXPath('//p[starts-with(., "Pemeran:")]');
                if ($pemainNode->count()) {
                    $pemain = $pemainNode->filter('a')->each(
                        fn (Crawler $a) => trim($a->text())
                    );
                }
            }

            
            $lokasi = [];
            if ($type === 'beredar-di-bioskop') {
                $lokasi = $panel->filter('.panel__content .tag--tiny a')->each(
                    fn (Crawler $a) => trim($a->text())
                );
            }

            $films[] = new FilmIndonesiaData(
                slug: $slug,
                judul: $judul,
                sinopsis: $sinopsis,
                posterUrl: $posterUrl,
                durasiMenit: null,            
                genre: $genres,
                tanggalRilis: $tanggalRilis,
                sutradara: $sutradara,
                pemain: $pemain,
                produser: [],                 
                produksi: null,
                ratingUsia: null,             
                lokasiBioskop: $lokasi,
                rating: null,
                sourceUrl: self::BASE_URL . '/film/' . $slug,
            );
        });

        return $films;
    }

    
    private function parseDetailPage(string $html, string $slug): ?FilmIndonesiaData
    {
        $crawler = new Crawler($html);

        $jsonLdNode = $crawler->filter('script[type="application/ld+json"]');
        if (! $jsonLdNode->count()) {
            Log::warning('FilmIndonesiaScraper: no JSON-LD found', ['slug' => $slug]);
            return null;
        }

        $jsonLd = json_decode($jsonLdNode->first()->text(), true);

        if (! is_array($jsonLd) || ($jsonLd['@type'] ?? null) !== 'Movie') {
            Log::warning('FilmIndonesiaScraper: JSON-LD is not Movie type', ['slug' => $slug]);
            return null;
        }

        
        $durasiMenit = $this->parseDuration($jsonLd['duration'] ?? null);

        
        $tanggalRilis = null;
        if (! empty($jsonLd['datePublished'])) {
            try {
                $tanggalRilis = Carbon::createFromFormat('d-m-Y', $jsonLd['datePublished']);
            } catch (\Exception) {
                
            }
        }

        
        $posterUrl = $jsonLd['image']['url'] ?? null;
        if ($posterUrl && str_ends_with($posterUrl, '/img/poster.svg')) {
            
            $bgset = $crawler->filter('.lazyload')->first()->attr('data-bgset') ?? '';
            if (preg_match('#(https://filmindonesia\.or\.id/f/img/movie/poster/[^,?\s]+)#', $bgset, $m)) {
                $posterUrl = $m[1] . '?p=1319-3c';
            }
        }

        
        $bodyText = $crawler->filter('body')->text();
        $ratingUsia = $this->parseRatingUsia($bodyText);

        
        $pemain = array_map(fn ($a) => $a['name'], $jsonLd['actor'] ?? []);
        $sutradara = array_map(fn ($a) => $a['name'], $jsonLd['director'] ?? []);
        $produser = array_map(fn ($a) => $a['name'], $jsonLd['producer'] ?? []);

        
        $lokasiBioskop = [];

        
        $sinopsisHtml = null;
        $panelContent = $crawler->filter('.panel__content p span')->first();
        if ($panelContent->count()) {
            $sinopsisHtml = trim($panelContent->text());
        }
        $sinopsis = $sinopsisHtml ?: ($jsonLd['description'] ?? null);

        return new FilmIndonesiaData(
            slug: $slug,
            judul: $jsonLd['name'] ?? '',
            sinopsis: $sinopsis,
            posterUrl: $posterUrl,
            durasiMenit: $durasiMenit,
            genre: $jsonLd['genre'] ?? [],
            tanggalRilis: $tanggalRilis,
            sutradara: $sutradara,
            pemain: $pemain,
            produser: $produser,
            produksi: null, 
            ratingUsia: $ratingUsia,
            lokasiBioskop: $lokasiBioskop,
            rating: null,
            sourceUrl: self::BASE_URL . '/film/' . $slug,
        );
    }

    
    private function parseDuration(?string $iso): ?int
    {
        if (empty($iso)) {
            return null;
        }

        if (! preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $iso, $m)) {
            return null;
        }

        $hours = (int) ($m[1] ?? 0);
        $minutes = (int) ($m[2] ?? 0);

        return ($hours * 60) + $minutes;
    }

    
    private function parseRatingUsia(string $text): ?string
    {
        if (preg_match('/(\d+)\s*tahun ke atas/i', $text, $m)) {
            return $m[1] . '+';
        }

        if (preg_match('/\bSU\b|untuk umum|segala umur/i', $text)) {
            return 'SU';
        }

        if (preg_match('/\b(13|17|21)\s*\+/', $text, $m)) {
            return $m[1] . '+';
        }

        return null;
    }

    
    private function fetchPage(string $path): ?string
    {
        $cacheKey = 'filmindo:page:' . md5($path);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($path) {
            try {
                $response = $this->http->get(self::BASE_URL . $path, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml',
                        'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                    ],
                    'timeout' => 15,
                    'allow_redirects' => true,
                    'http_errors' => false,
                ]);

                $status = $response->getStatusCode();

                if ($status !== 200) {
                    Log::warning('FilmIndonesiaScraper: non-200 response', [
                        'path' => $path,
                        'status' => $status,
                    ]);
                    return null;
                }

                $body = (string) $response->getBody();

                
                if (strlen($body) < 1000 || ! str_contains($body, '<html')) {
                    Log::warning('FilmIndonesiaScraper: response too small or not HTML', [
                        'path' => $path,
                        'size' => strlen($body),
                    ]);
                    return null;
                }

                return $body;
            } catch (GuzzleException $e) {
                Log::error('FilmIndonesiaScraper: HTTP error', [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
                return null;
            } catch (\Throwable $e) {
                Log::error('FilmIndonesiaScraper: unexpected error', [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }
}
