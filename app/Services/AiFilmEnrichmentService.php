<?php

namespace App\Services;

use App\DTOs\FilmIndonesiaData;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AiFilmEnrichmentService
{
    private const MODEL = 'llama-3.3-70b-versatile'; 

    private const TIMEOUT = 20;

    
    public function enrich(FilmIndonesiaData $data): FilmIndonesiaData
    {
        
        $locallyNormalized = $this->applyLocalNormalization($data);

        try {
            $prompt = $this->buildPrompt($locallyNormalized);
            $response = $this->callOpenAI($prompt);
            $enrichments = $this->parseResponse($response);

            return $this->applyAiEnrichments($locallyNormalized, $enrichments);
        } catch (\Throwable $e) {
            
            Log::warning('AiFilmEnrichmentService: enrichment failed, returning locally-normalized data', [
                'slug' => $data->slug,
                'error' => $e->getMessage(),
            ]);
            return $locallyNormalized;
        }
    }

    
    private function applyLocalNormalization(FilmIndonesiaData $data): FilmIndonesiaData
    {
        return new FilmIndonesiaData(
            slug: $data->slug,
            judul: $this->normalizeJudul($data->judul),
            sinopsis: $data->sinopsis ? trim($data->sinopsis) : null,
            posterUrl: $data->posterUrl,
            durasiMenit: $data->durasiMenit,
            genre: $this->normalizeGenre($data->genre),
            tanggalRilis: $data->tanggalRilis,
            sutradara: $this->normalizePersonList($data->sutradara),
            pemain: $this->normalizePersonList($data->pemain),
            produser: $this->normalizePersonList($data->produser),
            produksi: $data->produksi ? trim($data->produksi) : null,
            ratingUsia: $data->ratingUsia,
            lokasiBioskop: $data->lokasiBioskop,
            rating: $data->rating,
            sourceUrl: $data->sourceUrl,
        );
    }

    private function applyAiEnrichments(FilmIndonesiaData $data, array $enrichments): FilmIndonesiaData
    {
        $originalLength = strlen($data->sinopsis ?? '');
        $aiSinopsisdSingkat = $enrichments['sinopsis_singkat'] ?? null;
        $useAiSynopsis = $originalLength === 0 && ! empty($aiSinopsisdSingkat);

        return new FilmIndonesiaData(
            slug: $data->slug,
            judul: $data->judul,
            sinopsis: $useAiSynopsis ? $aiSinopsisdSingkat : $data->sinopsis,
            posterUrl: $data->posterUrl,
            durasiMenit: $data->durasiMenit,
            genre: ! empty($enrichments['genre'])
                ? $this->normalizeGenre($enrichments['genre'])
                : $data->genre,
            tanggalRilis: $data->tanggalRilis,
            sutradara: ! empty($enrichments['sutradara'])
                ? $this->normalizePersonList($enrichments['sutradara'])
                : $data->sutradara,
            pemain: ! empty($enrichments['pemain'])
                ? $this->normalizePersonList($enrichments['pemain'])
                : $data->pemain,
            produser: $data->produser,
            produksi: $enrichments['produksi'] ?? $data->produksi,
            ratingUsia: $enrichments['rating_usia'] ?? $data->ratingUsia,
            lokasiBioskop: $data->lokasiBioskop,
            rating: $data->rating,
            sourceUrl: $data->sourceUrl,
        );
    }

    
    private function buildPrompt(FilmIndonesiaData $data): string
    {
        $payload = json_encode($data->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Kamu adalah data normalizer untuk aplikasi ticketing bioskop Indonesia.

TUGAS: Normalisasi data film di bawah ini. ATURAN KETAT:
1. JANGAN mengarang data baru. Kalau field kosong, return null atau array kosong.
2. JANGAN mengubah judul film (kecuali trim whitespace).
3. Trim dan titlecase untuk nama orang (sutradara, pemain, produser).
4. Untuk genre: lowercase semua dan gunakan title case konsisten (contoh: "Komedi", "Horor").
5. Sinopsis_singkat: kalau sinopsis asli > 400 karakter, buat ringkasan 1-2 kalimat (maks 200 char) dalam Bahasa Indonesia. Kalau sudah pendek, return null (jangan generate).
6. Produksi: extract nama rumah produksi kalau ada di sinopsis/produser list. Kalau tidak ada, return null.
7. Rating_usia: normalisasi ke format "SU", "13+", "17+", atau "21+".

DATA FILM:
{$payload}

Return JSON object dengan field: sinopsis_singkat, sutradara (array), pemain (array), genre (array), produksi (string|null), rating_usia (string|null).

Contoh output:
{"sinopsis_singkat": "Mahasiswa galau magang di dukun desa untuk lulus skripsi.", "sutradara": ["Chiska Doppert"], "pemain": ["Jefan Nathanio", "Fajar Nugra", "Hana Saraswati", "Adi Sudirja"], "genre": ["Komedi", "Horor"], "produksi": null, "rating_usia": "13+"}

Return HANYA JSON, tanpa markdown code block atau penjelasan tambahan.
PROMPT;
    }

    private function callOpenAI(string $prompt): string
    {
        $response = OpenAI::chat()->create([
            'model' => self::MODEL,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.1, 
            'max_tokens' => 800,
            'response_format' => ['type' => 'json_object'],
        ], ['timeout' => self::TIMEOUT]);

        return $response->choices[0]->message->content ?? '{}';
    }

    private function parseResponse(string $response): array
    {
        $parsed = json_decode($response, true);

        if (! is_array($parsed)) {
            Log::warning('AiFilmEnrichmentService: invalid JSON response', ['response' => $response]);
            return [];
        }

        return $parsed;
    }

    private function normalizeJudul(string $original): string
    {
        $trimmed = trim($original);
        return mb_convert_case($trimmed, MB_CASE_TITLE, 'UTF-8');
    }

    private function normalizeGenre(array $original): array
    {
        $normalized = array_map(
            fn ($g) => mb_convert_case(trim($g), MB_CASE_TITLE, 'UTF-8'),
            $original
        );

        return array_values(array_unique(array_filter($normalized)));
    }

    
    private function normalizePersonList(array $original): array
    {
        $normalized = array_map(
            fn ($name) => $this->normalizePersonName($name),
            $original
        );

        return array_values(array_filter($normalized));
    }

    private function normalizePersonName(string $name): string
    {
        $name = trim($name);
        if (empty($name)) {
            return '';
        }

        $parts = preg_split('/\s+/', $name);
        $normalized = array_map(function ($part) {
            if (mb_strtoupper($part) === $part && mb_strlen($part) > 1) {
                return $part;
            }
            return mb_convert_case($part, MB_CASE_TITLE, 'UTF-8');
        }, $parts);

        return implode(' ', $normalized);
    }
}
