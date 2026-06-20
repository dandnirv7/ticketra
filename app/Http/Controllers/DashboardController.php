<?php

namespace App\Http\Controllers;

use App\Models\Bioskop;
use App\Models\Booking;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Kursi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $heroFilms = Film::query()
            ->where('sedang_tayang', true)
            ->orderByDesc('rating')
            ->orderByDesc('tanggal_rilis')
            ->limit(5)
            ->get()
            ->map(fn($f) => $this->mapFilm($f))
            ->values()
            ->all();

        if (empty($heroFilms)) {
            $heroFilms = $this->fallbackHeroFilms();
        }

        $nowShowing = Film::query()
            ->where('sedang_tayang', true)
            ->orderByDesc('rating')
            ->limit(8)
            ->get()
            ->map(fn($f) => $this->mapFilm($f))
            ->values()
            ->all();

        if (empty($nowShowing)) {
            $nowShowing = array_slice($this->fallbackNowShowing(), 0, 8);
        }

        $comingSoon = Film::query()
            ->where('tanggal_rilis', '>', now())
            ->orderBy('tanggal_rilis')
            ->limit(3)
            ->get()
            ->map(fn($f) => $this->mapFilm($f))
            ->values()
            ->all();

        if (empty($comingSoon)) {
            $comingSoon = $this->fallbackComingSoon();
        }

        $bioskopFavorit = Bioskop::query()
            ->withCount("studios")
            ->orderBy('nama')
            ->limit(1)
            ->first();

        $favoriteSeats = $this->buildFavoriteSeats($user->id);

        $upcomingBookingsCount = Booking::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending_payment', 'locked'])
            ->whereHas('jadwalTayang', fn($q) => $q->where('waktu_mulai', '>', now()))
            ->count();

        $snacks = $this->fallbackSnacks();

        $promos = $this->fallbackPromos();

        return view('dashboard', compact(
            'user',
            'heroFilms',
            'nowShowing',
            'comingSoon',
            'bioskopFavorit',
            'favoriteSeats',
            'upcomingBookingsCount',
            'snacks',
            'promos'
        ));
    }

    private function mapFilm(Film $f): array
    {
        return [
            'id' => $f->id,
            'title' => $f->judul,
            'synopsis' => $f->sinopsis ?? '',
            'genre' => $f->genre ?? 'Film',
            'duration' => $f->durasi_menit ? $f->durasi_menit . ' Menit' : '120 Menit',
            'rating' => $f->rating ? number_format((float) $f->rating, 1) : '7.5',
            'poster' => $f->poster_url ?: 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=400&h=600',
            'ageRating' => '13+',
            'tanggal_rilis' => $f->tanggal_rilis?->format('d F Y'),
        ];
    }

    private function buildFavoriteSeats(string $userId): array
    {
        $bookings = Booking::where('user_id', $userId)
            ->with('statusKursis.kursi')
            ->get();

        $labels = [];
        foreach ($bookings as $b) {
            foreach ($b->statusKursis as $sk) {
                if ($sk->kursi) {
                    $key = $sk->kursi->label_baris . $sk->kursi->nomor_kursi;
                    $labels[$key] = ($labels[$key] ?? 0) + 1;
                }
            }
        }
        arsort($labels);

        $top = array_slice(array_keys($labels), 0, 7, true);
        if (empty($top)) {
            $top = ['A7', 'A8', 'A9', 'A10', 'A11', 'A12', 'A13'];
        }

        return $top;
    }

    private function fallbackHeroFilms(): array
    {
        return [
            ['id' => 'm1', 'title' => 'Siksa Kubur', 'synopsis' => 'Setelah kedua orang tuanya jadi korban bom bunuh diri, Sita jadi tidak percaya agama. Sita bertekad mencari orang paling berdosa dan ikut masuk ke dalam kuburnya untuk membuktikan siksa kubur tidak ada.', 'genre' => 'Horror, Mystery', 'duration' => '117 Menit', 'rating' => '8.2', 'poster' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '17+', 'tanggal_rilis' => null],
            ['id' => 'm2', 'title' => 'Agak Laen', 'synopsis' => 'Empat sekawan penjaga rumah hantu di pasar malam berusaha mencari cara baru menakuti pengunjung agar tidak bangkrut. Kejadian tak terduga pun terjadi hingga merubah hidup mereka.', 'genre' => 'Comedy, Drama', 'duration' => '119 Menit', 'rating' => '8.5', 'poster' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '13+', 'tanggal_rilis' => null],
            ['id' => 'm3', 'title' => 'Badarawuhi di Desa Penari', 'synopsis' => 'Mengisahkan asal-usul entitas siluman ular yang meneror sekelompok mahasiswa KKN di sebuah desa terpencil yang misterius.', 'genre' => 'Horror, Thriller', 'duration' => '122 Menit', 'rating' => '7.6', 'poster' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '13+', 'tanggal_rilis' => null],
            ['id' => 'm4', 'title' => 'Ipar Adalah Maut', 'synopsis' => 'Kehidupan rumah tangga Nisa dan Aris yang bahagia mulai goyah sejak adik kandung Nisa, Rani, tinggal bersama mereka dan memicu benih-benih cinta terlarang.', 'genre' => 'Drama, Romance', 'duration' => '131 Menit', 'rating' => '7.9', 'poster' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '17+', 'tanggal_rilis' => null],
            ['id' => 'm5', 'title' => 'Home Sweet Loan', 'synopsis' => 'Kaluna, seorang pekerja kelas menengah, berjuang keras menabung demi membeli rumah impiannya di tengah tuntutan menjadi sandwich generation.', 'genre' => 'Drama, Family', 'duration' => '112 Menit', 'rating' => '8.1', 'poster' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => 'SU', 'tanggal_rilis' => null],
        ];
    }

    private function fallbackNowShowing(): array
    {
        return [
            ['id' => 'mx1', 'title' => 'Siksa Kubur', 'synopsis' => 'Setelah kedua orang tuanya jadi korban bom bunuh diri, Sita jadi tidak percaya agama. Sita bertekad mencari orang paling berdosa dan ikut masuk ke dalam kuburnya untuk membuktikan siksa kubur tidak ada.', 'genre' => 'Horror, Mystery', 'duration' => '117m', 'rating' => '8.2', 'poster' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '17+', 'tanggal_rilis' => null],
            ['id' => 'mx2', 'title' => 'Agak Laen', 'synopsis' => 'Empat sekawan penjaga rumah hantu di pasar malam berusaha mencari cara baru menakuti pengunjung agar tidak bangkrut.', 'genre' => 'Comedy, Drama', 'duration' => '119m', 'rating' => '8.5', 'poster' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '13+', 'tanggal_rilis' => null],
            ['id' => 'mx3', 'title' => 'Badarawuhi di Desa Penari', 'synopsis' => 'Mengisahkan asal-usul entitas siluman ular.', 'genre' => 'Horror, Thriller', 'duration' => '122m', 'rating' => '7.6', 'poster' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '13+', 'tanggal_rilis' => null],
            ['id' => 'mx4', 'title' => 'Ipar Adalah Maut', 'synopsis' => 'Kehidupan rumah tangga Nisa dan Aris.', 'genre' => 'Drama, Romance', 'duration' => '131m', 'rating' => '7.9', 'poster' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '17+', 'tanggal_rilis' => null],
            ['id' => 'mx5', 'title' => 'Home Sweet Loan', 'synopsis' => 'Kaluna berjuang keras menabung demi membeli rumah impiannya.', 'genre' => 'Drama, Family', 'duration' => '112m', 'rating' => '8.1', 'poster' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => 'SU', 'tanggal_rilis' => null],
            ['id' => 'mx6', 'title' => 'Bila Esok Ibu Tiada', 'synopsis' => 'Hubungan empat bersaudara diuji ketika sang ibu sakit keras.', 'genre' => 'Drama, Family', 'duration' => '104m', 'rating' => '8.0', 'poster' => 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => 'SU', 'tanggal_rilis' => null],
            ['id' => 'mx7', 'title' => 'Kang Mak', 'synopsis' => 'Makmur pulang dari medan perang tanpa menyadari istrinya telah meninggal.', 'genre' => 'Comedy, Horror', 'duration' => '124m', 'rating' => '7.5', 'poster' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '13+', 'tanggal_rilis' => null],
            ['id' => 'mx8', 'title' => 'Kuasa Gelap', 'synopsis' => 'Pastor menghadapi keraguan iman saat melakukan eksorsisme.', 'genre' => 'Horror, Thriller', 'duration' => '96m', 'rating' => '7.8', 'poster' => 'https://images.unsplash.com/photo-1478147427282-58a87a120781?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '17+', 'tanggal_rilis' => null],
        ];
    }

    private function fallbackComingSoon(): array
    {
        return [
            ['id' => 'cs1', 'title' => 'Kuasa Gelap', 'synopsis' => 'Kisah seorang pastor yang menghadapi keraguan iman saat harus melakukan eksorsisme.', 'genre' => 'Horror, Thriller', 'duration' => null, 'rating' => null, 'poster' => 'https://images.unsplash.com/photo-1478147427282-58a87a120781?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '17+', 'tanggal_rilis' => '5 Juli 2026'],
            ['id' => 'cs2', 'title' => 'Bila Esok Ibu Tiada', 'synopsis' => 'Hubungan empat bersaudara diuji ketika sang ibu sakit keras.', 'genre' => 'Drama, Family', 'duration' => null, 'rating' => null, 'poster' => 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => 'SU', 'tanggal_rilis' => '26 Juli 2026'],
            ['id' => 'cs3', 'title' => 'Kang Mak', 'synopsis' => 'Makmur, seorang tentara, pulang dari medan perang tanpa menyadari istrinya telah meninggal.', 'genre' => 'Comedy, Horror', 'duration' => null, 'rating' => null, 'poster' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&q=80&w=400&h=600', 'ageRating' => '13+', 'tanggal_rilis' => '15 Agustus 2026'],
        ];
    }

    private function fallbackSnacks(): array
    {
        return [
            ['id' => 's1', 'name' => 'Popcorn Large (Salt)', 'price' => 45000, 'emoji' => '🍿'],
            ['id' => 's2', 'name' => 'Coca Cola 22oz', 'price' => 25000, 'emoji' => '🥤'],
            ['id' => 's3', 'name' => 'Hotdog Deluxe', 'price' => 40000, 'emoji' => '🌭'],
            ['id' => 's4', 'name' => 'Nachos Cheese', 'price' => 35000, 'emoji' => '🌮'],
        ];
    }

    private function fallbackPromos(): array
    {
        return [
            ['id' => 'p1', 'title' => 'Promo Popcorn!', 'desc' => 'Beli 2 tiket, dapat 1 popcorn medium gratis. Khusus hari ini!', 'emoji' => '🍿', 'color' => 'bg-pastel-lemon'],
            ['id' => 'p2', 'title' => 'Cashback Jago', 'desc' => 'Nikmati cashback hingga 30% untuk pembayaran via QRIS.', 'emoji' => '💳', 'color' => 'bg-pastel-peach'],
        ];
    }
}
