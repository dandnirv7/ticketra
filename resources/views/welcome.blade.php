<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticketra. - Bebas Antre, Nonton Asyik</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-base"
      x-data="{
          mobileMenuOpen: false,
          activeDayTab: 'today',
          selectedCinemaId: @js($cinemas[0]['id'] ?? ''),
          get selectedCinema() {
              return (@js($cinemas)).find(c => c.id === this.selectedCinemaId) ?? { name: '', location: '' };
          },
          openFaq: -1,
          toggleFaq(i) { this.openFaq = this.openFaq === i ? -1 : i; }
      }">

    <div class="relative flex flex-col min-h-screen overflow-x-hidden">

        
        <div class="fixed left-0 right-0 z-50 px-4 pointer-events-none top-4">
            <nav class="relative flex items-center justify-between h-16 max-w-6xl px-6 mx-auto transition-all border-2 shadow-md pointer-events-auto bg-white/90 backdrop-blur-md border-border rounded-xl gap-3">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight md:text-2xl font-heading">
                    <x-icon name="heroicon-s-ticket" class="w-6 h-6 md:w-8 md:h-8 text-main fill-main/20" />
                    Ticketra<span class="text-main">.</span>
                </a>

                
                <div class="hidden gap-4 font-bold text-sm tracking-wide md:flex lg:gap-6">
                    <a href="#fitur" class="transition-colors hover:text-main">Fitur</a>
                    <a href="#cara-pesan" class="transition-colors hover:text-main">Cara Pesan</a>
                    <a href="#promo" class="transition-colors hover:text-main">Promo</a>
                    <a href="#now-showing" class="transition-colors hover:text-main">Sedang Tayang</a>
                    <a href="#testimoni" class="transition-colors hover:text-main">Testimoni</a>
                </div>

                
                <div class="hidden gap-2 md:flex lg:gap-3">
                    <a href="{{ route('auth.page', ['view' => 'register']) }}" class="brutal-btn brutal-btn-secondary !py-1.5 !px-4 text-xs lg:text-sm whitespace-nowrap">Daftar</a>
                    <a href="{{ route('auth.page', ['view' => 'login']) }}" class="brutal-btn !py-1.5 !px-4 text-xs lg:text-sm whitespace-nowrap">Masuk</a>
                </div>

                
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 transition-colors rounded-lg cursor-pointer md:hidden hover:bg-gray-100" aria-label="Toggle menu">
                    <span x-show="!mobileMenuOpen" style="display: none;"><x-icon name="heroicon-s-bars-3" class="w-6 h-6" /></span>
                    <span x-show="mobileMenuOpen" style="display: none;"><x-icon name="heroicon-s-x-mark" class="w-6 h-6" /></span>
                </button>

                
                <div x-show="mobileMenuOpen" x-transition.opacity.duration.200ms
                     class="absolute top-[120%] left-0 right-0 bg-white border-2 border-border rounded-2xl shadow-lg p-4 flex flex-col gap-4 md:hidden z-50"
                     style="display: none;">
                    <a href="#fitur" @click="mobileMenuOpen = false" class="px-2 py-1 font-bold hover:text-main">Fitur</a>
                    <a href="#cara-pesan" @click="mobileMenuOpen = false" class="px-2 py-1 font-bold hover:text-main">Cara Pesan</a>
                    <a href="#promo" @click="mobileMenuOpen = false" class="px-2 py-1 font-bold hover:text-main">Promo</a>
                    <a href="#now-showing" @click="mobileMenuOpen = false" class="px-2 py-1 font-bold hover:text-main">Sedang Tayang</a>
                    <a href="#testimoni" @click="mobileMenuOpen = false" class="px-2 py-1 font-bold hover:text-main">Testimoni</a>
                    <hr class="border-border/50">
                    <div class="flex gap-3">
                        <a href="{{ route('auth.page', ['view' => 'login']) }}" class="brutal-btn flex-1 !py-2 text-sm text-center">Masuk</a>
                        <a href="{{ route('auth.page', ['view' => 'register']) }}" class="brutal-btn brutal-btn-secondary flex-1 !py-2 text-sm text-center">Daftar</a>
                    </div>
                </div>
            </nav>
        </div>

        
        <header class="grid items-center gap-12 px-4 pt-32 pb-16 mx-auto max-w-7xl md:pt-40 md:pb-24 lg:grid-cols-2">
            <div class="space-y-8 relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-pastel-lemon border-2 border-border rounded-full font-bold text-sm shadow-sm">
                    <span class="w-2 h-2 bg-accent-red rounded-full animate-pulse border border-border"></span>
                    Bioskop Kini dalam Genggaman
                </div>

                <h1 class="text-5xl md:text-7xl leading-[1.1]">
                    Lewati Antrean,<br>
                    <span class="bg-main px-2 inline-block -rotate-2 border-2 border-border mt-3 text-white shadow-sm">Amankan Kursimu.</span>
                </h1>

                <p class="text-lg md:text-xl font-medium opacity-80 max-w-md leading-relaxed">
                    Pesan tiket bioskop instan, pilih kursi favorit duluan, dan kumpulkan promo eksklusif tanpa harus kehabisan.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="#now-showing" class="brutal-btn text-lg !py-4 !px-8">
                        Lihat Film <x-icon name="heroicon-s-arrow-right" class="w-5 h-5" />
                    </a>
                    <a href="#cara-pesan" class="brutal-btn brutal-btn-secondary text-lg !py-4 !px-8">
                        Cara Pesan
                    </a>
                </div>

                <div class="flex items-center gap-6 md:gap-10 pt-6 border-t-2 border-border/20">
                    <div>
                        <div class="text-3xl font-extrabold text-main">500+</div>
                        <div class="text-sm font-bold opacity-70">Bioskop Partner</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-main">2M+</div>
                        <div class="text-sm font-bold opacity-70">Tiket Terjual</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-main flex items-center gap-1">4.9 <x-icon name="heroicon-s-star" class="w-6 h-6 fill-main" /></div>
                        <div class="text-sm font-bold opacity-70">Rating User</div>
                    </div>
                </div>
            </div>

            
            <div class="relative hidden lg:flex justify-center items-center perspective-1000">
                <div class="absolute w-[80%] h-[80%] bg-pastel-lavender border-2 border-border rounded-[2rem] transform rotate-6 shadow-lg translate-x-4"></div>
                <div class="absolute w-[80%] h-[80%] bg-pastel-mint border-2 border-border rounded-[2rem] transform -rotate-3 shadow-lg -translate-x-4"></div>

                <div class="relative w-full max-w-[340px] z-20 transform hover:-translate-y-4 hover:rotate-2 transition-all duration-500 group cursor-pointer">
                    <div class="brutal-box bg-white border-b-0 rounded-b-none p-6 shadow-[8px_0px_0px_var(--border)]">
                        <div class="absolute -left-4 -bottom-4 w-8 h-8 bg-[#f5f6f8] border-y-2 border-r-2 border-border rounded-r-full z-10"></div>
                        <div class="absolute -right-4 -bottom-4 w-8 h-8 bg-[#f5f6f8] border-y-2 border-l-2 border-border rounded-l-full z-10"></div>

                        <div class="flex justify-between items-center mb-5">
                            <span class="font-heading font-extrabold text-xl flex items-center gap-1">
                                <x-icon name="heroicon-s-ticket" class="w-5 h-5 text-main" /> Ticketra.
                            </span>
                            <span class="text-[10px] font-bold bg-pastel-lemon border border-border px-2 py-1 rounded">E-TICKET</span>
                        </div>

                        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=400&h=600" class="w-full h-40 object-cover border-2 border-border rounded-lg mb-4 grayscale-[10%] group-hover:grayscale-0 transition-all">

                        <h3 class="font-extrabold text-2xl leading-tight mb-1">Dune: Part Two</h3>
                        <p class="text-sm font-bold opacity-70 mb-2">CGV Grand Indonesia • Studio 1</p>
                    </div>

                    <div class="h-0 border-t-4 border-dashed border-border bg-white mx-1 relative z-0"></div>

                    <div class="brutal-box bg-pastel-sky border-t-0 rounded-t-none p-6 shadow-[8px_8px_0px_var(--border)]">
                        <div class="grid grid-cols-3 gap-2 text-sm font-bold mb-5 bg-white p-3 rounded-lg border-2 border-border">
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase">TGL</div>
                                <div>24 Apr</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase">JAM</div>
                                <div>19:45</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase">KURSI</div>
                                <div class="text-main">C4, C5</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-end h-12 w-full px-2 opacity-80 mix-blend-multiply">
                            <div class="w-1.5 h-full bg-border"></div>
                            <div class="w-3 h-full bg-border"></div>
                            <div class="w-1 h-full bg-border"></div>
                            <div class="w-2 h-full bg-border"></div>
                            <div class="w-4 h-[90%] bg-border"></div>
                            <div class="w-1 h-full bg-border"></div>
                            <div class="w-2 h-[80%] bg-border"></div>
                            <div class="w-1.5 h-full bg-border"></div>
                            <div class="w-3 h-full bg-border"></div>
                            <div class="w-1 h-full bg-border"></div>
                            <div class="w-2 h-[90%] bg-border"></div>
                            <div class="w-3 h-full bg-border"></div>
                        </div>
                        <div class="text-center text-[10px] font-mono font-bold tracking-widest mt-1 opacity-60">TK-90210-DUNE</div>
                    </div>
                </div>
            </div>
        </header>

        
        <section id="fitur" class="bg-secondary-background border-y-2 border-border py-16 md:py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl mb-4">Kenapa Harus <span class="bg-main text-white px-2 border-2 border-border transform -rotate-1 inline-block">Ticketra?</span></h2>
                    <p class="font-medium opacity-80 max-w-2xl mx-auto text-lg">Tinggalkan cara lama. Kami mendesain platform ini agar pengalaman nonton bioskopmu jadi semulus sutra.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="brutal-box p-8 bg-pastel-sky flex flex-col hover:-translate-y-2 transition-transform">
                        <div class="w-14 h-14 bg-white border-2 border-border rounded-full flex items-center justify-center mb-6 shadow-sm">
                            <x-icon name="heroicon-s-bolt" class="w-7 h-7 text-accent-red" />
                        </div>
                        <h3 class="text-2xl mb-3">Tanpa Antre, Tanpa Kertas</h3>
                        <p class="font-medium opacity-80 text-base">Pesan dari rumah, dapatkan E-Ticket seketika. Cukup scan barcode langsung di pintu studio. Sayangi waktumu dan bumi.</p>
                    </div>
                    <div class="brutal-box p-8 bg-pastel-peach flex flex-col md:mt-8 hover:-translate-y-2 transition-transform">
                        <div class="w-14 h-14 bg-white border-2 border-border rounded-full flex items-center justify-center mb-6 shadow-sm">
                            <x-icon name="heroicon-s-cube" class="w-7 h-7 text-accent-green" />
                        </div>
                        <h3 class="text-2xl mb-3">Pilih Kursi Strategis</h3>
                        <p class="font-medium opacity-80 text-base">Denah kursi real-time di semua layar. Amankan posisi tengah (sweet spot) dengan cepat sebelum didahului penonton lain.</p>
                    </div>
                    <div class="brutal-box p-8 bg-pastel-lavender flex flex-col md:mt-16 hover:-translate-y-2 transition-transform">
                        <div class="w-14 h-14 bg-white border-2 border-border rounded-full flex items-center justify-center mb-6 shadow-sm">
                            <x-icon name="heroicon-s-shield-check" class="w-7 h-7 text-accent-yellow" />
                        </div>
                        <h3 class="text-2xl mb-3">Aman & Banyak Untungnya</h3>
                        <p class="font-medium opacity-80 text-base">Transaksi aman terenkripsi. Dapatkan loyalty points di setiap pembelian tiket atau makanan yang bisa ditukar tiket gratis.</p>
                    </div>
                </div>
            </div>
        </section>

        
        <section id="cara-pesan" class="py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl md:text-5xl text-center mb-16">Pesan Tiket Semudah <span class="text-accent-red">1, 2, 3!</span></h2>

                <div class="grid md:grid-cols-4 gap-6 relative">
                    <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-1 border-t-2 border-dashed border-border z-0"></div>

                    @foreach ($steps as $i => $step)
                        <div class="relative z-10 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 bg-main text-white font-extrabold text-2xl flex items-center justify-center rounded-full border-4 border-border shadow-md mb-6 group-hover:scale-110 transition-transform">
                                {{ $i + 1 }}
                            </div>
                            <div class="brutal-box bg-white p-5 w-full">
                                <h4 class="text-xl mb-2 font-bold">{{ $step['title'] }}</h4>
                                <p class="text-sm font-medium opacity-75">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        
        <section id="promo" class="bg-secondary-background border-y-2 border-border py-16 overflow-hidden relative z-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="text-3xl md:text-4xl flex items-center gap-3">
                        Promo Spesial 💸
                    </h2>
                    <a href="#" class="font-bold underline decoration-2 decoration-main hover:text-main text-sm md:text-base">Lihat Semua Promo</a>
                </div>

                <div class="flex gap-6 overflow-x-auto pb-6 snap-x hide-scrollbar">
                    @foreach ($promos as $promo)
                        <div class="min-w-[300px] md:min-w-[420px] brutal-card p-6 md:p-8 flex flex-col justify-between snap-start {{ $promo['color'] }}">
                            <div>
                                <span class="inline-block px-3 py-1 bg-white border-2 border-border rounded-full text-xs font-bold mb-4 shadow-sm">{{ $promo['tag'] }}</span>
                                <h3 class="text-2xl md:text-3xl mb-3 leading-tight">{{ $promo['title'] }}</h3>
                                <p class="font-medium opacity-90 text-sm md:text-base">{{ $promo['desc'] }}</p>
                            </div>
                            <div class="mt-8 flex items-center justify-between">
                                <div class="font-bold text-sm bg-white/60 px-3 py-1.5 rounded border border-border/30">Kode: <span class="uppercase tracking-wider font-mono">{{ $promo['code'] }}</span></div>
                                <button class="w-10 h-10 bg-white border-2 border-border rounded-full flex items-center justify-center hover:scale-110 hover:bg-main hover:text-white transition-all shadow-sm" aria-label="Salin kode">
                                    <x-icon name="heroicon-s-document-duplicate" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        
        <section id="now-showing" class="max-w-7xl mx-auto px-4 py-16 md:py-24 overflow-hidden relative">
            <div class="brutal-box p-6 bg-pastel-lemon mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                <div>
                    <h3 class="text-2xl font-bold mb-1">Pilih Lokasimu</h3>
                    <p class="text-sm font-medium opacity-80">Tentukan bioskop favoritmu sebelum memilih film.</p>
                </div>
                <div class="relative w-full md:w-96">
                    <select x-model="selectedCinemaId" class="w-full appearance-none bg-white border-2 border-border rounded-base px-4 py-3 font-bold text-lg cursor-pointer focus:ring-4 focus:ring-ring focus:outline-none shadow-sm hover:shadow-md transition-shadow">
                        @foreach ($cinemas as $cinema)
                            <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }} ({{ $cinema['location'] }})</option>
                        @endforeach
                    </select>
                    <x-icon name="heroicon-s-chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none w-6 h-6" />
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 border-b-4 border-border pb-4 gap-4 relative z-10">
                <div>
                    <h2 class="text-3xl md:text-4xl flex items-center gap-3">
                        Sedang Tayang <span class="w-3 h-3 bg-accent-red rounded-full animate-pulse border border-border"></span>
                    </h2>
                    <p class="font-medium mt-2 text-lg text-main">Lokasi Terpilih: <span class="font-bold underline decoration-wavy" x-text="selectedCinema.name"></span></p>
                </div>
                <div class="flex gap-2">
                    <button @click="activeDayTab = 'today'" :class="activeDayTab === 'today' ? 'bg-main text-white shadow-sm' : 'bg-white text-gray-500 hover:bg-pastel-sky hover:text-main-foreground'" class="px-5 py-2.5 border-2 border-border rounded-full font-bold text-sm transition-colors cursor-pointer">Hari Ini</button>
                    <button @click="activeDayTab = 'tomorrow'" :class="activeDayTab === 'tomorrow' ? 'bg-main text-white shadow-sm' : 'bg-white text-gray-500 hover:bg-pastel-sky hover:text-main-foreground'" class="px-5 py-2.5 border-2 border-border rounded-full font-bold text-sm transition-colors cursor-pointer">Besok</button>
                </div>
            </div>

            <div class="flex gap-6 overflow-x-auto pb-8 snap-x hide-scrollbar relative z-10">
                @foreach ($movies as $movie)
                    <article class="min-w-[280px] md:min-w-[300px] lg:min-w-[280px] brutal-card group flex flex-col overflow-hidden bg-white snap-start">
                        <div class="relative aspect-[2/3] border-b-2 border-border overflow-hidden bg-gray-100">
                            <img src="{{ $movie['poster'] }}" alt="{{ $movie['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            <div class="absolute top-3 right-3 bg-accent-yellow border-2 border-border rounded-full px-2 py-1 text-sm font-bold flex items-center gap-1 shadow-sm">
                                <x-icon name="heroicon-s-star" class="w-4 h-4 fill-main-foreground" /> {{ $movie['rating'] }}
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ $movie['genre'] }}</span>
                            <h3 class="text-xl mb-2 line-clamp-1" title="{{ $movie['title'] }}">{{ $movie['title'] }}</h3>
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-1.5 text-sm font-medium opacity-80">
                                    <x-icon name="heroicon-s-clock" class="w-4 h-4" /> {{ $movie['duration'] }}
                                </div>
                                <div class="text-xs font-bold px-2 py-0.5 bg-pastel-lavender rounded border border-border">{{ $movie['ageRating'] }}</div>
                            </div>
                            @auth
                                <a href="{{ route('jadwal.kursi', ['jadwalTayang' => 'placeholder']) }}" class="brutal-btn w-full mt-auto text-sm !py-2.5">
                                    Pesan Sekarang
                                </a>
                            @else
                                <a href="{{ route('auth.page', ['view' => 'login']) }}" class="brutal-btn w-full mt-auto text-sm !py-2.5 text-center">
                                    Pesan Sekarang
                                </a>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        
        <section id="testimoni" class="bg-pastel-sky border-y-2 border-border py-16 md:py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl md:text-5xl text-center mb-16">Kata Mereka Tentang <span class="bg-white px-2 border-2 border-border transform rotate-1 inline-block">Ticketra</span></h2>

                <div class="grid md:grid-cols-3 gap-6">
                    @foreach ($testimonials as $testi)
                        <div class="brutal-card bg-white p-6 md:p-8 flex flex-col">
                            <div class="flex text-accent-yellow mb-4">
                                @for ($s = 0; $s < 5; $s++)
                                    <x-icon name="heroicon-s-star" class="w-5 h-5 fill-current" />
                                @endfor
                            </div>
                            <p class="font-medium text-lg mb-6 flex-1">"{{ $testi['quote'] }}"</p>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-pastel-mint border-2 border-border rounded-full flex items-center justify-center font-extrabold text-auth-foreground">
                                    {{ strtoupper(substr($testi['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold">{{ $testi['name'] }}</div>
                                    <div class="text-xs font-medium text-gray-500">{{ $testi['role'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        
        <section id="faq" class="max-w-3xl mx-auto px-4 py-16 md:py-24 relative z-10">
            <h2 class="text-3xl md:text-5xl font-bold mb-10 text-center">Bantuan (FAQ) 🙋‍♂️</h2>
            <div class="space-y-4">
                @foreach ($faqs as $index => $faq)
                    <div class="brutal-box bg-white overflow-hidden transition-all duration-300">
                        <button type="button" @click="toggleFaq({{ $index }})" class="w-full text-left p-5 flex justify-between items-center font-bold text-lg focus:outline-none cursor-pointer">
                            <span>{{ $faq['q'] }}</span>
                            <span :class="openFaq === {{ $index }} ? 'rotate-180' : ''" class="bg-pastel-sky p-1 border-2 border-border rounded-full transition-transform duration-300">
                                <x-icon name="heroicon-s-chevron-down" class="w-5 h-5" />
                            </span>
                        </button>
                        <div x-show="openFaq === {{ $index }}" x-transition.opacity class="px-5 pb-5 font-medium opacity-80 border-t-2 border-border/10" style="display: none;">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        
        <section id="cta" class="relative z-10 py-10 pb-24">
            <div class="max-w-5xl px-4 mx-auto">
                <div class="brutal-box bg-main p-8 md:p-16 text-center transform hover:scale-[1.02] transition-transform duration-300">
                    <h2 class="mb-6 text-4xl text-white md:text-5xl">Siap Untuk Pengalaman Nonton Terbaik?</h2>
                    <p class="font-medium text-white/90 text-lg mb-8 max-w-2xl mx-auto">Daftar sekarang, dapatkan diskon 50% untuk film pertamamu, dan katakan selamat tinggal pada antrean loket selamanya.</p>
                    <a href="{{ route('auth.page', ['view' => 'register']) }}" class="brutal-btn brutal-btn-secondary text-lg !py-4 !px-10 inline-flex">
                        Buat Akun Gratis <x-icon name="heroicon-s-sparkles" class="w-5 h-5" />
                    </a>
                </div>
            </div>
        </section>

        
        <footer class="bg-secondary-background border-t-4 border-x-0 border-b-0 border-border rounded-t-[3rem] md:rounded-t-[5rem] mt-auto relative z-10 pt-16 md:pt-20">
            <div class="max-w-7xl mx-auto px-6 pb-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 text-3xl font-heading font-extrabold tracking-tight mb-4">
                            <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-main fill-main/20" />
                            Ticketra<span class="text-main">.</span>
                        </div>
                        <p class="font-medium opacity-80 max-w-sm mb-8 text-lg">Aplikasi pemesanan tiket bioskop #1 yang membebaskanmu dari antrean panjang. Pilih kursi, bayar, dan nikmati filmnya.</p>
                        <div class="flex gap-4">
                            <a href="#" class="w-12 h-12 bg-white border-2 border-border rounded-full flex items-center justify-center hover:bg-main hover:text-white hover:-translate-y-1 transition-all shadow-sm" aria-label="Instagram">
                                <x-icon name="heroicon-s-camera" class="w-5 h-5" />
                            </a>
                            <a href="#" class="w-12 h-12 bg-white border-2 border-border rounded-full flex items-center justify-center hover:bg-main hover:text-white hover:-translate-y-1 transition-all shadow-sm" aria-label="Twitter">
                                <x-icon name="heroicon-s-chat-bubble-left" class="w-5 h-5" />
                            </a>
                            <a href="#" class="w-12 h-12 bg-white border-2 border-border rounded-full flex items-center justify-center hover:bg-main hover:text-white hover:-translate-y-1 transition-all shadow-sm" aria-label="YouTube">
                                <x-icon name="heroicon-s-play" class="w-5 h-5" />
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xl mb-6">Eksplorasi</h4>
                        <ul class="space-y-3 font-medium opacity-80">
                            <li><a href="#now-showing" class="hover:text-main hover:underline decoration-2">Film Sedang Tayang</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Film Akan Datang</a></li>
                            <li><a href="#promo" class="hover:text-main hover:underline decoration-2">Promo & Penawaran</a></li>
                            <li><a href="#cara-pesan" class="hover:text-main hover:underline decoration-2">Cara Pesan</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xl mb-6">Bantuan</h4>
                        <ul class="space-y-3 font-medium opacity-80">
                            <li><a href="#faq" class="hover:text-main hover:underline decoration-2">Pusat Bantuan (FAQ)</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Hubungi Kami</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-16 pt-8 border-t-2 border-border/20 flex flex-col md:flex-row items-center justify-between font-bold text-sm opacity-60 gap-4 text-center">
                    <p>&copy; {{ date('Y') }} Ticketra. All rights reserved.</p>
                    <p>Designed with <x-icon name="heroicon-s-heart" class="w-4 h-4 inline text-accent-red fill-accent-red" /> for Movie Lovers in Indonesia</p>
                </div>
            </div>
        </footer>

    </div>
</body>
</html>
