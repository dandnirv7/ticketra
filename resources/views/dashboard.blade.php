<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda &middot; Ticketra.</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&family=Archivo:wght@400;500;600;700&family=Space+Grotesk:wght@500;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
$avatarInitial = strtoupper(substr($user->name ?? 'A', 0, 1));
$hasBio = $bioskopFavorit !== null;
$bottomActive = 'flex flex-col items-center justify-center gap-1 text-[10px] font-black text-black bg-pastel-mint border-[3px] border-black rounded-xl p-1.5 shadow-[2px_2px_0px_rgba(0,0,0,1)] transition-all';
$bottomInactive = 'flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-gray-700 hover:text-black border-[3px] border-transparent p-1.5 transition-all';
@endphp

<body class="min-h-screen font-base"
    x-data="{
          heroSlide: 0,
          heroMax: {{ count($heroFilms) }},
          heroFilms: {{ json_encode($heroFilms) }},
          movies: {{ json_encode($nowShowing) }},
          searchQuery: new URLSearchParams(window.location.search).get('q') || '',
          activeGenre: new URLSearchParams(window.location.search).get('genre') || 'All',
          activeSort: new URLSearchParams(window.location.search).get('sort') || 'populer',
          selectedLocation: new URLSearchParams(window.location.search).get('location') || 'Jakarta',
          showLocationDropdown: false,
          heroNext() { this.heroSlide = (this.heroSlide + 1) % this.heroMax; },
          heroPrev() { this.heroSlide = (this.heroSlide - 1 + this.heroMax) % this.heroMax; },
          heroGo(i) { this.heroSlide = i; },
          autoHero: null,
          init() {
              this.autoHero = setInterval(() => this.heroNext(), 5000);

              this.$watch('searchQuery', (val) => {
                  const url = new URL(window.location);
                  if (!val.trim()) url.searchParams.delete('q');
                  else url.searchParams.set('q', val.trim());
                  window.history.replaceState({}, '', url);
              });

              this.$watch('activeGenre', (val) => {
                  const url = new URL(window.location);
                  if (val === 'All') url.searchParams.delete('genre');
                  else url.searchParams.set('genre', val);
                  window.history.replaceState({}, '', url);
              });

              this.$watch('activeSort', (val) => {
                  const url = new URL(window.location);
                  if (val === 'populer') url.searchParams.delete('sort');
                  else url.searchParams.set('sort', val);
                  window.history.replaceState({}, '', url);
              });

              this.$watch('selectedLocation', (val) => {
                  const url = new URL(window.location);
                  if (val === 'Jakarta') url.searchParams.delete('location');
                  else url.searchParams.set('location', val);
                  window.history.replaceState({}, '', url);
              });
          },
          pauseHero() { if (this.autoHero) clearInterval(this.autoHero); },
          filteredMovies() {
              let list = [...this.movies];
              if (this.searchQuery.trim() !== '') {
                  const q = this.searchQuery.toLowerCase();
                  list = list.filter(m => m.title.toLowerCase().includes(q) || m.genre.toLowerCase().includes(q) || m.synopsis.toLowerCase().includes(q));
              }
              if (this.activeGenre !== 'All') {
                  list = list.filter(m => m.genre.toLowerCase().includes(this.activeGenre.toLowerCase()));
              }
              if (this.activeSort === 'rating') {
                  list.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
              } else if (this.activeSort === 'terbaru') {
                  list.sort((a, b) => (b.tanggal_rilis ?? '').localeCompare(a.tanggal_rilis ?? '') || b.id.localeCompare(a.id));
              } else if (this.activeSort === 'populer') {
                  list.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
              }
              return list;
          }
      }">

    <div class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 lg:p-8 min-h-screen max-w-[1600px] mx-auto">

        <aside class="hidden lg:block lg:w-64 lg:shrink-0">
            <div class="lg:sticky lg:top-8 border-[3px] border-border rounded-[24px] bg-secondary-background p-5 space-y-6 shadow-[6px_6px_0px_rgba(0,0,0,1)]">

                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-2xl font-extrabold tracking-tight font-heading">
                    <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-accent-green" />
                    Ticketra<span class="text-accent-green">.</span>
                </a>

                <nav class="flex flex-col gap-1.5 text-sm font-bold tracking-wide">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 bg-pastel-mint border-[3px] border-border rounded-xl font-bold shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-home" class="w-5 h-5 text-border" />
                        Beranda
                    </a>
                    <a href="{{ route('film.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-film" class="w-5 h-5 text-border" />
                        Film
                    </a>
                    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-ticket" class="w-5 h-5 text-border" />
                        Tiket Saya
                    </a>
                    <a href="{{ route('snacks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-shopping-bag" class="w-5 h-5 text-border" />
                        Snack Bar
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-heart" class="w-5 h-5 text-border" />
                        Wishlist
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-user" class="w-5 h-5 text-border" />
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-red-50 hover:border-accent-red hover:text-accent-red hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none text-left font-bold">
                            <x-icon name="heroicon-s-arrow-left-on-rectangle" class="w-5 h-5" />
                            Keluar
                        </button>
                    </form>
                </nav>

                <div class="border-[3px] border-border rounded-[20px] bg-pastel-lavender p-4 relative overflow-hidden shadow-[3px_3px_0px_var(--border)]">
                    <div class="pr-12">
                        <p class="text-sm font-extrabold text-foreground">Hi, {{ explode(' ', $user->name ?? 'Andi')[0] }}!</p>
                        <p class="text-[11px] font-medium text-foreground/80 mt-1 leading-snug">Selamat datang di Ticketra.</p>
                    </div>
                    <x-icon name="heroicon-s-ticket" class="absolute w-8 h-8 transform right-2 bottom-2 text-border/25 rotate-12" />
                </div>
            </div>
        </aside>

        <div class="flex flex-col flex-1 min-w-0 gap-4">

            <header class="flex items-center justify-between gap-3 px-1 py-2">
                <button @click="$dispatch('toggle-mobile-menu')"
                        class="lg:hidden w-10 h-10 bg-secondary-background border-[3px] border-border rounded-xl flex items-center justify-center hover:bg-pastel-lemon/20 transition-colors shrink-0">
                    <x-icon name="heroicon-s-bars-3" class="w-5 h-5 text-border" />
                </button>

                <div class="flex-1">
                    <h2 class="text-xl font-black font-heading text-foreground lg:hidden">Ticketra.</h2>
                </div>

                <div class="flex items-center justify-end gap-3 shrink-0">
                    <div class="relative">
                        <button @click="showLocationDropdown = !showLocationDropdown" @click.away="showLocationDropdown = false"
                            class="flex items-center gap-2 px-4 py-2.5 bg-secondary-background border-[3px] border-border rounded-xl text-xs font-bold whitespace-nowrap shadow-none hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5 hover:bg-pastel-lemon/20 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                            <x-icon name="heroicon-s-map-pin" class="w-4 h-4 text-border" />
                            <span x-text="selectedLocation">Jakarta</span>
                            <x-icon name="heroicon-s-chevron-down" class="w-3 h-3 text-border" />
                        </button>
                        <div x-show="showLocationDropdown" x-transition.opacity
                            class="absolute right-0 mt-2 w-44 bg-white border-[3px] border-border rounded-xl shadow-[4px_4px_0px_var(--border)] z-50 py-1 text-xs font-bold text-foreground">
                            <template x-for="loc in ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi']">
                                <button @click="selectedLocation = loc; showLocationDropdown = false"
                                    class="px-4 py-2.5 w-full text-left border-b-2 transition-colors hover:bg-pastel-mint/30 border-border/10 last:border-b-0"
                                    x-text="loc">
                                </button>
                            </template>
                        </div>
                    </div>

                    <livewire:notifications-dropdown />

                    <a href="{{ route('profile.edit') }}" class="hidden lg:flex w-11 h-11 bg-accent-yellow border-[3px] border-border rounded-full items-center justify-center text-xl hover:scale-105 transition-transform focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 select-none">
                        {{ $user->avatar_emoji ?? '🍿' }}
                    </a>
                </div>
            </header>

            <div class="flex-1 border-[3px] border-border rounded-[24px] bg-slate-50 p-6 md:p-8 space-y-6 shadow-[8px_8px_0px_var(--border)]">

                <section class="brutal-card bg-pastel-lavender p-6 md:p-8 overflow-hidden relative border-[3px] border-border rounded-[20px] shadow-[4px_4px_0px_var(--border)]">
                    <div class="flex flex-col items-stretch gap-6 lg:grid lg:grid-cols-2">
                        <!-- 1. Trending Now -->
                        <div class="relative z-10 lg:col-start-1 lg:row-start-1">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-border text-white rounded-full text-[9px] font-extrabold uppercase tracking-wider">
                                <x-icon name="heroicon-s-fire" class="w-3 h-3 fill-current text-accent-red" /> Trending Now
                            </span>
                        </div>

                        <!-- 2. Carousel Hero Image -->
                        <div class="relative h-64 md:h-80 lg:h-96 rounded-2xl overflow-hidden border-2 border-border shadow-[8px_8px_0px_0px_var(--border)] bg-foreground/10 lg:col-start-2 lg:row-start-1 lg:row-span-2">
                            @foreach ($heroFilms as $i => $film)
                            <div x-show="heroSlide === {{ $i }}" x-transition.opacity.duration.500ms
                                class="absolute inset-0"
                                style="display: {{ $i === 0 ? 'block' : 'none' }};">
                                <img src="{{ $film['poster'] }}" alt="{{ $film['title'] }}" class="object-cover w-full h-full" />
                            </div>
                            @endforeach

                            <div class="absolute top-3 right-3 px-3 py-1 bg-border/80 text-white rounded-full text-[10px] font-extrabold tracking-wider">
                                <span x-text="(heroSlide + 1) + ' / ' + heroMax"></span>
                            </div>

                            <div class="flex absolute bottom-3 left-1/2 gap-1.5 items-center -translate-x-1/2">
                                @for ($i = 0; $i < count($heroFilms); $i++)
                                    <button @click="heroGo({{ $i }})" :class="heroSlide === {{ $i }} ? 'w-6 bg-border' : 'w-2 bg-border/40 hover:bg-border/70'" class="h-2 transition-all rounded-full focus:outline-none"></button>
                                    @endfor
                            </div>
                        </div>

                        <!-- 3, 4, 5. Title, Synopsis, Action Buttons -->
                        <div class="relative z-10 flex flex-col justify-center space-y-4 md:space-y-5 lg:col-start-1 lg:row-start-2">
                            <h1 class="text-4xl md:text-5xl font-heading font-black leading-[0.95] uppercase tracking-tight max-w-lg">
                                <span x-text="heroFilms[heroSlide]?.title ?? ''"></span>
                            </h1>
                            <p class="max-w-md text-sm font-medium leading-relaxed text-foreground/80" x-text="truncateWords(heroFilms[heroSlide]?.synopsis ?? '', 40)"></p>
                            <div class="flex flex-wrap gap-3 pt-1">
                                <a :href="'/film/' + heroFilms[heroSlide]?.id" class="brutal-btn bg-accent-yellow !py-3 !px-6 text-xs tracking-wider shadow-neo-sm border-2 border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                                    <x-icon name="heroicon-s-ticket" class="w-4 h-4 text-border" />
                                    Pesan Tiket
                                </a>
                                <a :href="'/film/' + heroFilms[heroSlide]?.id + '#trailer-section'" class="brutal-btn bg-white hover:bg-pastel-sky/20 text-border !py-3 !px-6 text-xs tracking-wider shadow-neo-sm border-2 border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                                    <x-icon name="heroicon-s-play" class="w-4 h-4 fill-current text-border" />
                                    Tonton Trailer
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <a href="#" class="border-[3px] border-border rounded-[20px] bg-pastel-mint p-5 flex flex-col justify-between shadow-[4px_4px_0px_var(--border)] transition-transform hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_var(--border)] focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 group">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-extrabold text-foreground">Bioskop</h3>
                                <p class="text-[11px] font-medium text-foreground/70 mt-1 leading-snug">Lihat bioskop favoritmu</p>
                            </div>
                            <div class="w-10 h-10 bg-[#E2F5EC] rounded-lg flex items-center justify-center text-accent-green shrink-0">
                                <x-icon name="heroicon-s-building-storefront" class="w-7 h-7 text-border" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <span class="w-7 h-7 bg-white border-2 border-border rounded-full flex items-center justify-center group-hover:bg-border group-hover:text-white transition-all shadow-[1px_1px_0px_var(--border)]">
                                <x-icon name="heroicon-s-arrow-right" class="w-3.5 h-3.5 text-border group-hover:text-white" />
                            </span>
                        </div>
                    </a>

                    <a href="{{ route('bookings.index') }}" class="border-[3px] border-border rounded-[20px] bg-pastel-lemon p-5 flex flex-col justify-between shadow-[4px_4px_0px_var(--border)] transition-transform hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_var(--border)] focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 group">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-extrabold text-foreground">Tiket Saya</h3>
                                <p class="text-[11px] font-medium text-foreground/70 mt-1 leading-snug">Cek tiket & riwayat kamu</p>
                            </div>
                            <div class="w-10 h-10 bg-[#FFFBEA] rounded-lg flex items-center justify-center text-accent-yellow shrink-0">
                                <x-icon name="heroicon-s-ticket" class="w-7 h-7 text-border" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <span class="w-7 h-7 bg-white border-2 border-border rounded-full flex items-center justify-center group-hover:bg-border group-hover:text-white transition-all shadow-[1px_1px_0px_var(--border)]">
                                <x-icon name="heroicon-s-arrow-right" class="w-3.5 h-3.5 text-border group-hover:text-white" />
                            </span>
                        </div>
                    </a>

                    <a href="#" class="border-[3px] border-border rounded-[20px] bg-pastel-pink p-5 flex flex-col justify-between shadow-[4px_4px_0px_var(--border)] transition-transform hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_var(--border)] focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 group">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-extrabold text-foreground">Promo</h3>
                                <p class="text-[11px] font-medium text-foreground/70 mt-1 leading-snug">Temukan promo menarik hari ini</p>
                            </div>
                            <div class="w-10 h-10 bg-[#FFF0F5] rounded-lg flex items-center justify-center text-[#ec4899] shrink-0">
                                <x-icon name="heroicon-s-tag" class="w-7 h-7 text-border" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <span class="w-7 h-7 bg-white border-2 border-border rounded-full flex items-center justify-center group-hover:bg-border group-hover:text-white transition-all shadow-[1px_1px_0px_var(--border)]">
                                <x-icon name="heroicon-s-arrow-right" class="w-3.5 h-3.5 text-border group-hover:text-white" />
                            </span>
                        </div>
                    </a>

                    <a href="#" class="border-[3px] border-border rounded-[20px] bg-[#E6F3FF] p-5 flex flex-col justify-between shadow-[4px_4px_0px_var(--border)] transition-transform hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_var(--border)] focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 group">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-extrabold text-foreground">Snack Bar</h3>
                                <p class="text-[11px] font-medium text-foreground/70 mt-1 leading-snug">Pesan camilan sebelum nonton</p>
                            </div>
                            <div class="w-10 h-10 bg-[#E6F3FF] rounded-lg flex items-center justify-center text-main shrink-0">
                                <x-icon name="heroicon-s-shopping-bag" class="w-7 h-7 text-border" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <span class="w-7 h-7 bg-white border-2 border-border rounded-full flex items-center justify-center group-hover:bg-border group-hover:text-white transition-all shadow-[1px_1px_0px_var(--border)]">
                                <x-icon name="heroicon-s-arrow-right" class="w-3.5 h-3.5 text-border group-hover:text-white" />
                            </span>
                        </div>
                    </a>
                </section>

                <section class="border-[3px] border-border rounded-[20px] bg-white p-5 md:p-6 shadow-[4px_4px_0px_var(--border)] space-y-6">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <h2 class="flex items-center gap-2 text-xl font-extrabold tracking-wide uppercase md:text-2xl text-foreground">
                            Sedang Tayang
                            <span class="w-2.5 h-2.5 rounded-full border animate-pulse bg-accent-red border-border"></span>
                        </h2>
                        <div class="relative w-full max-w-md">
                            <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Cari film, bioskop, atau promo..." class="w-full bg-secondary-background border-[3px] border-border rounded-full pl-5 pr-12 py-3 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2" />
                            <div class="absolute -translate-y-1/2 right-4 top-1/2 text-foreground">
                                <x-icon name="heroicon-s-magnifying-glass" class="w-5 h-5 text-border" />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b-[3px] border-border/10 pb-4">

                        <div class="flex flex-wrap gap-2">
                            <template x-for="genreObj in [
                                { label: 'Semua', value: 'All' },
                                { label: 'Aksi', value: 'Action' },
                                { label: 'Komedi', value: 'Comedy' },
                                { label: 'Horor', value: 'Horror' },
                                { label: 'Drama', value: 'Drama' },
                                { label: 'Romantis', value: 'Romance' },
                                { label: 'Sci-Fi', value: 'Sci-Fi' }
                            ]" :key="genreObj.value">
                                <button @click="activeGenre = genreObj.value"
                                    :class="activeGenre === genreObj.value ? 'bg-accent-green text-border border-border shadow-none translate-x-[2px] translate-y-[2px]' : 'bg-white text-border hover:bg-slate-50 hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5'"
                                    class="px-3.5 py-1.5 rounded-xl border-2 border-border text-[10px] font-extrabold uppercase tracking-wide transition-all shadow-[2px_2px_0px_var(--border)] active:translate-y-[1px] active:shadow-none focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2"
                                    x-text="genreObj.label">
                                </button>
                            </template>
                        </div>

                        <div class="flex items-center gap-2" x-data="{ showSortDropdown: false }">
                            <span class="text-xs font-black text-foreground">Urutkan:</span>
                            <div class="relative">
                                <button @click="showSortDropdown = !showSortDropdown" @click.away="showSortDropdown = false"
                                    class="flex items-center gap-2 px-4 py-2.5 bg-white border-[3px] border-border rounded-xl text-xs font-bold whitespace-nowrap shadow-none hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5 hover:bg-pastel-lemon/20 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                                    <span x-text="activeSort === 'populer' ? 'Terpopuler' : (activeSort === 'rating' ? 'Rating Tertinggi' : 'Terbaru')">Terpopuler</span>
                                    <x-icon name="heroicon-s-chevron-down" class="w-3.5 h-3.5 text-border" />
                                </button>
                                <div x-show="showSortDropdown" x-transition.opacity
                                    class="absolute right-0 mt-2 w-44 bg-white border-[3px] border-border rounded-xl shadow-[4px_4px_0px_var(--border)] z-50 py-1 text-xs font-bold text-foreground">
                                    <button @click="activeSort = 'populer'; showSortDropdown = false" class="px-4 py-2.5 w-full text-left border-b-2 transition-colors hover:bg-pastel-mint/30 border-border/10">Terpopuler</button>
                                    <button @click="activeSort = 'rating'; showSortDropdown = false" class="px-4 py-2.5 w-full text-left border-b-2 transition-colors hover:bg-pastel-mint/30 border-border/10">Rating Tertinggi</button>
                                    <button @click="activeSort = 'terbaru'; showSortDropdown = false" class="px-4 py-2.5 w-full text-left transition-colors hover:bg-pastel-mint/30">Terbaru</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="filteredMovies().length === 0" class="border-[3px] border-border border-dashed rounded-[20px] p-12 text-center text-foreground/50 font-bold">
                        Tidak ada film yang cocok dengan pencarian atau filter Anda.
                    </div>

                    <div x-show="filteredMovies().length > 0" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                            <template x-if="filteredMovies().length > 0">
                                <div class="md:col-span-2 border-[3px] border-border rounded-[20px] relative overflow-hidden min-h-[320px] shadow-[4px_4px_0px_var(--border)] group flex flex-col justify-between">
                                    <img :src="filteredMovies()[0].poster" :alt="filteredMovies()[0].title" class="absolute inset-0 object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent via-black/40"></div>

                                    <div class="relative z-10 flex flex-col justify-between flex-1 h-full p-5 md:p-6">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 bg-pastel-pink text-border border-2 border-border rounded-full text-[9px] font-extrabold uppercase tracking-wide">Terlaris</span>
                                            <span class="px-2.5 py-1 bg-accent-yellow text-border border-2 border-border rounded-lg text-[9px] font-extrabold" x-text="'★ ' + filteredMovies()[0].rating"></span>
                                            <span class="px-2 py-1 bg-slate-200 text-border border-2 border-border rounded-lg text-[9px] font-extrabold tracking-wider">IMAX</span>
                                        </div>

                                        <div class="mt-12 space-y-2">
                                            <h3 class="text-xl font-black leading-tight tracking-tight text-white uppercase md:text-2xl" x-text="filteredMovies()[0].title"></h3>
                                            <p class="text-xs font-semibold leading-snug text-white/90 line-clamp-2" x-text="filteredMovies()[0].synopsis"></p>
                                        </div>

                                        <div class="flex items-center justify-between mt-4">
                                            <span class="px-3 py-1 bg-white text-border border-2 border-border rounded-lg text-[10px] font-extrabold" x-text="filteredMovies()[0].duration"></span>
                                            <a :href="'/film/' + filteredMovies()[0].id" class="brutal-btn bg-accent-green text-border !py-2.5 !px-5 text-xs shadow-[2px_2px_0px_var(--border)] border-2 border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                                                Beli Tiket
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="flex gap-4 overflow-x-auto snap-x scroll-smooth pb-4 md:pb-0 md:contents hide-scrollbar">
                                <template x-for="film in filteredMovies().slice(1, 7)" :key="film.id">
                                    <div class="border-[3px] border-border rounded-[20px] overflow-hidden bg-white shadow-[4px_4px_0px_var(--border)] flex flex-col justify-between group min-w-[240px] md:min-w-0 snap-start">
                                        <div class="relative aspect-[4/5] overflow-hidden border-b-[3px] border-border bg-slate-50">
                                            <img :src="film.poster" :alt="film.title" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
                                            <div class="absolute top-2 right-2 px-2 py-1 bg-white border-2 border-border rounded-lg text-[9px] font-extrabold flex items-center gap-0.5 shadow-[1px_1px_0px_var(--border)]">
                                                <x-icon name="heroicon-s-star" class="w-3 h-3 fill-current text-accent-yellow" />
                                                <span x-text="film.rating"></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col justify-between flex-1 gap-2 p-4">
                                            <div>
                                                <h3 class="text-sm font-extrabold leading-tight uppercase text-foreground line-clamp-1" :title="film.title" x-text="film.title"></h3>
                                                <p class="text-[9px] font-bold text-foreground/50 uppercase tracking-wider mt-1" x-text="film.genre"></p>
                                            </div>
                                            <div class="flex items-center justify-between pt-2">
                                                <span class="px-2 py-0.5 bg-[#F3F4F6] border-2 border-border rounded-lg text-[9px] font-extrabold" x-text="film.duration"></span>
                                                <a :href="'/film/' + film.id" class="flex gap-0.5 items-center text-xs font-black text-emerald-600 rounded transition-colors hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-border hover:underline">
                                                    Beli Tiket <span class="font-bold">&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div class="border-[3px] border-border rounded-[20px] bg-pastel-lemon p-5 md:p-6 flex items-center gap-4 shadow-[4px_4px_0px_var(--border)]">
                        <div class="w-14 h-14 bg-white border-[3px] border-border rounded-xl flex items-center justify-center text-border shadow-[3px_3px_0px_rgba(0,0,0,1)] shrink-0 select-none">
                            <x-icon name="heroicon-s-shopping-bag" class="w-7 h-7 text-border" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-extrabold leading-tight uppercase text-foreground">Promo Popcorn!</h3>
                            <p class="text-[11px] font-medium text-foreground/75 mt-1 leading-normal">Beli 2 tiket, dapat 1 popcorn medium gratis. Khusus hari ini!</p>
                        </div>
                        <a href="#" class="px-4 py-2.5 bg-white border-2 border-border rounded-xl text-[10px] font-extrabold text-foreground hover:bg-slate-50 shadow-[2px_2px_0px_var(--border)] whitespace-nowrap active:translate-y-[1px] active:shadow-none transition-all flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-border">
                            Klaim Promo <span class="font-bold">&gt;</span>
                        </a>
                    </div>

                    <div class="border-[3px] border-border rounded-[20px] bg-pastel-peach p-5 md:p-6 flex items-center gap-4 shadow-[4px_4px_0px_var(--border)]">
                        <div class="w-14 h-14 bg-white border-[3px] border-border rounded-xl flex items-center justify-center text-border shadow-[3px_3px_0px_rgba(0,0,0,1)] shrink-0 select-none">
                            <x-icon name="heroicon-s-credit-card" class="w-7 h-7 text-border" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-extrabold leading-tight uppercase text-foreground">Cashback Jago</h3>
                            <p class="text-[11px] font-medium text-foreground/75 mt-1 leading-normal">Nikmati cashback hingga 30% untuk pembayaran via QRIS.</p>
                        </div>
                        <a href="#" class="px-4 py-2.5 bg-white border-2 border-border rounded-xl text-[10px] font-extrabold text-foreground hover:bg-slate-50 shadow-[2px_2px_0px_var(--border)] whitespace-nowrap active:translate-y-[1px] active:shadow-none transition-all flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-border">
                            Selengkapnya <span class="font-bold">&gt;</span>
                        </a>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <div class="space-y-6">

                        <div class="border-[3px] border-border rounded-[20px] bg-white p-5 md:p-6 shadow-[4px_4px_0px_var(--border)]">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h2 class="flex items-center gap-2 text-lg font-extrabold md:text-xl text-foreground">
                                        Snack Bar
                                    </h2>
                                    <p class="text-[11px] font-medium text-foreground/60 mt-1">Pre-order sekarang, langsung ambil tanpa antre!</p>
                                </div>
                                <a href="#" class="text-xs font-bold underline decoration-2 hover:text-main">Lihat Semua</a>
                            </div>

                            <div class="flex gap-4 overflow-x-auto snap-x scroll-smooth pb-4 sm:grid sm:grid-cols-3 sm:pb-0 hide-scrollbar">
                                @foreach (array_slice($snacks, 0, 3) as $snack)
                                <div class="border-[3px] border-border rounded-xl bg-white p-3 flex flex-col justify-between shadow-[2px_2px_0px_var(--border)] relative min-w-[140px] sm:min-w-0 snap-start">
                                    <div>
                                        <div class="aspect-square bg-[#E6F3FF] border-2 border-border rounded-lg flex items-center justify-center text-4xl mb-2 select-none">
                                            {{ $snack['emoji'] }}
                                        </div>
                                        <p class="text-[10px] font-black uppercase leading-tight line-clamp-2 min-h-[28px] text-foreground">{{ $snack['name'] }}</p>
                                        <p class="mt-1 text-xs font-extrabold text-accent-red">Rp {{ number_format($snack['price'], 0, ',', '.') }}</p>
                                    </div>
                                    <button class="w-7 h-7 bg-accent-green text-border border-2 border-border rounded-full flex items-center justify-center mt-3 self-end hover:scale-105 shadow-[1px_1px_0px_var(--border)] active:translate-y-[1px] active:shadow-none transition-all focus:outline-none">
                                        <x-icon name="heroicon-s-plus" class="w-4 h-4 text-border" />
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-[3px] border-border rounded-[20px] bg-white p-5 md:p-6 shadow-[4px_4px_0px_var(--border)] space-y-4">
                            <h2 class="text-lg font-extrabold md:text-xl text-foreground">Bioskop Favorit</h2>
                            @if($bioskopFavorit)
                            <div class="flex flex-col items-stretch gap-5 md:flex-row">
                                <div class="flex-1 border-[3px] border-border rounded-xl bg-pastel-pink/10 p-4 space-y-3 shadow-[2px_2px_0px_var(--border)] flex flex-col justify-between">
                                    <div class="space-y-1.5">
                                        <p class="text-base font-extrabold leading-tight text-foreground">{{ $bioskopFavorit->nama }}</p>
                                        <p class="text-[11px] font-bold text-foreground/60 flex items-center gap-1">
                                            <x-icon name="heroicon-s-map-pin" class="w-3.5 h-3.5 text-border" />
                                            {{ $bioskopFavorit->kota }}
                                        </p>
                                    </div>

                                    <div class="text-[10px] font-black uppercase text-foreground/50 tracking-wider">
                                        IMAX &bull; 4DX &bull; Velvet
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-[10px] font-bold text-foreground/50 flex items-center gap-1">📍 2.1 km</span>
                                        <button class="w-8 h-8 bg-white border-2 border-border rounded-full flex items-center justify-center hover:scale-110 shadow-[1px_1px_0px_var(--border)] active:translate-y-[1px] active:shadow-none transition-all">
                                            <x-icon name="heroicon-s-heart" class="w-4 h-4 text-accent-red fill-accent-red" />
                                        </button>
                                    </div>
                                </div>

                                <div class="hidden md:block w-full md:w-1/2 h-40 border-[3px] border-border rounded-xl overflow-hidden shadow-[2px_2px_0px_var(--border)] shrink-0">
                                    <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&q=80&w=400&h=300" alt="Bioskop" class="object-cover w-full h-full" />
                                </div>
                            </div>
                            @else
                            <div class="p-6 text-center border-2 border-dashed border-border/20 rounded-xl">
                                <p class="text-xs font-bold text-gray-400">Belum ada bioskop favorit. Transaksi tiket Anda akan terekam di sini.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-6">

                        <div class="border-[3px] border-border rounded-[20px] bg-white p-5 md:p-6 shadow-[4px_4px_0px_var(--border)] space-y-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-extrabold md:text-xl text-foreground">Akan Tayang</h2>
                                    <p class="text-[11px] font-medium text-foreground/60 mt-1">Jangan lewatkan film yang akan datang</p>
                                </div>
                                <a href="#" class="text-xs font-bold underline decoration-2 hover:text-main">Lihat Semua</a>
                            </div>

                            <div class="space-y-3">
                                @foreach ($comingSoon as $film)
                                <div class="border-[3px] border-border rounded-xl bg-white p-3 flex items-center gap-3 shadow-[2px_2px_0px_var(--border)] transition-all hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_var(--border)]">
                                    <div class="overflow-hidden border-2 rounded-lg w-14 h-18 border-border shrink-0">
                                        <img src="{{ $film['poster'] }}" alt="{{ $film['title'] }}" class="object-cover w-full h-full" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-black leading-tight uppercase text-foreground line-clamp-1">{{ $film['title'] }}</h3>
                                        <p class="text-[9px] font-bold text-foreground/50 uppercase tracking-wider mt-0.5">{{ $film['genre'] }}</p>
                                        <p class="text-[10px] font-extrabold text-emerald-600 mt-1">{{ strtoupper($film['tanggal_rilis'] ?? 'Coming Soon') }}</p>
                                    </div>
                                    <button class="px-3 py-2 bg-white border-2 border-border rounded-xl text-[10px] font-extrabold text-foreground hover:bg-slate-50 shadow-[1px_1px_0px_var(--border)] active:translate-y-[1px] active:shadow-none transition-all flex items-center gap-1.5 whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-border">
                                        <x-icon name="heroicon-s-bell" class="w-3.5 h-3.5 text-border" />
                                        Ingatkan
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-[3px] border-border rounded-[20px] bg-white p-5 md:p-6 shadow-[4px_4px_0px_var(--border)] space-y-4" x-data="{ selectedSeat: '' }">
                            <div class="flex items-start justify-between">
                                <h2 class="text-lg font-extrabold md:text-xl text-foreground">Kursi Favoritmu</h2>
                                <a href="#" class="text-xs font-bold underline decoration-2 hover:text-main">Lihat Semua <span class="font-bold">&gt;</span></a>
                            </div>

                            @if(count($favoriteSeats) > 0)
                            <div class="grid grid-cols-7 gap-2.5">
                                @foreach ($favoriteSeats as $seat)
                                <button @click="selectedSeat = '{{ $seat }}'"
                                    :class="selectedSeat === '{{ $seat }}' ? 'bg-[#86EFAC] text-border border-border shadow-[2px_2px_0px_var(--border)]' : 'bg-white text-foreground hover:bg-[#E6F3FF]/40'"
                                    class="aspect-square border-2 border-border rounded-xl text-[11px] font-black flex items-center justify-center relative transition-all shadow-[1px_1px_0px_var(--border)] active:translate-y-[1px] active:shadow-none focus:outline-none">
                                    {{ $seat }}
                                    @if ($loop->first)
                                    <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-accent-yellow border-2 border-border rounded-full flex items-center justify-center text-[8px] text-foreground shadow-[1px_1px_0px_var(--border)] font-bold">★</span>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                            <p class="text-[10px] font-extrabold text-foreground/50 text-center leading-snug">Baris tengah, posisi pas untuk pengalaman terbaik!</p>
                            @else
                            <div class="p-6 text-center border-2 border-dashed border-border/20 rounded-xl">
                                <p class="text-xs font-bold text-gray-400">Belum ada kursi favorit. Transaksi tiket Anda akan terekam di sini.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                <footer class="hidden md:block border-[3px] border-border rounded-[20px] bg-white p-6 md:p-8 shadow-[4px_4px_0px_var(--border)]">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-12">
                        <div class="space-y-3 md:col-span-5">
                            <div class="flex items-center gap-2 text-2xl font-black tracking-tight font-heading text-foreground">
                                <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-accent-green" />
                                Ticketra<span class="text-accent-green">.</span>
                            </div>
                            <p class="max-w-sm text-xs font-bold leading-relaxed text-foreground/60">Platform pemesanan tiket bioskop masa depan. Bebas antre, banyak promo, kursi pasti dapat.</p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="mb-4 text-xs font-extrabold tracking-wider uppercase text-foreground">Navigasi</h4>
                            <ul class="space-y-2 text-xs font-bold text-foreground/60">
                                <li><a href="{{ route('dashboard') }}" class="hover:text-accent-green hover:underline decoration-2">Beranda</a></li>
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Bioskop</a></li>
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Film</a></li>
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Promo</a></li>
                            </ul>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="mb-4 text-xs font-extrabold tracking-wider uppercase text-foreground">Bantuan</h4>
                            <ul class="space-y-2 text-xs font-bold text-foreground/60">
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Pusat Bantuan</a></li>
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Syarat & Ketentuan</a></li>
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Kebijakan Privasi</a></li>
                                <li><a href="#" class="hover:text-accent-green hover:underline decoration-2">Hubungi Kami</a></li>
                            </ul>
                        </div>
                        <div class="md:col-span-3">
                            <h4 class="mb-4 text-xs font-extrabold tracking-wider uppercase text-foreground">Ikuti Kami</h4>
                            <div class="flex gap-3">
                                <a href="#" class="flex items-center justify-center font-black text-white transition-transform border-2 rounded-full w-9 h-9 bg-border border-border hover:scale-105 focus:outline-none">
                                    f
                                </a>
                                <a href="#" class="flex items-center justify-center font-bold text-white transition-transform border-2 rounded-full w-9 h-9 bg-border border-border hover:scale-105 focus:outline-none">

                                     <x-icon name="heroicon-s-tv" class="w-4 h-4" />
                                </a>
                                <a href="#" class="flex items-center justify-center font-bold text-white transition-transform border-2 rounded-full w-9 h-9 bg-border border-border hover:scale-105 focus:outline-none">
                                    ▶
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 border-t-2 border-border/10 text-center text-[9px] font-black text-foreground/50 tracking-widest uppercase">
                        <p>&copy; {{ date('Y') }} TICKETRA STUDIOS.</p>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <x-mobile-nav :bottomActive="$bottomActive" :bottomInactive="$bottomInactive" />
</body>

<script>
    function truncateWords(text, maxWords = 46) {
        return text
            .trim()
            .split(/\s+/)
            .slice(0, maxWords)
            .join(' ') + (
                text.trim().split(/\s+/).length > maxWords ?
                '...' :
                ''
            );
    }
</script>

</html>
