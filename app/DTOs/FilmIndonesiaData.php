<?php

namespace App\DTOs;

use Carbon\Carbon;

final class FilmIndonesiaData
{
    public function __construct(
        public readonly string $slug,                    
        public readonly string $judul,
        public readonly ?string $sinopsis = null,
        public readonly ?string $posterUrl = null,
        public readonly ?int $durasiMenit = null,
        public readonly array $genre = [],               
        public readonly ?Carbon $tanggalRilis = null,
        public readonly array $sutradara = [],           
        public readonly array $pemain = [],              
        public readonly array $produser = [],            
        public readonly ?string $produksi = null,        
        public readonly ?string $ratingUsia = null,      
        public readonly array $lokasiBioskop = [],       
        public readonly ?float $rating = null,           
        public readonly string $sourceUrl = '',
    ) {}

    
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'judul' => $this->judul,
            'sinopsis' => $this->sinopsis,
            'poster_url' => $this->posterUrl,
            'durasi_menit' => $this->durasiMenit,
            'genre' => $this->genre,
            'tanggal_rilis' => $this->tanggalRilis?->format('Y-m-d'),
            'sutradara' => $this->sutradara,
            'pemain' => $this->pemain,
            'produser' => $this->produser,
            'produksi' => $this->produksi,
            'rating_usia' => $this->ratingUsia,
            'lokasi_bioskop' => $this->lokasiBioskop,
            'rating' => $this->rating,
            'source_url' => $this->sourceUrl,
        ];
    }

    
    public function similarityWith(string $query): float
    {
        $query = mb_strtolower(trim($query));
        $judul = mb_strtolower($this->judul);

        if ($query === $judul) {
            return 1.0;
        }

        
        if (str_contains($judul, $query) || str_contains($query, $judul)) {
            return 0.85;
        }

        
        $distance = levenshtein($query, $judul);
        $maxLen = max(strlen($query), strlen($judul));
        if ($maxLen === 0) {
            return 0.0;
        }

        return max(0.0, 1.0 - ($distance / $maxLen));
    }
}
