<?php

namespace App\Services;

use App\DTOs\FilmIndonesiaData;
use Illuminate\Support\Facades\Log;

class FilmMetadataAssembler
{
    public function __construct(
        private readonly FilmIndonesiaScraper $scraper,
        private readonly AiFilmEnrichmentService $ai,
        private readonly PosterDownloader $posterDownloader,
    ) {}

    
    public function lookup(string $judul): ?FilmIndonesiaData
    {
        $raw = $this->scraper->searchAndGetDetail($judul);

        if ($raw === null) {
            return null;
        }

        return $this->ai->enrich($raw);
    }

    
    public function lookupBySlug(string $slug, bool $enrich = true): ?FilmIndonesiaData
    {
        $raw = $this->scraper->getDetail($slug);

        if ($raw === null) {
            return null;
        }

        return $enrich ? $this->ai->enrich($raw) : $raw;
    }

    
    public function toFilmFormData(FilmIndonesiaData $data, bool $downloadPoster = true): array
    {
        $posterPath = null;
        if ($downloadPoster && $data->posterUrl) {
            
            
            $slugName = $this->extractSlugFromPosterUrl($data->posterUrl)
                ?? $this->extractSlugFromUrl($data->sourceUrl)
                ?? $data->judul;
            $posterPath = $this->posterDownloader->download($data->posterUrl, $slugName);
        }

        return [
            'judul' => $data->judul,
            'poster_url' => $posterPath,           
            'sinopsis' => $data->sinopsis,
            'durasi_menit' => $data->durasiMenit ?? 0,
            'rating' => $data->rating,
            'genre' => implode(', ', $data->genre),
            'tanggal_rilis' => $data->tanggalRilis?->format('Y-m-d'),
            
            
            'sedang_tayang' => $this->scraper->isNowShowing($data->slug)
                || ! empty($data->lokasiBioskop),
            'sutradara' => implode(', ', $data->sutradara),
            'pemain' => implode(', ', $data->pemain),
            'produksi' => $data->produksi,
            'rating_usia' => $data->ratingUsia ?? '13+',
            'negara' => 'Indonesia',
            'bahasa' => 'Indonesia',
        ];
    }

    
    private function extractSlugFromUrl(string $sourceUrl): ?string
    {
        if (preg_match('#/film/([^/]+)$#', $sourceUrl, $m)) {
            return $m[1];
        }
        return null;
    }

    
    private function extractSlugFromPosterUrl(string $posterUrl): ?string
    {
        if (preg_match('#/poster/([^/?]+)#', $posterUrl, $m)) {
            return $m[1];
        }
        return null;
    }
}
