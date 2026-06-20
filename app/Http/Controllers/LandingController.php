<?php

namespace App\Http\Controllers;

use App\Models\Bioskop;
use App\Models\JadwalTayang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(Request $request): View
    {
        $cinemas = Bioskop::orderBy('nama')
            ->get(['id', 'nama', 'kota'])
            ->map(fn($b) => [
                'id' => (string) $b->id,
                'name' => $b->nama,
                'location' => $b->kota,
            ])->values()->all();

        $movies = JadwalTayang::with('film')
            ->where('waktu_mulai', '>=', now())
            ->orderBy('waktu_mulai')
            ->get()
            ->pluck('film')
            ->filter()
            ->unique('id')
            ->take(8)
            ->map(fn($m) => [
                'id' => (string) $m->id,
                'title' => $m->judul,
                'genre' => $m->genre ?? 'Film',
                'duration' => $m->durasi_menit ? $m->durasi_menit . ' Menit' : '120 Menit',
                'poster' => $m->poster_url ?: 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=400&h=600',
                'rating' => $m->rating ? number_format((float) $m->rating, 1) : '7.5',
                'ageRating' => '13+',
            ])->values()->all();

        if (empty($movies)) {
            $movies = [
                ['id' => 'm1', 'title' => 'Dune: Part Two', 'genre' => 'Sci-Fi, Action', 'duration' => '166 Menit', 'poster' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=400&h=600', 'rating' => '8.8', 'ageRating' => '13+'],
                ['id' => 'm2', 'title' => 'Kung Fu Panda 4', 'genre' => 'Animation, Comedy', 'duration' => '94 Menit', 'poster' => 'https://images.unsplash.com/photo-1585647347384-2593bc35786b?auto=format&fit=crop&q=80&w=400&h=600', 'rating' => '7.5', 'ageRating' => 'SU'],
                ['id' => 'm3', 'title' => 'Oppenheimer', 'genre' => 'Biography, Drama', 'duration' => '180 Menit', 'poster' => 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?auto=format&fit=crop&q=80&w=400&h=600', 'rating' => '8.4', 'ageRating' => '17+'],
                ['id' => 'm4', 'title' => 'Godzilla x Kong', 'genre' => 'Action, Sci-Fi', 'duration' => '115 Menit', 'poster' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=400&h=600', 'rating' => '7.0', 'ageRating' => '13+'],
                ['id' => 'm5', 'title' => 'The Fall Guy', 'genre' => 'Action, Comedy', 'duration' => '126 Menit', 'poster' => 'https://images.unsplash.com/photo-1533613220915-609f661a6fe1?auto=format&fit=crop&q=80&w=400&h=600', 'rating' => '7.3', 'ageRating' => '13+'],
            ];
        }

        $promos = [
            ['title' => 'Cashback 50%', 'desc' => 'Maks. 25rb dengan QRIS Bank Jago.', 'tag' => 'Diskon Spesial', 'code' => 'JAGO50', 'color' => 'bg-pastel-mint'],
            ['title' => 'Beli 1 Gratis 1', 'desc' => 'Khusus pengguna baru Ticketra.', 'tag' => 'Pengguna Baru', 'code' => 'NEWBIE', 'color' => 'bg-pastel-lemon'],
            ['title' => 'Potongan 20Rb', 'desc' => 'Bayar pakai kartu kredit Mandiri.', 'tag' => 'Kartu Kredit', 'code' => 'MNDRI20', 'color' => 'bg-pastel-lavender'],
        ];

        $testimonials = [
            ['name' => 'Andi S.', 'role' => 'Movie Freak', 'quote' => 'Gila sih, UX-nya mulus banget! Gak perlu lagi ngantre panjang di loket cuma buat rebutan kursi avengers.'],
            ['name' => 'Rina Kartika', 'role' => 'Mahasiswi', 'quote' => 'Sering banget dapet promo dari Ticketra. Buat kantong mahasiswa ini ngebantu banget biar tetep bisa update film.'],
            ['name' => 'Bima A.', 'role' => 'Pekerja Kantoran', 'quote' => 'Design webnya fresh dan beda dari yang lain. Pesen tiket sambil jalan ke mall juga bisa, sampe bioskop langsung masuk.'],
        ];

        $steps = [
            ['title' => 'Pilih Bioskop & Film', 'desc' => 'Cari film yang ingin kamu tonton dan tentukan lokasi bioskop.'],
            ['title' => 'Tentukan Waktu', 'desc' => 'Pilih hari dan jam tayang yang sesuai dengan jadwalmu.'],
            ['title' => 'Pilih Kursi Posisi Wuenak', 'desc' => 'Pilih tempat duduk favoritmu lewat seat map interaktif.'],
            ['title' => 'Bayar & Nikmati!', 'desc' => 'Selesaikan pembayaran, tiket langsung masuk ke email/aplikasi.'],
        ];

        $faqs = [
            ['q' => 'Apakah saya harus mencetak e-ticket?', 'a' => "Tidak perlu. Anda cukup menunjukkan barcode e-ticket yang ada di aplikasi/email Anda kepada petugas pintu studio untuk di-scan."],
            ['q' => 'Bisakah saya membatalkan atau mengubah jadwal tiket?', 'a' => "Sesuai kebijakan bioskop partner kami, tiket yang sudah dibayar tidak dapat dibatalkan, diuangkan kembali, maupun diubah jadwalnya."],
            ['q' => 'Metode pembayaran apa saja yang didukung?', 'a' => "Kami mendukung transfer Bank (Virtual Account), Kartu Kredit/Debit, dan E-Wallet (GoPay, OVO, Dana, ShopeePay, QRIS)."],
            ['q' => 'Apakah ada biaya admin tambahan?', 'a' => "Ya, terdapat biaya admin layanan sebesar Rp 3.000 per tiket yang akan otomatis ditambahkan pada total pembayaran Anda di akhir."],
        ];

        return view('welcome', compact('cinemas', 'movies', 'promos', 'testimonials', 'steps', 'faqs'));
    }
}
