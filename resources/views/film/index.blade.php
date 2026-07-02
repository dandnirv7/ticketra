@section('title', 'Film Sedang & Akan Tayang di Bioskop - Ticketra.')

@section('seo')
<meta name="description" content="Lihat daftar film terbaru dan akan tayang di bioskop favoritmu. Pesan tiket online dengan mudah dan cepat hanya di Ticketra.">
<link rel="canonical" href="{{ url('/film') }}">

<meta property="og:site_name" content="Ticketra.">
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="website">
<meta property="og:title" content="Film Sedang & Akan Tayang di Bioskop - Ticketra.">
<meta property="og:description" content="Lihat daftar film terbaru dan akan tayang di bioskop favoritmu. Pesan tiket online dengan mudah dan cepat hanya di Ticketra.">
<meta property="og:url" content="{{ url('/film') }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Film Sedang & Akan Tayang di Bioskop - Ticketra.">
<meta name="twitter:description" content="Lihat daftar film terbaru dan akan tayang di bioskop favoritmu. Pesan tiket online dengan mudah dan cepat hanya di Ticketra.">
@stop

<x-app-layout>

    <div x-data="{
        searchQuery: new URLSearchParams(window.location.search).get('q') || '',
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'Semua',
        sortBy: new URLSearchParams(window.location.search).get('sort') || 'popular',
        toastMessage: '',
        showToast: false,
        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },
        init() {
            this.$watch('searchQuery', (val) => {
                const url = new URL(window.location);
                if (!val.trim()) url.searchParams.delete('q');
                else url.searchParams.set('q', val.trim());
                window.history.replaceState({}, '', url);
            });
            this.$watch('activeTab', (val) => {
                const url = new URL(window.location);
                if (val === 'Semua') url.searchParams.delete('tab');
                else url.searchParams.set('tab', val);
                window.history.replaceState({}, '', url);
            });
            this.$watch('sortBy', (val) => {
                const url = new URL(window.location);
                if (val === 'popular') url.searchParams.delete('sort');
                else url.searchParams.set('sort', val);
                window.history.replaceState({}, '', url);
            });
        },
        setTab(tab) {
            this.activeTab = tab;
        },
        setSort(sort) {
            this.sortBy = sort;
        },
        matchesSearch(title, genre, synopsis) {
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return title.toLowerCase().includes(q) || 
                   genre.toLowerCase().includes(q) || 
                   synopsis.toLowerCase().includes(q);
        },
        sortedMovies(movies) {
            const arr = [...movies];
            if (this.sortBy === 'rating') {
                arr.sort((a, b) => b.rating - a.rating);
            } else if (this.sortBy === 'latest') {
                arr.sort((a, b) => new Date(b.tanggal_rilis) - new Date(a.tanggal_rilis));
            }
            return arr;
        },
        hasAnyMovies() {
            let matchesShowing = false;
            let matchesComing = false;

            if (this.activeTab === 'Semua' || this.activeTab === 'Sedang Tayang') {
                matchesShowing = @js($nowShowing).some(m => this.matchesSearch(m.judul, m.genre, m.sinopsis || ''));
            }
            if (this.activeTab === 'Semua' || this.activeTab === 'Akan Tayang') {
                matchesComing = @js($comingSoon).some(m => this.matchesSearch(m.judul, m.genre, m.sinopsis || ''));
            }

            return matchesShowing || matchesComing;
        }
    }" class="relative space-y-8">

        
        <div x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-20px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-20px]"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-[999] bg-pastel-mint border-[3px] border-border px-6 py-3.5 rounded-xl font-bold shadow-[4px_4px_0px_rgba(0,0,0,1)] text-xs text-foreground flex items-center gap-2"
            style="display: none;">
            <x-icon name="heroicon-s-check-circle" class="w-5 h-5 text-emerald-600" />
            <span x-text="toastMessage"></span>
        </div>

        


        
        @if($heroMovie)
        <section class="brutal-card bg-pastel-lavender p-6 md:p-8 overflow-hidden relative border-[3px] border-border rounded-[24px] shadow-[8px_8px_0px_var(--border)]">
            <div class="grid items-center gap-6 lg:grid-cols-12">
                <div class="relative z-10 space-y-4 md:space-y-5 lg:col-span-8">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-border text-white rounded-full text-[9px] font-extrabold uppercase tracking-wider">
                        <x-icon name="heroicon-s-fire" class="w-3 h-3 text-accent-red fill-current" /> TRENDING NOW
                    </span>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-heading font-black leading-[0.95] uppercase tracking-tight max-w-lg">
                        {{ $heroMovie->judul }}
                    </h2>
                    <p class="max-w-xl text-sm font-medium leading-relaxed text-foreground/80">
                        {{ Str::limit($heroMovie->sinopsis, 180) }}
                    </p>
                    <div class="flex flex-wrap gap-3 pt-1">
                        <a href="{{ route('film.show', $heroMovie->id) }}" class="brutal-btn bg-accent-green !py-3 !px-6 text-xs tracking-wider shadow-neo-sm border-[3px] border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                            <x-icon name="heroicon-s-ticket" class="w-4 h-4 text-border" />
                            Lihat Detail
                        </a>
                        <a href="{{ route('film.show', $heroMovie->id) }}#trailer-section" class="brutal-btn bg-white hover:bg-pastel-sky/20 text-border !py-3 !px-6 text-xs tracking-wider shadow-neo-sm border-[3px] border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 border-dashed">
                            <x-icon name="heroicon-s-play" class="w-4 h-4 fill-current text-border" />
                            Tonton Trailer
                        </a>
                    </div>
                </div>

                
                <div class="relative flex justify-center lg:col-span-4">
                    <div class="relative w-64 cursor-pointer h-96 group" @click="window.location.href = '{{ route('film.show', $heroMovie->id) }}'">
                        <div class="absolute inset-0 bg-accent-yellow border-[3px] border-border rounded-[24px] transform rotate-3 shadow-[6px_6px_0px_var(--border)] transition-transform group-hover:rotate-0"></div>
                        <div class="absolute inset-0 bg-white border-[3px] border-border rounded-[24px] overflow-hidden shadow-[6px_6px_0px_var(--border)] transition-transform group-hover:-translate-y-1">
                            <img src="{{ $heroMovie->poster_url }}" alt="{{ $heroMovie->judul }}" class="object-cover w-full h-full" />
                            <div class="absolute top-4 right-4 px-3 py-1 bg-accent-yellow border-2 border-border rounded-full text-xs font-bold flex items-center gap-1 shadow-[2px_2px_0px_var(--border)]">
                                <x-icon name="heroicon-s-star" class="w-4 h-4 fill-current text-border" />
                                <span>{{ number_format($heroMovie->rating, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b-[3px] border-border/10 pb-5">
            
            <div class="flex flex-wrap gap-2">
                <template x-for="tab in ['Semua', 'Sedang Tayang', 'Akan Tayang']" :key="tab">
                    <button @click="setTab(tab)"
                        :class="activeTab === tab ? 'bg-accent-green text-border border-border shadow-none translate-x-[2px] translate-y-[2px]' : 'bg-white text-border hover:bg-slate-50 hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5'"
                        class="px-4 py-2 rounded-xl border-[3px] border-border text-xs font-extrabold uppercase tracking-wide transition-all shadow-[2px_2px_0px_var(--border)] active:translate-y-[1px] active:shadow-none focus:outline-none"
                        x-text="tab">
                    </button>
                </template>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2" x-data="{ showSortDropdown: false }">
                    <span class="text-xs font-black text-foreground">Urutkan:</span>
                    <div class="relative">
                        <button @click="showSortDropdown = !showSortDropdown" @click.away="showSortDropdown = false"
                            class="flex items-center gap-2 px-4 py-2.5 bg-secondary-background border-[3px] border-border rounded-xl text-xs font-bold whitespace-nowrap shadow-none hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5 hover:bg-pastel-lemon/20 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                            <x-icon name="heroicon-s-arrows-up-down" class="w-4 h-4 text-border" />
                            <span x-text="sortBy === 'popular' ? 'Popularitas' : (sortBy === 'rating' ? 'Rating Tertinggi' : 'Terbaru')">Popularitas</span>
                        </button>
                        <div x-show="showSortDropdown" x-transition.opacity
                            class="absolute right-0 mt-2 w-44 bg-white border-[3px] border-border rounded-xl shadow-[4px_4px_0px_var(--border)] z-50 py-1 text-xs font-bold text-foreground"
                            style="display: none;">
                            <button @click="setSort('popular'); showSortDropdown = false" class="px-4 py-2.5 w-full text-left border-b-2 transition-colors hover:bg-pastel-mint/30 border-border/10">Popularitas</button>
                            <button @click="setSort('rating'); showSortDropdown = false" class="px-4 py-2.5 w-full text-left border-b-2 transition-colors hover:bg-pastel-mint/30 border-border/10">Rating Tertinggi</button>
                            <button @click="setSort('latest'); showSortDropdown = false" class="px-4 py-2.5 w-full text-left transition-colors hover:bg-pastel-mint/30">Terbaru</button>
                        </div>
                    </div>
                </div>

                <div class="relative flex-1 max-w-xs">
                    <input type="text"
                        x-model.debounce.300ms="searchQuery"
                        placeholder="Cari film atau genre..."
                        class="w-full bg-secondary-background border-[3px] border-border rounded-full pl-5 pr-12 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2" />
                    <div class="absolute -translate-y-1/2 right-4 top-1/2 text-border">
                        <x-icon name="heroicon-s-magnifying-glass" class="w-4 h-4 text-border" />
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="!hasAnyMovies()"
            class="text-center py-24 bg-white border-[3px] border-border border-dashed rounded-[2rem]"
            style="display: none;">
            <div class="w-20 h-20 bg-pastel-sky border-[3px] border-border rounded-full flex items-center justify-center mx-auto mb-4 text-4xl transform rotate-12 shadow-[3px_3px_0px_var(--border)]">🍿</div>
            <h3 class="text-3xl mb-2 font-heading font-black uppercase tracking-tight">Ups, Film Gak Ketemu</h3>
            <p class="font-medium opacity-70 text-lg">Coba cari judul film, genre, atau sinopsis lainnya.</p>
        </div>

        <section x-show="hasAnyMovies() && (activeTab === 'Semua' || activeTab === 'Sedang Tayang')" class="space-y-6">
            <div class="flex items-center gap-2 pb-2">
                <h2 class="text-2xl font-black tracking-tight uppercase text-foreground">SEDANG TAYANG</h2>
                <span class="w-3 h-3 border-2 rounded-full bg-accent-red animate-pulse border-border"></span>
            </div>

            
            
            <div class="hidden grid-cols-2 gap-6 md:grid md:grid-cols-3 lg:grid-cols-4">
                @foreach($nowShowing as $movie)
                <article x-show="matchesSearch('{{ addslashes($movie->judul) }}', '{{ addslashes($movie->genre) }}', '{{ addslashes($movie->sinopsis) }}')"
                    class="border-[3px] border-border rounded-[20px] overflow-hidden bg-white shadow-[4px_4px_0px_var(--border)] flex flex-col justify-between group transition-all duration-300">

                    
                    <div class="relative aspect-[2/3] overflow-hidden border-b-[3px] border-border bg-slate-100 cursor-pointer"
                        @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->judul }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />

                        
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white border-2 border-border rounded-lg text-[9px] font-extrabold flex items-center gap-0.5 shadow-[1.5px_1.5px_0px_var(--border)]">
                            <x-icon name="heroicon-s-star" class="w-3 h-3 fill-current text-accent-yellow" />
                            <span>{{ number_format($movie->rating, 1) }}</span>
                        </div>
                    </div>

                    
                    <div class="flex flex-col justify-between flex-1 gap-3 p-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-extrabold leading-tight uppercase cursor-pointer text-foreground line-clamp-1 hover:text-emerald-600"
                                @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                                {{ $movie->judul }}
                            </h3>
                            <p class="text-[9px] font-bold text-foreground/50 uppercase tracking-wider">{{ $movie->genre }}</p>
                            <span class="px-2.5 py-1 bg-[#F3F4F6] border-2 border-border rounded-lg text-[9px] font-extrabold">
                                {{ $movie->durasi_menit }}m | {{ $movie->rating_usia }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-border/10">
                            <a href="{{ route('film.show', $movie->id) }}"
                                class="w-full px-3 font-semibold text-sm py-2 bg-accent-green text-border border-2 border-border rounded-lg flex items-center justify-center hover:scale-105 transition-transform shadow-[1.5px_1.5px_0px_var(--border)] focus:outline-none gap-1">
                                <x-icon name="heroicon-s-ticket" class="w-4 h-4 text-border" />
                                <span>
                                    Pesan Tiket
                                </span>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            
            <div class="flex gap-4 pb-4 overflow-x-auto md:hidden snap-x hide-scrollbar">
                @foreach($nowShowing as $movie)
                <article x-show="matchesSearch('{{ addslashes($movie->judul) }}', '{{ addslashes($movie->genre) }}', '{{ addslashes($movie->sinopsis) }}')"
                    class="min-w-[200px] max-w-[200px] border-[3px] border-border rounded-[20px] overflow-hidden bg-white shadow-[4px_4px_0px_var(--border)] flex flex-col justify-between snap-start">
                    <div class="relative aspect-[2/3] overflow-hidden border-b-[3px] border-border bg-slate-100"
                        @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->judul }}" class="object-cover w-full h-full" />
                        <div class="absolute top-2 right-2 px-2 py-1 bg-white border-2 border-border rounded-lg text-[9px] font-extrabold flex items-center gap-0.5 shadow-sm">
                            <x-icon name="heroicon-s-star" class="w-3 h-3 fill-current text-accent-yellow" />
                            <span>{{ number_format($movie->rating, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col justify-between flex-1 gap-2 p-3">
                        <div>
                            <h3 class="text-xs font-extrabold leading-tight uppercase text-foreground line-clamp-1"
                                @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                                {{ $movie->judul }}
                            </h3>
                            <p class="text-[8px] font-bold text-foreground/50 uppercase tracking-wider truncate mt-0.5">{{ $movie->genre }}</p>
                        </div>
                        <div class="flex justify-between items-center pt-1.5 border-t border-border/10">
                            <span class="px-2 py-0.5 bg-[#F3F4F6] border-2 border-border rounded-lg text-[8px] font-extrabold">{{ $movie->durasi_menit }}m</span>
                            <a href="{{ route('film.show', $movie->id) }}"
                                class="flex items-center justify-center border-2 rounded-full shadow-sm w-7 h-7 bg-accent-green text-border border-border">
                                <x-icon name="heroicon-s-plus" class="w-3.5 h-3.5 text-border" />
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>

        
        <section x-show="hasAnyMovies() && (activeTab === 'Semua' || activeTab === 'Akan Tayang')" class="space-y-6">
            <div class="flex items-center gap-2 pb-2">
                <h2 class="text-2xl font-black tracking-tight uppercase text-foreground">AKAN TAYANG</h2>
                <span class="px-3 py-1 bg-pastel-sky border-2 border-border rounded-full text-[9px] font-extrabold text-foreground uppercase tracking-wider shadow-sm">UPCOMING</span>
            </div>

            
            
            <div class="hidden grid-cols-2 gap-6 md:grid md:grid-cols-3 lg:grid-cols-4">
                @foreach($comingSoon as $movie)
                <article x-show="matchesSearch('{{ addslashes($movie->judul) }}', '{{ addslashes($movie->genre) }}', '{{ addslashes($movie->sinopsis) }}')"
                    class="border-[3px] border-border rounded-[20px] overflow-hidden bg-white shadow-[4px_4px_0px_var(--border)] flex flex-col justify-between group transition-all duration-300">

                    
                    <div class="relative aspect-[2/3] overflow-hidden border-b-[3px] border-border bg-slate-100 cursor-pointer"
                        @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->judul }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />

                        
                        <div class="absolute top-3 left-3 px-2 py-1 bg-pastel-lavender border-2 border-border rounded-lg text-[8px] font-extrabold shadow-[1.5px_1.5px_0px_var(--border)]">
                            <span>AKAN TAYANG</span>
                        </div>
                    </div>

                    
                    <div class="flex flex-col justify-between flex-1 gap-3 p-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-extrabold leading-tight uppercase cursor-pointer text-foreground line-clamp-1 hover:text-emerald-600"
                                @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                                {{ $movie->judul }}
                            </h3>
                            <p class="text-[9px] font-bold text-foreground/50 uppercase tracking-wider">{{ $movie->genre }}</p>
                            <p class="text-[9px] font-bold text-emerald-600 flex items-center gap-1 mt-1">
                                <x-icon name="heroicon-s-calendar" class="w-3.5 h-3.5" />
                                <span>Rilis: {{ $movie->tanggal_rilis?->format('d F Y') ?? 'Segera' }}</span>
                            </p>
                        </div>

                        <div class="pt-2 border-t border-border/10">
                            <button @click="triggerToast('Sukses! Pengingat untuk {{ addslashes($movie->judul) }} telah diaktifkan.')"
                                class="w-full flex items-center justify-center gap-1.5 py-2.5 bg-white hover:bg-slate-50 border-2 border-border rounded-xl text-xs font-bold text-foreground shadow-[2px_2px_0px_var(--border)] active:translate-y-[1px] active:shadow-none transition-all">
                                <x-icon name="heroicon-s-bell" class="w-4 h-4 text-border" />
                                <span>Ingatkan Saya</span>
                            </button>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            
            <div class="flex gap-4 pb-4 overflow-x-auto md:hidden snap-x hide-scrollbar">
                @foreach($comingSoon as $movie)
                <article x-show="matchesSearch('{{ addslashes($movie->judul) }}', '{{ addslashes($movie->genre) }}', '{{ addslashes($movie->sinopsis) }}')"
                    class="min-w-[200px] max-w-[200px] border-[3px] border-border rounded-[20px] overflow-hidden bg-white shadow-[4px_4px_0px_var(--border)] flex flex-col justify-between snap-start">
                    <div class="relative aspect-[2/3] overflow-hidden border-b-[3px] border-border bg-slate-100"
                        @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->judul }}" class="object-cover w-full h-full" />
                        <div class="absolute top-2 left-2 px-2 py-0.5 bg-pastel-lavender border border-border rounded text-[7px] font-extrabold">
                            <span>AKAN TAYANG</span>
                        </div>
                    </div>
                    <div class="flex flex-col justify-between flex-1 gap-2 p-3">
                        <div>
                            <h3 class="text-xs font-extrabold leading-tight uppercase text-foreground line-clamp-1"
                                @click="window.location.href = '{{ route('film.show', $movie->id) }}'">
                                {{ $movie->judul }}
                            </h3>
                            <p class="text-[8px] font-bold text-foreground/50 uppercase tracking-wider truncate mt-0.5">{{ $movie->genre }}</p>
                            <p class="text-[8px] font-bold text-emerald-600 truncate mt-0.5">Rilis: {{ $movie->tanggal_rilis?->format('d M Y') ?? 'Segera' }}</p>
                        </div>
                        <div class="pt-1.5 border-t border-border/10">
                            <button @click="triggerToast('Sukses! Pengingat untuk {{ addslashes($movie->judul) }} telah diaktifkan.')"
                                class="w-full flex items-center justify-center gap-1 py-1.5 bg-white border border-border rounded-lg text-[9px] font-bold text-foreground shadow-sm">
                                <x-icon name="heroicon-s-bell" class="w-3.5 h-3.5 text-border" />
                                <span>Ingatkan</span>
                            </button>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>

        
        <section class="grid grid-cols-1 gap-6 pt-4 md:grid-cols-2">
            <div class="border-[3px] border-border rounded-[24px] bg-pastel-lemon p-6 flex items-center gap-4 shadow-[4px_4px_0px_var(--border)] relative">
                <div class="w-14 h-14 bg-white border-[3px] border-border rounded-xl flex items-center justify-center text-border shadow-[3px_3px_0px_rgba(0,0,0,1)] shrink-0 select-none">
                    <x-icon name="heroicon-s-shopping-bag" class="w-7 h-7 text-border" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-extrabold leading-tight uppercase text-foreground">PROMO POPCORN!</h3>
                    <p class="text-[11px] font-medium text-foreground/75 mt-1 leading-normal">Beli 2 tiket, dapat 1 popcorn medium gratis. Khusus hari ini!</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="font-mono text-xs font-bold bg-white/70 px-2 py-0.5 rounded border border-border/20">POPGRATIS</span>
                        <button @click="navigator.clipboard.writeText('POPGRATIS'); triggerToast('Kode promo POPGRATIS disalin!')"
                            class="px-3 py-1 bg-white border-2 border-border rounded-lg text-[9px] font-extrabold hover:bg-slate-50 shadow-[1px_1px_0px_var(--border)] active:translate-y-[0.5px]">
                            Salin
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-[3px] border-border rounded-[24px] bg-pastel-peach p-6 flex items-center gap-4 shadow-[4px_4px_0px_var(--border)] relative">
                <div class="w-14 h-14 bg-white border-[3px] border-border rounded-xl flex items-center justify-center text-border shadow-[3px_3px_0px_rgba(0,0,0,1)] shrink-0 select-none">
                    <x-icon name="heroicon-s-credit-card" class="w-7 h-7 text-border" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-extrabold leading-tight uppercase text-foreground">CASHBACK JAGO</h3>
                    <p class="text-[11px] font-medium text-foreground/75 mt-1 leading-normal">Nikmati cashback hingga 30% untuk pembayaran via QRIS Bank Jago.</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="font-mono text-xs font-bold bg-white/70 px-2 py-0.5 rounded border border-border/20">JAGOTIKET</span>
                        <button @click="navigator.clipboard.writeText('JAGOTIKET'); triggerToast('Kode promo JAGOTIKET disalin!')"
                            class="px-3 py-1 bg-white border-2 border-border rounded-lg text-[9px] font-extrabold hover:bg-slate-50 shadow-[1px_1px_0px_var(--border)] active:translate-y-[0.5px]">
                            Salin
                        </button>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-app-layout>