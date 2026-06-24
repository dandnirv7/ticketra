<?php

namespace App\Http\Controllers;

use App\Models\Bioskop;
use App\Models\Film;
use App\Models\JadwalTayang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FilmController extends Controller
{
    
    public function index(Request $request): View
    {
        
        $heroMovie = Film::where('sedang_tayang', true)
            ->where('judul', 'like', '%Dune%')
            ->first();

        if (!$heroMovie) {
            $heroMovie = Film::where('sedang_tayang', true)
                ->orderByDesc('rating')
                ->first();
        }

        
        $nowShowing = Film::where('sedang_tayang', true)
            ->orderByDesc('rating')
            ->get();

        
        $comingSoon = Film::where('sedang_tayang', false)
            ->orderBy('tanggal_rilis')
            ->get();

        
        $allGenres = Film::pluck('genre')
            ->filter()
            ->flatMap(fn($g) => array_map('trim', explode(',', $g)))
            ->unique()
            ->values()
            ->all();

        return view('film.index', compact('heroMovie', 'nowShowing', 'comingSoon', 'allGenres'));
    }

    
    public function show(Film $film): View
    {
        
        $jadwals = JadwalTayang::with(['studio.bioskop'])
            ->where('film_id', $film->id)
            ->where('waktu_mulai', '>=', now()->startOfDay())
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        
        $showtimesGrouped = [];
        foreach ($jadwals as $j) {
            $dateStr = $j->waktu_mulai->format('Y-m-d');
            $city = $j->studio->bioskop->kota ?? 'Jakarta Pusat';
            $bioskopId = $j->studio->bioskop->id;
            $bioskopName = $j->studio->bioskop->nama;

            
            $distance = '2.1 km';
            if (str_contains($bioskopName, 'Senayan XXI')) {
                $distance = '3.4 km';
            } elseif (str_contains($bioskopName, 'Senayan Park')) {
                $distance = '4.7 km';
            }

            $studioType = $j->studio->tipe ?? 'Reguler';

            if (!isset($showtimesGrouped[$dateStr])) {
                $showtimesGrouped[$dateStr] = [];
            }
            if (!isset($showtimesGrouped[$dateStr][$city])) {
                $showtimesGrouped[$dateStr][$city] = [];
            }
            if (!isset($showtimesGrouped[$dateStr][$city][$bioskopId])) {
                $showtimesGrouped[$dateStr][$city][$bioskopId] = [
                    'id' => $bioskopId,
                    'name' => $bioskopName,
                    'distance' => $distance,
                    'formats' => [],
                    'showtimes' => [],
                ];
            }

            
            $formatTag = strtoupper($studioType);
            if ($formatTag === 'REGULER') {
                $formatTag = '2D';
            } else {
                $formatTag = $formatTag . ' 2D';
            }

            if (!in_array($formatTag, $showtimesGrouped[$dateStr][$city][$bioskopId]['formats'])) {
                $showtimesGrouped[$dateStr][$city][$bioskopId]['formats'][] = $formatTag;
            }

            $showtimesGrouped[$dateStr][$city][$bioskopId]['showtimes'][] = [
                'id' => $j->id,
                'time' => $j->waktu_mulai->format('H:i'),
                'price' => (float)$j->harga,
            ];
        }

        $cities = Bioskop::distinct()->pluck('kota')->toArray();
        $cinemas = Bioskop::orderBy('nama')->get(['id', 'nama', 'kota'])
            ->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->nama,
                'kota' => $b->kota,
            ])->all();

        
        $recommendations = Film::where('id', '!=', $film->id)
            ->where('sedang_tayang', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        
        $promos = [
            [
                'id' => 'p1',
                'title' => 'PROMO POPCORN!',
                'desc' => 'Beli 2 tiket, dapat 1 popcorn medium gratis. Khusus hari ini!',
                'code' => 'POPGRATIS',
                'color' => 'bg-pastel-lemon',
            ],
            [
                'id' => 'p2',
                'title' => 'CASHBACK JAGO',
                'desc' => 'Nikmati cashback hingga 30% untuk pembayaran via QRIS.',
                'code' => 'JAGOTIKET',
                'color' => 'bg-pastel-peach',
            ],
        ];

        return view('film.show', compact(
            'film',
            'showtimesGrouped',
            'cities',
            'cinemas',
            'recommendations',
            'promos'
        ));
    }
}
