<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ticketra. - Bebas Antre, Nonton Asyik | Pesan Tiket Bioskop Online</title>

    <x-favicons />

    <meta name="description" content="Pesan tiket bioskop instan, pilih kursi favorit, dan kumpulkan promo eksklusif tanpa antre. Booking online bioskop di Jakarta, Bogor, Depok, Tangerang, Bekasi.">
    <link rel="canonical" href="{{ url('/') }}">

    <meta property="og:site_name" content="Ticketra.">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Ticketra. - Bebas Antre, Nonton Asyik | Pesan Tiket Bioskop Online">
    <meta property="og:description" content="Pesan tiket bioskop instan, pilih kursi favorit, dan kumpulkan promo eksklusif tanpa antre. Booking online bioskop di Jakarta, Bogor, Depok, Tangerang, Bekasi.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ url('/images/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Ticketra. - Bebas Antre, Nonton Asyik | Pesan Tiket Bioskop Online">
    <meta name="twitter:description" content="Pesan tiket bioskop instan, pilih kursi favorit, dan kumpulkan promo eksklusif tanpa antre. Booking online bioskop di Jakarta, Bogor, Depok, Tangerang, Bekasi.">

    <script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Ticketra.",
    "url": "{{ config('app.url') }}",
    "description": "Pesan tiket bioskop instan, pilih kursi favorit, dan kumpulkan promo eksklusif tanpa antre. Booking online bioskop di Jakarta, Bogor, Depok, Tangerang, Bekasi.",
    "foundingDate": "2024",
    "areaServed": { "@@type": "Country", "name": "Indonesia" }
}
</script>
    <script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "Ticketra.",
    "url": "{{ config('app.url') }}",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": {
            "@@type": "EntryPoint",
            "urlTemplate": "{{ url('/film') }}?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

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
            <nav class="relative flex items-center justify-between h-16 max-w-6xl gap-3 px-6 mx-auto transition-all border-2 shadow-md pointer-events-auto bg-white/90 backdrop-blur-md border-border rounded-xl">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight md:text-2xl font-heading">
                    <x-icon name="heroicon-s-ticket" class="w-6 h-6 md:w-8 md:h-8 text-main fill-main/20" />
                    Ticketra<span class="text-main">.</span>
                </a>

                
                <div class="hidden gap-4 text-sm font-bold tracking-wide md:flex lg:gap-6">
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
            <div class="relative z-10 space-y-8">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-pastel-lemon border-2 border-border rounded-full font-bold text-sm shadow-sm">
                    <span class="w-2 h-2 border rounded-full bg-accent-red animate-pulse border-border"></span>
                    Bioskop Kini dalam Genggaman
                </div>

                <h1 class="text-5xl md:text-7xl leading-[1.1]">
                    Lewati Antrean,<br>
                    <span class="inline-block px-2 mt-3 text-white border-2 shadow-sm bg-main -rotate-2 border-border">Amankan Kursimu.</span>
                </h1>

                <p class="max-w-md text-lg font-medium leading-relaxed md:text-xl opacity-80">
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

                <div class="flex items-center gap-6 pt-6 border-t-2 md:gap-10 border-border/20">
                    <div>
                        <div class="text-3xl font-extrabold text-main">500+</div>
                        <div class="text-sm font-bold opacity-70">Bioskop Partner</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-main">2M+</div>
                        <div class="text-sm font-bold opacity-70">Tiket Terjual</div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1 text-3xl font-extrabold text-main">4.9 <x-icon name="heroicon-s-star" class="w-6 h-6 fill-main" /></div>
                        <div class="text-sm font-bold opacity-70">Rating User</div>
                    </div>
                </div>
            </div>

            
            <div class="relative items-center justify-center hidden lg:flex perspective-1000">
                <div class="absolute w-[80%] h-[80%] bg-pastel-lavender border-2 border-border rounded-[2rem] transform rotate-6 shadow-lg translate-x-4"></div>
                <div class="absolute w-[80%] h-[80%] bg-pastel-mint border-2 border-border rounded-[2rem] transform -rotate-3 shadow-lg -translate-x-4"></div>

                <div class="relative w-full max-w-[340px] z-20 transform hover:-translate-y-4 hover:rotate-2 transition-all duration-500 group cursor-pointer">
                    <div class="brutal-box bg-white border-b-0 rounded-b-none p-6 shadow-[8px_0px_0px_var(--border)]">
                        <div class="absolute -left-4 -bottom-4 w-8 h-8 bg-[#f5f6f8] border-y-2 border-r-2 border-border rounded-r-full z-10"></div>
                        <div class="absolute -right-4 -bottom-4 w-8 h-8 bg-[#f5f6f8] border-y-2 border-l-2 border-border rounded-l-full z-10"></div>

                        <div class="flex items-center justify-between mb-5">
                            <span class="flex items-center gap-1 text-xl font-extrabold font-heading">
                                <x-icon name="heroicon-s-ticket" class="w-5 h-5 text-main" /> Ticketra.
                            </span>
                            <span class="text-[10px] font-bold bg-pastel-lemon border border-border px-2 py-1 rounded">E-TICKET</span>
                        </div>

                        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=400&h=600" class="w-full h-40 object-cover border-2 border-border rounded-lg mb-4 grayscale-[10%] group-hover:grayscale-0 transition-all">

                        <h3 class="mb-1 text-2xl font-extrabold leading-tight">Dune: Part Two</h3>
                        <p class="mb-2 text-sm font-bold opacity-70">CGV Grand Indonesia • Studio 1</p>
                    </div>

                    <div class="relative z-0 h-0 mx-1 bg-white border-t-4 border-dashed border-border"></div>

                    <div class="brutal-box bg-pastel-sky border-t-0 rounded-t-none p-6 shadow-[8px_8px_0px_var(--border)]">
                        <div class="grid grid-cols-3 gap-2 p-3 mb-5 text-sm font-bold bg-white border-2 rounded-lg border-border">
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

                        <div class="flex items-end justify-between w-full h-12 px-2 opacity-80 mix-blend-multiply">
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

        
        <section id="fitur" class="relative z-10 py-16 bg-secondary-background border-y-2 border-border md:py-24">
            <div class="px-4 mx-auto max-w-7xl">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl md:text-5xl">Kenapa Harus <span class="inline-block px-2 text-white transform border-2 bg-main border-border -rotate-1">Ticketra?</span></h2>
                    <p class="max-w-2xl mx-auto text-lg font-medium opacity-80">Tinggalkan cara lama. Kami mendesain platform ini agar pengalaman nonton bioskopmu jadi semulus sutra.</p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <div class="flex flex-col p-8 transition-transform brutal-box bg-pastel-sky hover:-translate-y-2">
                        <div class="flex items-center justify-center mb-6 bg-white border-2 rounded-full shadow-sm w-14 h-14 border-border">
                            <x-icon name="heroicon-s-bolt" class="w-7 h-7 text-accent-red" />
                        </div>
                        <h3 class="mb-3 text-2xl">Tanpa Antre, Tanpa Kertas</h3>
                        <p class="text-base font-medium opacity-80">Pesan dari rumah, dapatkan E-Ticket seketika. Cukup scan barcode langsung di pintu studio. Sayangi waktumu dan bumi.</p>
                    </div>
                    <div class="flex flex-col p-8 transition-transform brutal-box bg-pastel-peach md:mt-8 hover:-translate-y-2">
                        <div class="flex items-center justify-center mb-6 bg-white border-2 rounded-full shadow-sm w-14 h-14 border-border">
                            <x-icon name="heroicon-s-cube" class="w-7 h-7 text-accent-green" />
                        </div>
                        <h3 class="mb-3 text-2xl">Pilih Kursi Strategis</h3>
                        <p class="text-base font-medium opacity-80">Denah kursi real-time di semua layar. Amankan posisi tengah (sweet spot) dengan cepat sebelum didahului penonton lain.</p>
                    </div>
                    <div class="flex flex-col p-8 transition-transform brutal-box bg-pastel-lavender md:mt-16 hover:-translate-y-2">
                        <div class="flex items-center justify-center mb-6 bg-white border-2 rounded-full shadow-sm w-14 h-14 border-border">
                            <x-icon name="heroicon-s-shield-check" class="w-7 h-7 text-accent-yellow" />
                        </div>
                        <h3 class="mb-3 text-2xl">Aman & Banyak Untungnya</h3>
                        <p class="text-base font-medium opacity-80">Transaksi aman terenkripsi. Dapatkan loyalty points di setiap pembelian tiket atau makanan yang bisa ditukar tiket gratis.</p>
                    </div>
                </div>
            </div>
        </section>

        
        <section id="cara-pesan" class="py-16 md:py-24">
            <div class="px-4 mx-auto max-w-7xl">
                <h2 class="mb-16 text-3xl text-center md:text-5xl">Pesan Tiket Semudah <span class="text-accent-red">1, 2, 3!</span></h2>

                <div class="relative grid gap-6 md:grid-cols-4">
                    <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-1 border-t-2 border-dashed border-border z-0"></div>

                    @foreach ($steps as $i => $step)
                        <div class="relative z-10 flex flex-col items-center text-center group">
                            <div class="flex items-center justify-center w-16 h-16 mb-6 text-2xl font-extrabold text-white transition-transform border-4 rounded-full shadow-md bg-main border-border group-hover:scale-110">
                                {{ $i + 1 }}
                            </div>
                            <div class="w-full p-5 bg-white brutal-box">
                                <h4 class="mb-2 text-xl font-bold">{{ $step['title'] }}</h4>
                                <p class="text-sm font-medium opacity-75">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        
        <section id="promo" class="relative z-10 py-16 overflow-hidden bg-secondary-background border-y-2 border-border">
            <div class="px-4 mx-auto max-w-7xl">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="flex items-center gap-3 text-3xl md:text-4xl">
                        Promo Spesial 💸
                    </h2>
                    <a href="#" class="text-sm font-bold underline decoration-2 decoration-main hover:text-main md:text-base">Lihat Semua Promo</a>
                </div>

                <div class="flex gap-6 pb-6 overflow-x-auto snap-x hide-scrollbar">
                    @foreach ($promos as $promo)
                        <div class="min-w-[300px] md:min-w-[420px] brutal-card p-6 md:p-8 flex flex-col justify-between snap-start {{ $promo['color'] }}">
                            <div>
                                <span class="inline-block px-3 py-1 mb-4 text-xs font-bold bg-white border-2 rounded-full shadow-sm border-border">{{ $promo['tag'] }}</span>
                                <h3 class="mb-3 text-2xl leading-tight md:text-3xl">{{ $promo['title'] }}</h3>
                                <p class="text-sm font-medium opacity-90 md:text-base">{{ $promo['desc'] }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-8">
                                <div class="font-bold text-sm bg-white/60 px-3 py-1.5 rounded border border-border/30">Kode: <span class="font-mono tracking-wider uppercase">{{ $promo['code'] }}</span></div>
                                <button class="flex items-center justify-center w-10 h-10 transition-all bg-white border-2 rounded-full shadow-sm border-border hover:scale-110 hover:bg-main hover:text-white" aria-label="Salin kode">
                                    <x-icon name="heroicon-s-document-duplicate" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        
        <section id="now-showing" class="relative px-4 py-16 mx-auto overflow-hidden max-w-7xl md:py-24">
            <div class="relative z-10 flex flex-col items-start justify-between gap-6 p-6 mb-12 brutal-box bg-pastel-lemon md:flex-row md:items-center">
                <div>
                    <h3 class="mb-1 text-2xl font-bold">Pilih Lokasimu</h3>
                    <p class="text-sm font-medium opacity-80">Tentukan bioskop favoritmu sebelum memilih film.</p>
                </div>
                <div class="relative w-full md:w-96">
                    <select x-model="selectedCinemaId" class="w-full px-4 py-3 text-lg font-bold transition-shadow bg-white border-2 shadow-sm appearance-none cursor-pointer border-border rounded-base focus:ring-4 focus:ring-ring focus:outline-none hover:shadow-md">
                        @foreach ($cinemas as $cinema)
                            <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }} ({{ $cinema['location'] }})</option>
                        @endforeach
                    </select>
                    <x-icon name="heroicon-s-chevron-down" class="absolute w-6 h-6 -translate-y-1/2 pointer-events-none right-4 top-1/2" />
                </div>
            </div>

            <div class="relative z-10 flex flex-col justify-between gap-4 pb-4 mb-8 border-b-4 md:flex-row md:items-end border-border">
                <div>
                    <h2 class="flex items-center gap-3 text-3xl md:text-4xl">
                        Sedang Tayang <span class="w-3 h-3 border rounded-full bg-accent-red animate-pulse border-border"></span>
                    </h2>
                    <p class="mt-2 text-lg font-medium text-main">Lokasi Terpilih: <span class="font-bold underline decoration-wavy" x-text="selectedCinema.name"></span></p>
                </div>
                <div class="flex gap-2">
                    <button @click="activeDayTab = 'today'" :class="activeDayTab === 'today' ? 'bg-main text-white shadow-sm' : 'bg-white text-gray-500 hover:bg-pastel-sky hover:text-main-foreground'" class="px-5 py-2.5 border-2 border-border rounded-full font-bold text-sm transition-colors cursor-pointer">Hari Ini</button>
                    <button @click="activeDayTab = 'tomorrow'" :class="activeDayTab === 'tomorrow' ? 'bg-main text-white shadow-sm' : 'bg-white text-gray-500 hover:bg-pastel-sky hover:text-main-foreground'" class="px-5 py-2.5 border-2 border-border rounded-full font-bold text-sm transition-colors cursor-pointer">Besok</button>
                </div>
            </div>

            <div class="relative z-10 flex gap-6 pb-8 overflow-x-auto snap-x hide-scrollbar">
                @foreach ($movies as $movie)
                    <article class="min-w-[280px] md:min-w-[300px] lg:min-w-[280px] brutal-card group flex flex-col overflow-hidden bg-white snap-start">
                        <div class="relative aspect-[2/3] border-b-2 border-border overflow-hidden bg-gray-100 cursor-pointer"
                             onclick="window.location.href = '{{ route('film.show', $movie['id']) }}'">
                            <img src="{{ $movie['poster'] }}" alt="{{ $movie['title'] }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <div class="absolute flex items-center gap-1 px-2 py-1 text-sm font-bold border-2 rounded-full shadow-sm top-3 right-3 bg-accent-yellow border-border">
                                <x-icon name="heroicon-s-star" class="w-4 h-4 fill-main-foreground" /> {{ $movie['rating'] }}
                            </div>
                        </div>
                        <div class="flex flex-col flex-1 p-5">
                            <span class="mb-1 text-xs font-bold tracking-wider text-gray-500 uppercase">{{ $movie['genre'] }}</span>
                            <h3 class="mb-2 text-xl cursor-pointer line-clamp-1 hover:text-main" title="{{ $movie['title'] }}"
                                onclick="window.location.href = '{{ route('film.show', $movie['id']) }}'">
                                {{ $movie['title'] }}
                            </h3>
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-1.5 text-sm font-medium opacity-80">
                                    <x-icon name="heroicon-s-clock" class="w-4 h-4" /> {{ $movie['duration'] }}
                                </div>
                                <div class="text-xs font-bold px-2 py-0.5 bg-pastel-lavender rounded border border-border">{{ $movie['ageRating'] }}</div>
                            </div>
                            <a href="{{ route('film.show', $movie['id']) }}" class="brutal-btn w-full mt-auto text-sm !py-2.5 text-center">
                                Pesan Sekarang
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        
        <section id="testimoni" class="relative z-10 py-16 bg-pastel-sky border-y-2 border-border md:py-24">
            <div class="px-4 mx-auto max-w-7xl">
                <h2 class="mb-16 text-3xl text-center md:text-5xl">Kata Mereka Tentang <span class="inline-block px-2 transform bg-white border-2 border-border rotate-1">Ticketra</span></h2>

                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($testimonials as $testi)
                        <div class="flex flex-col p-6 bg-white brutal-card md:p-8">
                            <div class="flex mb-4 text-accent-yellow">
                                @for ($s = 0; $s < 5; $s++)
                                    <x-icon name="heroicon-s-star" class="w-5 h-5 fill-current" />
                                @endfor
                            </div>
                            <p class="flex-1 mb-6 text-lg font-medium">"{{ $testi['quote'] }}"</p>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center w-12 h-12 font-extrabold border-2 rounded-full bg-pastel-mint border-border text-auth-foreground">
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

        
        <section id="faq" class="relative z-10 max-w-3xl px-4 py-16 mx-auto md:py-24">
            <h2 class="mb-10 text-3xl font-bold text-center md:text-5xl">Bantuan (FAQ) 🙋‍♂️</h2>
            <div class="space-y-4">
                @foreach ($faqs as $index => $faq)
                    <div class="overflow-hidden transition-all duration-300 bg-white brutal-box">
                        <button type="button" @click="toggleFaq({{ $index }})" class="flex items-center justify-between w-full p-5 text-lg font-bold text-left cursor-pointer focus:outline-none">
                            <span>{{ $faq['q'] }}</span>
                            <span :class="openFaq === {{ $index }} ? 'rotate-180' : ''" class="p-1 transition-transform duration-300 border-2 rounded-full bg-pastel-sky border-border">
                                <x-icon name="heroicon-s-chevron-down" class="w-5 h-5" />
                            </span>
                        </button>
                        <div x-show="openFaq === {{ $index }}" x-transition.opacity class="px-5 pb-5 font-medium border-t-2 opacity-80 border-border/10" style="display: none;">
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
                    <p class="max-w-2xl mx-auto mb-8 text-lg font-medium text-white/90">Daftar sekarang, dapatkan diskon 50% untuk film pertamamu, dan katakan selamat tinggal pada antrean loket selamanya.</p>
                    <a href="{{ route('auth.page', ['view' => 'register']) }}" class="brutal-btn brutal-btn-secondary text-lg !py-4 !px-10 inline-flex">
                        Buat Akun Gratis <x-icon name="heroicon-s-sparkles" class="w-5 h-5" />
                    </a>
                </div>
            </div>
        </section>

        
        <footer class="bg-secondary-background border-t-4 border-x-0 border-b-0 border-border rounded-t-[3rem] md:rounded-t-[5rem] mt-auto relative z-10 pt-16 md:pt-20">
            <div class="px-6 pb-12 mx-auto max-w-7xl">
                <div class="grid grid-cols-1 gap-12 md:grid-cols-4 md:gap-8">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 mb-4 text-3xl font-extrabold tracking-tight font-heading">
                            <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-main fill-main/20" />
                            Ticketra<span class="text-main">.</span>
                        </div>
                        <p class="max-w-sm mb-8 text-lg font-medium opacity-80">Aplikasi pemesanan tiket bioskop #1 yang membebaskanmu dari antrean panjang. Pilih kursi, bayar, dan nikmati filmnya.</p>
                        <div class="flex gap-4">
                            <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-white border-2 rounded-full shadow-sm border-border hover:bg-main hover:text-white hover:-translate-y-1" aria-label="Instagram">
                                 <x-fab-instagram class="w-5 h-5"/>
                            </a>
                            <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-white border-2 rounded-full shadow-sm border-border hover:bg-main hover:text-white hover:-translate-y-1" aria-label="Youtube">
                                <x-fab-youtube class="w-5 h-5"/>
                            </a>
                            <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-white border-2 rounded-full shadow-sm border-border hover:bg-main hover:text-white hover:-translate-y-1" aria-label="Twitter">
                                <x-fab-x-twitter class="w-5 h-5"/>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-6 text-xl">Eksplorasi</h4>
                        <ul class="space-y-3 font-medium opacity-80">
                            <li><a href="#now-showing" class="hover:text-main hover:underline decoration-2">Film Sedang Tayang</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Film Akan Datang</a></li>
                            <li><a href="#promo" class="hover:text-main hover:underline decoration-2">Promo & Penawaran</a></li>
                            <li><a href="#cara-pesan" class="hover:text-main hover:underline decoration-2">Cara Pesan</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-6 text-xl">Bantuan</h4>
                        <ul class="space-y-3 font-medium opacity-80">
                            <li><a href="#faq" class="hover:text-main hover:underline decoration-2">Pusat Bantuan (FAQ)</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-main hover:underline decoration-2">Hubungi Kami</a></li>
                        </ul>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-4 pt-8 mt-16 text-sm font-bold text-center border-t-2 border-border/20 opacity-60">
                    <p>&copy; {{ date('Y') }} Ticketra. All rights reserved.</p>
                </div>
            </div>
        </footer>

    </div>
</body>
</html>
