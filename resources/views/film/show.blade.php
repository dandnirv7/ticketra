@section('title', $film->judul . ' - Detail Film & Jadwal Tayang | Ticketra.')

@section('seo')
@php use Illuminate\Support\Str; @endphp
<meta name="description" content="Nonton {{ $film->judul }} di bioskop. {{ Str::limit($film->sinopsis, 120) }}. Pesan tiket online dan pilih kursi favoritmu di Ticketra.">
<link rel="canonical" href="{{ url('/film/' . $film->id) }}">

<meta property="og:site_name" content="Ticketra.">
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="movie">
<meta property="og:title" content="{{ $film->judul }} - Detail Film & Jadwal Tayang | Ticketra.">
<meta property="og:description" content="{{ Str::limit($film->sinopsis, 160) }}">
<meta property="og:url" content="{{ url('/film/' . $film->id) }}">
<meta property="og:image" content="{{ $film->poster_url }}">
<meta property="og:image:width" content="400">
<meta property="og:image:height" content="600">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $film->judul }} - Detail Film & Jadwal Tayang | Ticketra.">
<meta name="twitter:description" content="{{ Str::limit($film->sinopsis, 160) }}">
<meta name="twitter:image" content="{{ $film->poster_url }}">

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Movie",
    "name": "{{ $film->judul }}",
    "image": "{{ $film->poster_url }}",
    "description": @json($film->sinopsis, JSON_UNESCAPED_UNICODE),
    "datePublished": "{{ $film->tanggal_rilis?->format('Y-m-d') ?? '' }}",
    "duration": "PT{{ $film->durasi_menit }}M",
    "genre": "{{ $film->genre }}",
    "offers": {
        "@@type": "Offer",
        "availability": "https://schema.org/InStock",
        "url": "{{ url('/film/' . $film->id) }}",
        "priceCurrency": "IDR"
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Film", "item": "{{ url('/film') }}" },
        { "@@type": "ListItem", "position": 3, "name": "{{ $film->judul }}" }
    ]
}
</script>
@stop

<x-app-layout>

    
    <div x-data="{
        showtimesData: @js($showtimesGrouped),
        dates: [],
        selectedDate: '',
        toastMessage: '',
        showToast: false,
        toastType: 'success',
        openTrailer: false,
        showAllCast: false,
        
        init() {
            const daysNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthsNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            const today = new Date();
            
            for (let i = 0; i < 5; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);
                const dayName = i === 0 ? 'Hari Ini' : daysNames[date.getDay()];
                const dateStr = date.getDate() + ' ' + monthsNames[date.getMonth()];
                
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const isoDate = `${year}-${month}-${day}`;
                
                this.dates.push({ dayName, dateStr, isoDate });
            }
            
            this.selectedDate = this.dates[0].isoDate;

            @if (session('success'))
                this.triggerToast('{{ session('success') }}', 'success');
            @endif
            @if (session('error'))
                this.triggerToast('{{ session('error') }}', 'error');
            @endif
        },
        
        triggerToast(msg, type = 'success') {
            this.toastMessage = msg;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },
        
        getFilteredCinemas() {
            if (!this.showtimesData[this.selectedDate]) return [];
            
            const allCinemas = [];
            Object.values(this.showtimesData[this.selectedDate]).forEach(cityCinemas => {
                Object.values(cityCinemas).forEach(cinema => {
                    allCinemas.push(cinema);
                });
            });
            return allCinemas;
        }
    }" class="relative space-y-8">

        
        <div x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-20px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-20px]"
            :class="toastType === 'error' ? 'bg-[#FFD1D1] border-red-500 text-red-900 shadow-[4px_4px_0px_rgba(0,0,0,1)]' : 'bg-pastel-mint border-border text-foreground shadow-[4px_4px_0px_rgba(0,0,0,1)]'"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-[999] border-[3px] px-6 py-3.5 rounded-xl font-bold text-xs flex items-center gap-2"
            style="display: none;">
            <template x-if="toastType === 'error'">
                <x-icon name="heroicon-s-exclamation-triangle" class="w-5 h-5 text-red-600 shrink-0" />
            </template>
            <template x-if="toastType !== 'error'">
                <x-icon name="heroicon-s-check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
            </template>
            <span x-text="toastMessage"></span>
        </div>

        
        

        
        <section class="brutal-card bg-[#EBE9FE] p-6 md:p-8 border-[3px] border-border rounded-[24px] shadow-[8px_8px_0px_rgba(0,0,0,1)]">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                
                <div class="flex flex-col items-center gap-4 lg:col-span-4">
                    <div
                        class="relative w-full max-w-[240px]
           border-[3px] border-border
           rounded-[20px]
           overflow-hidden
           bg-white
           shadow-[6px_6px_0px_rgba(0,0,0,1)]">
                        
                        <div class="aspect-[2/3]">
                            <img
                                src="{{ $film->poster_url }}"
                                alt="{{ $film->judul }}"
                                class="object-cover w-full h-full" />
                        </div>

                        
                        <div
                            class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/70 to-transparent"></div>

                        
                        <div class="absolute bottom-4 left-4 right-4">
                            <button
                                @click="openTrailer = true"
                                class="w-full bg-white
                   border-[3px] border-border
                   rounded-xl
                   py-3
                   font-bold text-sm
                   text-foreground
                   flex items-center justify-center gap-2
                   shadow-[3px_3px_0px_rgba(0,0,0,1)]
                   hover:bg-slate-50
                   active:translate-y-[1px]
                   active:shadow-[2px_2px_0px_rgba(0,0,0,1)]
                   transition-all">
                                <x-icon
                                    name="heroicon-s-play"
                                    class="w-5 h-5" />
                                <span>Putar Trailer</span>
                            </button>
                        </div>
                    </div>
                    
                </div>

                
                <div class="flex flex-col justify-between gap-6 lg:col-span-8">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-3xl font-black tracking-tight uppercase md:text-4xl font-heading text-foreground">
                                    {{ $film->judul }}
                                </h1>
                                <div class="flex items-center gap-1.5 px-3 py-1 bg-white border-2 border-border rounded-xl text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">
                                    <x-icon name="heroicon-s-star" class="w-4 h-4 fill-current text-accent-yellow" />
                                    <span>{{ number_format($film->rating ?: 8.0, 1) }}</span>
                                    <span class="text-foreground/50 text-[10px]">/10</span>
                                </div>
                                <span class="inline-block px-1 py-0.5 bg-accent-yellow border border-border text-[9px] font-black rounded tracking-tighter">IMDb</span>
                            </div>

                            
                            <p class="text-xs font-bold tracking-wide uppercase text-foreground/70">
                                {{ $film->tanggal_rilis?->format('Y') ?? '2024' }} &bull; {{ floor($film->durasi_menit / 60) }}j {{ $film->durasi_menit % 60 }}m &bull; {{ $film->genre }}
                            </p>
                        </div>

                        
                        <div class="flex flex-wrap gap-2 pt-1">
                            <span class="px-3 py-1 bg-white border-2 border-border rounded-lg text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">
                                {{ $film->rating_usia }}
                            </span>
                            @if(str_contains(strtolower($film->genre), 'sci-fi') || str_contains(strtolower($film->judul), 'dune') || str_contains(strtolower($film->judul), 'godzilla'))
                            <span class="px-3 py-1 bg-white border-2 border-border rounded-lg text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">IMAX</span>
                            <span class="px-3 py-1 bg-white border-2 border-border rounded-lg text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">Dolby Atmos</span>
                            <span class="px-3 py-1 bg-white border-2 border-border rounded-lg text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">2D & 3D</span>
                            @else
                            <span class="px-3 py-1 bg-white border-2 border-border rounded-lg text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">2D</span>
                            <span class="px-3 py-1 bg-white border-2 border-border rounded-lg text-xs font-extrabold shadow-[2px_2px_0px_rgba(0,0,0,1)]">Dolby Surround</span>
                            @endif
                        </div>

                        
                        <div x-data="{ expanded: false }">

                            <p
                                :class="expanded
            ? 'max-h-[300px] overflow-y-auto no-scrollbar pr-2'
            : 'line-clamp-4'"
                                class="pt-2 text-sm font-medium leading-relaxed transition-all duration-300 text-foreground/80">
                                {{ $film->sinopsis }}
                            </p>

                            <button
                                @click="expanded = !expanded"
                                class="mt-3 text-xs font-black tracking-widest uppercase transition-colors text-foreground hover:text-primary ">
                                <span x-text="expanded ? 'Tutup Ringkasan' : 'Baca Selengkapnya'" class="inline-block transition-transform duration-200"></span>

                                <template x-if="expanded">
                                    <x-icon name="heroicon-s-chevron-up" class="w-3.5 h-3.5 inline-block" />
                                </template>

                                <template x-if="!expanded">
                                    <x-icon name="heroicon-s-chevron-down" class="w-3.5 h-3.5 inline-block" />
                                </template>
                            </button>

                        </div>
                    </div>

                    
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t-2 border-dashed border-border/20">
                        <div>
                            <p class="text-[10px] font-bold text-foreground/50 uppercase tracking-wider">Sutradara</p>
                            <p class="text-xs font-extrabold text-foreground mt-0.5">{{ $film->sutradara ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-foreground/50 uppercase tracking-wider">Penulis</p>
                            <p class="text-xs font-extrabold text-foreground mt-0.5">{{ $film->penulis ?: 'N/A' }}</p>
                        </div>
                        <div class="col-span-1">
                            <p class="text-[10px] font-bold text-foreground/50 uppercase tracking-wider">Pemain</p>
                            <p class="text-xs font-extrabold text-foreground mt-0.5 line-clamp-2" title="{{ $film->pemain }}">{{ $film->pemain ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

            
            <div id="pilih-jadwal" class="space-y-6 lg:col-span-8">

                <div class="border-[3px] border-border rounded-[24px] bg-white p-5 md:p-6 shadow-[6px_6px_0px_rgba(0,0,0,1)] space-y-6">
                    <h2 class="text-2xl font-black tracking-tight uppercase text-foreground">PILIH JADWAL & BIOSKOP</h2>

                    
                    <div class="flex items-center gap-3 pb-2 overflow-x-auto hide-scrollbar">
                        <div class="flex gap-2">
                            <template x-for="date in dates" :key="date.isoDate">
                                <button @click="selectedDate = date.isoDate"
                                    :class="selectedDate === date.isoDate ? 'bg-accent-green text-border border-border translate-x-[2px] translate-y-[2px] shadow-[2px_2px_0px_rgba(0,0,0,1)]' : 'bg-white text-border hover:bg-slate-50 active:translate-x-[2px] active:translate-y-[2px] active:shadow-[2px_2px_0px_rgba(0,0,0,1)] shadow-[4px_4px_0px_rgba(0,0,0,1)]'"
                                    class="px-5 py-3.5 rounded-[16px] border-[3px] border-border text-center flex flex-col items-center justify-center min-w-[100px] transition-all focus:outline-none">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wide opacity-60" x-text="date.dayName"></span>
                                    <span class="text-sm font-black mt-0.5" x-text="date.dateStr"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    
                    <div class="pt-4 space-y-4">

                        
                        <div x-show="getFilteredCinemas().length === 0"
                            class="border-4 border-dashed border-border/20 rounded-[20px] p-8 text-center text-foreground/50 font-bold"
                            style="display: none;">
                            Tidak ada jadwal tayang untuk tanggal terpilih.
                        </div>

                        <template x-for="cinema in getFilteredCinemas()" :key="cinema.id">
                            <div class="border-[3px] border-border rounded-xl bg-white p-5 shadow-[3px_3px_0px_rgba(0,0,0,0.15)] space-y-4">
                                <div class="flex flex-col justify-between gap-3 pb-3 border-b sm:flex-row sm:items-center border-border/10">
                                    <div>
                                        <h3 class="text-base font-extrabold text-foreground" x-text="cinema.name"></h3>
                                        <p class="text-[10px] font-semibold text-foreground/55 mt-0.5 flex items-center gap-1">
                                            <span>Jakarta</span> &bull; <span x-text="cinema.distance"></span>
                                        </p>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="fmt in cinema.formats" :key="fmt">
                                            <span class="px-2 py-0.5 bg-pastel-sky border-2 border-border text-[9px] font-black rounded" x-text="fmt"></span>
                                        </template>
                                    </div>
                                </div>

                                
                                <div class="flex flex-wrap gap-2.5">
                                    <template x-for="st in cinema.showtimes" :key="st.id">
                                        <a :href="st.is_past ? 'javascript:void(0)' : '/jadwal/' + st.id + '/kursi'"
                                            :class="st.is_past 
                                                ? 'px-4 py-2 bg-gray-100 text-gray-400 border-2 border-border/30 rounded-xl font-black text-xs cursor-not-allowed opacity-50' 
                                                : 'px-4 py-2 bg-white hover:bg-accent-green hover:border-border border-2 border-border rounded-xl font-black text-xs transition-all shadow-[2px_2px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] active:translate-y-[1px]'"
                                            x-text="st.time">
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>

                    <button class="w-full py-3 border-2 border-border rounded-xl text-xs font-black text-foreground hover:bg-slate-50 shadow-[2px_2px_0px_rgba(0,0,0,1)] flex items-center justify-center gap-1 active:translate-y-[0.5px]">
                        <span>Tampilkan lebih banyak bioskop</span>
                        <x-icon name="heroicon-s-chevron-down" class="w-3.5 h-3.5" />
                    </button>

                </div>

            </div>

            
            <div class="space-y-6 lg:col-span-4">

                
                <div class="border-[3px] border-border rounded-[24px] bg-white p-5 md:p-6 shadow-[6px_6px_0px_rgba(0,0,0,1)] space-y-4">
                    <h2 class="text-lg font-black tracking-wide uppercase text-foreground">TENTANG FILM</h2>

                    <div class="space-y-3.5 text-xs font-bold text-foreground">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 border-2 rounded-lg bg-pastel-mint border-border shrink-0">
                                <x-icon name="heroicon-s-calendar" class="w-4 h-4 text-border" />
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-foreground/50 uppercase leading-none">Rilis</p>
                                <p class="mt-0.5">{{ $film->tanggal_rilis?->format('d F Y') ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 border-2 rounded-lg bg-pastel-sky border-border shrink-0">
                                <x-icon name="heroicon-s-language" class="w-4 h-4 text-border" />
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-foreground/50 uppercase leading-none">Bahasa</p>
                                <p class="mt-0.5">{{ $film->bahasa }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 border-2 rounded-lg bg-pastel-peach border-border shrink-0">
                                <x-icon name="heroicon-s-globe-alt" class="w-4 h-4 text-border" />
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-foreground/50 uppercase leading-none">Negara</p>
                                <p class="mt-0.5">{{ $film->negara }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 border-2 rounded-lg bg-pastel-lemon border-border shrink-0">
                                <x-icon name="heroicon-s-clock" class="w-4 h-4 text-border" />
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-foreground/50 uppercase leading-none">Durasi</p>
                                <p class="mt-0.5">{{ $film->durasi_menit }} Menit</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 border-2 rounded-lg bg-pastel-lavender border-border shrink-0">
                                <x-icon name="heroicon-s-building-office" class="w-4 h-4 text-border" />
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-foreground/50 uppercase leading-none">Produksi</p>
                                <p class="mt-0.5 truncate max-w-[200px]" title="{{ $film->produksi }}">{{ $film->produksi ?: 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 border-2 rounded-lg bg-pastel-pink border-border shrink-0">
                                <x-icon name="heroicon-s-shield-check" class="w-4 h-4 text-border" />
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-foreground/50 uppercase leading-none">Rating</p>
                                <p class="mt-0.5">{{ $film->rating_usia }} (Remaja)</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        
        <section class="grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($promos as $promo)
            <div class="border-[3px] border-border rounded-[24px] p-6 flex items-center gap-4 shadow-[4px_4px_0px_var(--border)] relative {{ $promo['color'] }}">
                <div class="w-14 h-14 bg-white border-[3px] border-border rounded-xl flex items-center justify-center text-border shadow-[3px_3px_0px_rgba(0,0,0,1)] shrink-0 select-none">
                    @if($promo['id'] === 'p1')
                        <x-icon name="heroicon-s-shopping-bag" class="w-7 h-7 text-border" />
                    @else
                        <x-icon name="heroicon-s-credit-card" class="w-7 h-7 text-border" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-extrabold leading-tight uppercase text-foreground">{{ $promo['title'] }}</h3>
                    <p class="text-[11px] font-medium text-foreground/75 mt-1 leading-normal">{{ $promo['desc'] }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="font-mono text-xs font-bold bg-white/70 px-2 py-0.5 rounded border border-border/20" x-text="'{{ $promo['code'] }}'"></span>
                        <button @click="navigator.clipboard.writeText('{{ $promo['code'] }}'); triggerToast('Kode promo {{ $promo['code'] }} disalin!')"
                            class="px-3 py-1 bg-white border-2 border-border rounded-lg text-[9px] font-extrabold hover:bg-slate-50 shadow-[1px_1px_0px_var(--border)] active:translate-y-[0.5px]">
                            Salin
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </section>

        
        <section class="border-[3px] border-border rounded-[24px] bg-white p-5 md:p-6 shadow-[6px_6px_0px_rgba(0,0,0,1)] space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black tracking-tight uppercase text-foreground">FILM LAINNYA YANG MUNGKIN KAMU SUKAI</h2>
                <a href="{{ route('film.index') }}" class="text-xs font-bold underline hover:text-emerald-600">Lihat Semua</a>
            </div>

            
            <div class="flex gap-6 pb-4 overflow-x-auto snap-x scroll-smooth md:grid md:grid-cols-4 md:pb-0">
                @foreach($recommendations as $rec)
                <div class="border-[3px] border-border rounded-[20px] overflow-hidden bg-white shadow-[3px_3px_0px_rgba(0,0,0,1)] flex flex-col justify-between group cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_rgba(0,0,0,1)] min-w-[200px] md:min-w-0 snap-start"
                    @click="window.location.href = '{{ route('film.show', $rec->id) }}'">

                    <div class="relative aspect-[2/3] overflow-hidden border-b-2 border-border bg-slate-100">
                        <img src="{{ $rec->poster_url }}" alt="{{ $rec->judul }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
                        <div class="absolute top-2 right-2 px-2 py-0.5 bg-white border border-border rounded-lg text-[8px] font-extrabold flex items-center gap-0.5 shadow-sm">
                            <x-icon name="heroicon-s-star" class="w-3 h-3 fill-current text-accent-yellow" />
                            <span>{{ number_format($rec->rating, 1) }}</span>
                        </div>
                    </div>

                    <div class="p-3.5 space-y-1">
                        <h3 class="text-xs font-extrabold leading-tight uppercase text-foreground line-clamp-1 group-hover:text-emerald-600">
                            {{ $rec->judul }}
                        </h3>
                        <p class="text-[8px] font-bold text-foreground/50 uppercase tracking-wider truncate">{{ $rec->genre }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        
        <section class="pt-8 border-t-2 border-dashed border-border/20">
            <h2 class="mb-6 text-lg font-black tracking-wide text-center uppercase text-foreground">KENAPA PESAN DI TICKETRA?</h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center border-2 rounded-lg w-9 h-9 bg-pastel-lemon border-border shrink-0">
                        <x-icon name="heroicon-s-ticket" class="w-4 h-4 text-border" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-foreground">Kursi Pilihan Terbaik</h4>
                        <p class="text-[10px] font-semibold text-foreground/60 mt-0.5 leading-normal">Pilih kursi favoritmu dengan mudah dan cepat.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center border-2 rounded-lg w-9 h-9 bg-pastel-mint border-border shrink-0">
                        <x-icon name="heroicon-s-shield-check" class="w-4 h-4 text-border" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-foreground">Pembayaran Aman</h4>
                        <p class="text-[10px] font-semibold text-foreground/60 mt-0.5 leading-normal">Transaksi aman dengan berbagai metode pembayaran.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center border-2 rounded-lg w-9 h-9 bg-pastel-sky border-border shrink-0">
                        <x-icon name="heroicon-s-phone" class="w-4 h-4 text-border" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-foreground">E-Ticket Instan</h4>
                        <p class="text-[10px] font-semibold text-foreground/60 mt-0.5 leading-normal">Tiket langsung dikirim ke email & aplikasi Anda.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center border-2 rounded-lg w-9 h-9 bg-pastel-pink border-border shrink-0">
                        <x-icon name="heroicon-s-arrow-path" class="w-4 h-4 text-border" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-foreground">Refund Mudah</h4>
                        <p class="text-[10px] font-semibold text-foreground/60 mt-0.5 leading-normal">Proses refund mudah sesuai ketentuan bioskop.</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
    
    <div x-show="openTrailer"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;"
        x-transition.opacity>
        <div @click.away="openTrailer = false"
            class="relative w-full max-w-3xl bg-white border-[4px] border-border rounded-[24px] shadow-[8px_8px_0px_rgba(0,0,0,1)] overflow-hidden">
            
            <div class="flex items-center justify-between p-4 border-b-[3px] border-border bg-pastel-sky">
                <h3 class="text-sm font-black uppercase font-heading text-foreground">TRAILER: {{ $film->judul }}</h3>
                <button @click="openTrailer = false" class="w-8 h-8 bg-white border-2 border-border rounded-full flex items-center justify-center hover:scale-105 active:translate-y-[1px] focus:outline-none">
                    <x-icon name="heroicon-s-x-mark" class="w-4 h-4 text-border" />
                </button>
            </div>
            
            <div class="w-full bg-black aspect-video">
                <template x-if="openTrailer">
                    <iframe class="w-full h-full"
                        src="{{ $film->trailer_url ?: 'https:
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>