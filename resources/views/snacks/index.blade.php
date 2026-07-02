<x-app-layout>
    <style>
        .text-stroke-soft {
            -webkit-text-stroke: 2px var(--border, #111827);
            color: transparent;
        }
        .filter-pill {
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            border-width: 3px;
            border-color: var(--border, #111827);
            background-color: #ffffff;
            font-weight: 800;
            font-size: 0.75rem;
            color: var(--border, #111827);
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: none;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .filter-pill:hover {
            background-color: rgba(255, 243, 176, 0.2);
            transform: translateY(-2px);
            box-shadow: 3px 3px 0px var(--border, #111827);
        }
        .filter-pill.active {
            background-color: var(--pastel-mint, #bef264);
            transform: translate(-1px, -1px);
            box-shadow: 3px 3px 0px var(--border, #111827);
        }
        .disabled-card {
            opacity: 0.6;
            filter: grayscale(80%);
        }
    </style>


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('snacks', {
                searchQuery: '',
            });

            Alpine.data('snacksComponent', () => ({
                activeCat: 'Semua',
                sortBy: new URLSearchParams(window.location.search).get('sort') || 'populer',
                cart: [],
                showToast: false,
                toastMsg: '',
                toastType: 'success',
                toastTimeout: null,
                fnbItems: @json($fnbItems),

                init() {
                    const search = new URLSearchParams(window.location.search).get('q');
                    if (search) {
                        Alpine.store('snacks').searchQuery = search;
                    }

                    const urlParams = new URLSearchParams(window.location.search);
                    const kat = urlParams.get('kategori');
                    if (kat) {
                        const matched = ['popcorn', 'minuman', 'snack', 'combo'].find(c => c === kat.toLowerCase());
                        if (matched) {
                            this.activeCat = matched.charAt(0).toUpperCase() + matched.slice(1);
                        } else {
                            this.activeCat = 'Semua';
                        }
                    }

                    this.$watch('activeCat', (value) => {
                        const url = new URL(window.location);
                        if (value === 'Semua') {
                            url.searchParams.delete('kategori');
                        } else {
                            url.searchParams.set('kategori', value.toLowerCase());
                        }
                        window.history.replaceState({}, '', url);
                    });

                    this.$watch('sortBy', (value) => {
                        const url = new URL(window.location);
                        if (value === 'populer') {
                            url.searchParams.delete('sort');
                        } else {
                            url.searchParams.set('sort', value);
                        }
                        window.history.replaceState({}, '', url);
                    });

                    this.$watch('$store.snacks.searchQuery', (value) => {
                        const url = new URL(window.location);
                        if (!value.trim()) {
                            url.searchParams.delete('q');
                        } else {
                            url.searchParams.set('q', value.trim());
                        }
                        window.history.replaceState({}, '', url);
                    });
                },

                get sortedItems() {
                    let filtered = this.fnbItems;


                    if (this.activeCat !== 'Semua') {
                        filtered = filtered.filter(item => item.category === this.activeCat);
                    }


                    const query = (Alpine.store('snacks') ? Alpine.store('snacks').searchQuery : '').trim().toLowerCase();
                    if (query) {
                        filtered = filtered.filter(item =>
                            item.name.toLowerCase().includes(query) ||
                            (item.desc && item.desc.toLowerCase().includes(query)) ||
                            item.category.toLowerCase().includes(query)
                        );
                    }


                    let result = [...filtered];
                    if (this.sortBy === 'populer') {
                        result.sort((a, b) => b.popular - a.popular);
                    } else if (this.sortBy === 'murah') {
                        result.sort((a, b) => a.price - b.price);
                    } else if (this.sortBy === 'mahal') {
                        result.sort((a, b) => b.price - a.price);
                    }
                    return result;
                },

                get totalCartItems() {
                    return this.cart.reduce((total, item) => total + item.qty, 0);
                },

                get totalCartPrice() {
                    return this.cart.reduce((total, item) => total + item.price * item.qty, 0);
                },

                getCategoryColor(category) {
                    switch (category) {
                        case 'Popcorn':
                            return 'bg-secondary';
                        case 'Combo':
                            return 'bg-brand/20';
                        case 'Minuman':
                            return 'bg-background';
                        case 'Snack':
                            return 'bg-brand/20';
                        default:
                            return 'bg-gray-100';
                    }
                },

                updateCart(item, delta) {
                    if (item.status === 'HABIS') {
                        this.triggerAction(`Maaf, ${item.name} sedang habis.`, 'error');
                        return;
                    }

                    const existing = this.cart.find(i => i.id === item.id);
                    if (existing) {
                        existing.qty += delta;
                        if (existing.qty <= 0) {
                            this.cart = this.cart.filter(i => i.id !== item.id);
                        }
                    } else if (delta > 0) {
                        this.cart.push({ ...item, qty: 1 });
                        this.triggerAction(`${item.name} ditambahkan!`, 'success');
                    }
                },

                getItemQty(id) {
                    const item = this.cart.find(i => i.id === id);
                    return item ? item.qty : 0;
                },

                checkoutCart() {
                    document.getElementById('cartInput').value = JSON.stringify(this.cart.map(item => ({
                        id: item.id,
                        qty: item.qty
                    })));
                    document.getElementById('checkoutSnacksForm').submit();
                },

                triggerAction(msg, type = 'success') {
                    if (this.toastTimeout) clearTimeout(this.toastTimeout);
                    this.toastMsg = msg;
                    this.toastType = type;
                    this.showToast = true;
                    this.toastTimeout = setTimeout(() => {
                        this.showToast = false;
                    }, 2500);
                }
            }));
        });
    </script>


    <div x-data="snacksComponent" class="relative min-h-screen pb-32">


        <div x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-20px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-20px]"
            :class="toastType === 'error' ? 'bg-[#FFD1D1] border-red-500 text-red-900 shadow-[4px_4px_0px_rgba(0,0,0,1)]' : 'bg-background border-border text-foreground shadow-[4px_4px_0px_rgba(0,0,0,1)]'"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-[999] border-[3px] px-6 py-3.5 rounded-xl font-bold text-xs flex items-center gap-2"
            style="display: none;">
            <template x-if="toastType === 'error'">
                <x-icon name="heroicon-s-exclamation-triangle" class="w-5 h-5 text-red-600 shrink-0" />
            </template>
            <template x-if="toastType !== 'error'">
                <x-icon name="heroicon-s-check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
            </template>
            <span x-text="toastMsg"></span>
        </div>


        <section class="relative bg-brand/20 border-[3px] border-border rounded-[2rem] p-8 md:p-14 shadow-[8px_8px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col md:flex-row gap-12 items-center mb-16">
            <div class="absolute inset-0 opacity-[0.06] pointer-events-none bg-dot-matrix"></div>

            <div class="relative z-10 flex-1 w-full">
                <span class="bg-foreground text-white border-2 border-border px-4 py-1.5 text-xs font-black uppercase tracking-widest mb-6 inline-flex items-center gap-1.5 rounded-full shadow-[2px_2px_0px_rgba(0,0,0,1)] transform -rotate-2">
                    <x-icon name="heroicon-s-shopping-bag" class="w-3.5 h-3.5 text-accent-yellow" /> Snack Bar Fast Lane
                </span>
                <h1 class="text-[3.5rem] md:text-[5.5rem] lg:text-[6rem] font-price font-black leading-[0.85] tracking-tighter mb-6 uppercase">
                    Nonton <br />
                    Kurang <br />
                    <span class="text-white text-stroke-soft drop-shadow-[4px_4px_0px_rgba(0,0,0,1)]">Gak Ngunyah.</span>
                </h1>
                <p class="max-w-md pl-4 text-base font-bold border-l-4 border-foreground opacity-90 md:text-lg">
                    Pre-order popcorn & minumanmu. Langsung ambil di jalur khusus tanpa antre!
                </p>
            </div>

            <div class="relative z-10 flex items-center justify-center w-full h-64 mt-10 select-none md:w-auto md:mt-0 md:h-auto">
                <div class="absolute w-48 h-48 md:w-64 md:h-64 bg-accent border-4 border-border rounded-2xl transform rotate-[15deg] shadow-[8px_8px_0px_rgba(0,0,0,0.2)] flex items-center justify-center">
                    <x-icon name="heroicon-s-sparkles" class="w-20 h-20 text-border" />
                </div>
                <div class="absolute w-48 h-48 md:w-64 md:h-64 bg-brand/20 border-4 border-border rounded-2xl transform -rotate-[10deg] shadow-[8px_8px_0px_rgba(0,0,0,0.2)] flex items-center justify-center">
                    <x-icon name="heroicon-s-shopping-bag" class="w-20 h-20 text-border" />
                </div>
            </div>
        </section>


        <section class="grid gap-6 mb-16 md:grid-cols-2">
            <div class="brutal-card p-6 md:p-8 bg-accent/20 flex flex-col md:flex-row items-start md:items-center gap-6 cursor-pointer rounded-[2rem] border-4 hover:-translate-y-2 transform -rotate-1 shadow-[6px_6px_0px_var(--border)]"
                @click="triggerAction('Klaim Promo Upsize')">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-white border-2 border-border rounded-2xl flex items-center justify-center shadow-[3px_3px_0px_var(--border)] shrink-0 transform rotate-6 select-none">
                    <x-icon name="heroicon-s-shopping-bag" class="w-8 h-8 text-border" />
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest bg-white border-2 border-border px-2 py-1 rounded shadow-sm mb-2 inline-block">Khusus Member</span>
                    <h4 class="mb-1 text-xl md:text-2xl">Gratis Upsize Minuman!</h4>
                    <p class="mb-3 text-sm font-bold opacity-75">
                        Beli popcorn ukuran apapun, gratis upsize untuk semua varian Coca Cola.
                    </p>
                </div>
            </div>
            <div class="brutal-card p-6 md:p-8 bg-secondary flex flex-col md:flex-row items-start md:items-center gap-6 cursor-pointer rounded-[2rem] border-4 hover:-translate-y-2 transform rotate-1 shadow-[6px_6px_0px_var(--border)]"
                @click="triggerAction('Klaim Cashback QRIS')">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-white border-2 border-border rounded-2xl flex items-center justify-center shadow-[3px_3px_0px_var(--border)] shrink-0 transform -rotate-6 select-none">
                    <x-icon name="heroicon-s-credit-card" class="w-8 h-8 text-border" />
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest bg-white border-2 border-border px-2 py-1 rounded shadow-sm mb-2 inline-block">Metode Bayar</span>
                    <h4 class="mb-1 text-xl md:text-2xl">Cashback QRIS 20%</h4>
                    <p class="mb-3 text-sm font-bold opacity-75">
                        Gunakan QRIS Mandiri, Jago, atau BCA untuk cashback instan di F&B.
                    </p>
                </div>
            </div>
        </section>


        <section class="bg-white border-4 border-border rounded-[2rem] p-8 shadow-[8px_8px_0px_rgba(0,0,0,1)] relative mb-16">
            <div class="absolute -top-5 left-8 bg-accent border-2 border-border px-4 py-1 text-sm font-black uppercase shadow-[3px_3px_0px_var(--border)] transform -rotate-3 select-none">
                Cara Kerjanya
            </div>
            <div class="grid grid-cols-1 gap-8 mt-4 md:grid-cols-3">
                <div class="flex flex-col gap-3">
                    <x-icon name="heroicon-s-shopping-bag" class="w-12 h-12 select-none text-border" />
                    <h4 class="text-lg font-black tracking-tight uppercase">1. Pesan Online</h4>
                    <p class="text-sm font-medium opacity-80">
                        Masukkan ke keranjang bareng tiket atau pesan terpisah sebelum film mulai.
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <x-icon name="heroicon-s-phone" class="w-12 h-12 select-none text-border" />
                    <h4 class="text-lg font-black tracking-tight uppercase">2. Scan QR Code</h4>
                    <p class="text-sm font-medium opacity-80">
                        Tunjukkan QR E-Ticket F&B kamu di layar ke petugas Ticketra Fast Lane.
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <x-icon name="heroicon-s-ticket" class="w-12 h-12 select-none text-border" />
                    <h4 class="text-lg font-black tracking-tight uppercase">3. Langsung Ngunyah</h4>
                    <p class="text-sm font-medium opacity-80">
                        Makananmu sudah disiapkan fresh. Ambil dan langsung nikmati filmnya!
                    </p>
                </div>
            </div>
        </section>


        <section id="katalog" class="mb-16">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <h2 class="text-3xl tracking-tight uppercase md:text-4xl">Katalog Menu</h2>

                <form method="GET" action="{{ route('snacks.index') }}" class="flex items-center gap-3 bg-white p-2 border-[3px] border-border rounded-2xl shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                    <x-icon name="heroicon-s-building-office-2" class="w-5 h-5 text-border shrink-0 ml-2" />
                    <span class="text-xs font-black uppercase tracking-wider text-gray-700 whitespace-nowrap">Lokasi Bioskop:</span>
                    <select name="bioskop_id" onchange="this.form.submit()" class="bg-accent/20 border-2 border-border rounded-xl px-3 py-1.5 text-xs font-bold uppercase focus:outline-none cursor-pointer">
                        @foreach($bioskops as $b)
                            <option value="{{ $b->id }}" {{ $selectedBioskopId == $b->id ? 'selected' : '' }}>
                                {{ $b->nama }} ({{ $b->kota }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>


            <div class="flex flex-col items-center justify-between gap-6 pb-8 mb-8 border-b-4 md:flex-row border-border/20">
                <div class="flex items-center w-full gap-3 pb-2 overflow-x-auto md:w-auto hide-scrollbar">
                    <template x-for="cat in ['Semua', 'Popcorn', 'Minuman', 'Snack', 'Combo']" :key="cat">
                        <button
                            @click="activeCat = cat"
                            :class="activeCat === cat ? 'filter-pill active' : 'filter-pill'"
                            x-text="cat">
                        </button>
                    </template>
                </div>
                <div class="flex items-center w-full gap-4 md:w-auto">
                    <div class="relative w-full md:w-64" x-data="{ showSortDropdown: false }">
                        <button @click="showSortDropdown = !showSortDropdown" @click.away="showSortDropdown = false"
                            class="w-full flex items-center justify-between gap-2 px-5 py-3.5 bg-surface border-[3px] border-border rounded-xl text-xs font-bold uppercase whitespace-nowrap shadow-none hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5 hover:bg-accent/20/20 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="heroicon-s-arrows-up-down" class="w-4 h-4 text-border" />
                                <span x-text="sortBy === 'populer' ? 'Terpopuler' : (sortBy === 'murah' ? 'Harga Terendah' : 'Harga Tertinggi')">Terpopuler</span>
                            </div>
                            <x-icon name="heroicon-s-chevron-down" class="w-3 h-3 text-border" />
                        </button>
                        <div x-show="showSortDropdown" x-transition.opacity
                            class="absolute right-0 mt-2 w-full bg-white border-[3px] border-border rounded-xl shadow-[4px_4px_0px_var(--border)] z-50 py-1 text-xs font-bold text-foreground"
                            style="display: none;">
                            <button @click="sortBy = 'populer'; showSortDropdown = false" class="w-full px-5 py-3 text-left transition-colors border-b-2 hover:bg-background/30 border-border/10">Terpopuler</button>
                            <button @click="sortBy = 'murah'; showSortDropdown = false" class="w-full px-5 py-3 text-left transition-colors border-b-2 hover:bg-background/30 border-border/10">Harga Terendah</button>
                            <button @click="sortBy = 'mahal'; showSortDropdown = false" class="w-full px-5 py-3 text-left transition-colors hover:bg-background/30 last:border-b-0 border-border/10">Harga Tertinggi</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search input below catalog filters -->
            <div class="relative flex-1 max-w-md mb-8">
                <input type="text"
                    x-model.debounce.300ms="$store.snacks.searchQuery"
                    placeholder="Cari camilan atau minuman..."
                    class="w-full bg-surface border-[3px] border-border rounded-full pl-5 pr-12 py-3 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2" />
                <div class="absolute -translate-y-1/2 right-4 top-1/2 text-border">
                    <x-icon name="heroicon-s-magnifying-glass" class="w-5 h-5 text-border" />
                </div>
            </div>


            <div class="hidden md:grid md:grid-cols-4 gap-6 auto-rows-[minmax(320px,auto)]">

                <div class="hidden md:col-span-1 md:col-span-2 md:row-span-1 md:row-span-2"></div>

                <template x-for="(item, index) in sortedItems" :key="'desk-'+item.id">
                    <div class="brutal-card p-6 bg-white flex relative overflow-hidden rounded-[2rem] border-4 shadow-[6px_6px_0px_var(--border)]"
                         :class="{
                             'md:col-span-2': index === 0,
                             'md:col-span-1': index !== 0,
                             'md:row-span-1': true,
                             'disabled-card': item.status === 'HABIS'
                         }">

                        <template x-if="index === 0">
                            <div class="relative z-10 flex flex-col items-stretch justify-between flex-1 gap-6 p-2 md:flex-row">

                                <div class="absolute top-0 right-6 bg-black text-white px-5 py-2 font-black text-[10px] uppercase tracking-widest rounded-b-xl z-20">
                                    BEST VALUE
                                </div>

                                <div class="flex flex-col justify-between flex-1 py-2">
                                    <div>
                                        <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest mb-3 block" x-text="item.category"></span>
                                        <h3 class="text-4xl md:text-5xl font-black uppercase leading-[1.05] tracking-tight mb-4 text-black" x-text="item.name"></h3>
                                        <div class="border-l-[3px] border-black pl-4 mb-6">
                                            <p class="text-sm font-bold leading-relaxed text-gray-700 opacity-80 md:text-base" x-text="item.desc"></p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-4 mt-auto">

                                        <div class="border-[3px] border-black bg-white px-5 py-2.5 shadow-[4px_4px_0px_rgba(0,0,0,1)] w-max">
                                            <p class="text-2xl font-black text-black font-price">
                                                Rp <span x-text="item.price.toLocaleString('id-ID')"></span>
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <template x-if="getItemQty(item.id) > 0">
                                                <div class="flex items-center gap-2">
                                                    <button @click="updateCart(item, -1)" class="w-12 h-12 border-2 border-border rounded-xl flex items-center justify-center bg-white hover:bg-secondary font-bold shadow-[2px_2px_0px_var(--border)] text-2xl">-</button>
                                                    <span class="w-12 text-2xl font-black text-center font-price" x-text="getItemQty(item.id)"></span>
                                                    <button @click="updateCart(item, 1)" :disabled="item.status === 'HABIS'" class="w-12 h-12 border-2 border-border rounded-xl flex items-center justify-center bg-accent hover:bg-primary hover:text-white font-bold shadow-[2px_2px_0px_var(--border)] text-2xl">+</button>
                                                </div>
                                            </template>
                                            <template x-if="getItemQty(item.id) === 0">
                                                <button @click="updateCart(item, 1)" :disabled="item.status === 'HABIS'" class="rounded-full bg-black text-white hover:bg-gray-800 py-3.5 px-8 font-black uppercase tracking-wider text-xs shadow-[2px_2px_0px_rgba(0,0,0,0.15)] transition-colors w-max">
                                                    TAMBAH +
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full md:w-[220px] lg:w-[240px] aspect-[2/3] md:aspect-[3/4] border-[4px] border-black rounded-[2.5rem] flex items-center justify-center relative overflow-visible shadow-[4px_4px_0px_rgba(0,0,0,0.1)] self-center shrink-0" :class="getCategoryColor(item.category)">
                                    <div class="text-[6rem] md:text-[7rem] transform hover:scale-[1.15] hover:-rotate-6 transition-transform duration-300 drop-shadow-[8px_8px_0px_rgba(0,0,0,0.15)] z-10" x-text="item.emoji"></div>
                                </div>
                            </div>
                        </template>


                        <template x-if="index !== 0">
                            <div class="relative z-10 flex flex-col justify-between flex-1">
                                <div class="absolute z-20 -top-3 -left-3">
                                    <template x-if="item.status === 'TERSEDIA'">
                                        <span class="bg-accent border-2 border-border px-3 py-1.5 rounded-sm text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_var(--border)] transform -rotate-3 inline-block">Tersedia</span>
                                    </template>
                                    <template x-if="item.status === 'SISA SEDIKIT'">
                                        <span class="bg-accent border-2 border-border px-3 py-1.5 rounded-sm text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_var(--border)] transform rotate-3 inline-block">🔥 Sisa Sedikit</span>
                                    </template>
                                    <template x-if="item.status === 'HABIS'">
                                        <span class="bg-gray-200 border-2 border-border px-3 py-1.5 rounded-sm text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_var(--border)] transform -rotate-2 inline-block text-gray-500">Habis</span>
                                    </template>
                                </div>

                                <div class="w-full aspect-square border-4 border-border rounded-2xl flex items-center justify-center relative overflow-visible mt-2 shadow-[4px_4px_0px_var(--border)]" :class="getCategoryColor(item.category)">
                                    <div class="text-[5rem] lg:text-[6rem] transform scale-110 hover:scale-125 hover:-translate-y-3 hover:rotate-12 transition-all duration-300 drop-shadow-[4px_4px_0px_rgba(0,0,0,0.15)] z-10" x-text="item.emoji"></div>
                                </div>

                                <div class="flex flex-col h-auto pt-5 shrink-0">
                                    <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest mb-1" x-text="item.category"></span>
                                    <h4 class="mb-3 text-lg font-extrabold leading-tight uppercase font-heading" x-text="item.name"></h4>

                                    <div class="flex items-center gap-2 pl-3 mb-4 border-l-2 border-gray-400">
                                        <p class="text-xl font-black font-price text-foreground">
                                            Rp <span x-text="item.price.toLocaleString('id-ID')"></span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 pt-4 mt-auto border-t-2 border-border/20">
                                        <template x-if="getItemQty(item.id) > 0">
                                            <div class="flex items-center w-full gap-2">
                                                <button @click="updateCart(item, -1)" class="w-12 h-12 border-2 border-border rounded-xl flex items-center justify-center bg-white hover:bg-secondary font-black transition-colors shadow-[2px_2px_0px_var(--border)] text-xl">-</button>
                                                <span class="flex-1 text-2xl font-black text-center font-price" x-text="getItemQty(item.id)"></span>
                                                <button @click="updateCart(item, 1)" :disabled="item.status === 'HABIS'" class="w-12 h-12 border-2 border-border rounded-xl flex items-center justify-center bg-accent hover:bg-primary hover:text-white font-black transition-colors shadow-[2px_2px_0px_var(--border)] text-xl">+</button>
                                            </div>
                                        </template>
                                        <template x-if="getItemQty(item.id) === 0">
                                            <button @click="updateCart(item, 1)" :disabled="item.status === 'HABIS'" class="w-full bg-white border-[3px] border-black rounded-xl py-3 px-4 font-black uppercase text-xs tracking-wider shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:bg-gray-50 active:translate-y-0.5 active:shadow-[1px_1px_0px_rgba(0,0,0,1)] transition-all text-black text-center">
                                                TAMBAH +
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>


            <div class="flex flex-col gap-8 md:hidden">

                <template x-if="sortedItems.length > 0">
                    <div class="brutal-card p-6 bg-white border-4 flex flex-col gap-5 rounded-[2rem] relative shadow-[6px_6px_0px_var(--border)]" :class="sortedItems[0].status === 'HABIS' ? 'disabled-card' : ''">
                        <div class="absolute z-20 -top-4 -right-4">
                            <span class="bg-foreground text-white border-2 border-border px-3 py-1 font-black text-[10px] uppercase shadow-[2px_2px_0px_var(--border)] transform rotate-3 inline-block">🔥 Best Value</span>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest mb-1 block" x-text="sortedItems[0].category"></span>
                            <h3 class="text-3xl font-heading font-black uppercase leading-[1] tracking-tighter mb-2" x-text="sortedItems[0].name"></h3>
                            <p class="pl-2 text-xs font-bold border-l-4 opacity-80 border-border" x-text="sortedItems[0].desc"></p>
                        </div>

                        <div class="w-full aspect-[4/3] border-4 border-border rounded-2xl flex items-center justify-center relative overflow-visible shadow-[4px_4px_0px_var(--border)]" :class="getCategoryColor(sortedItems[0].category)">
                            <div class="text-[7rem] transform scale-110 drop-shadow-[8px_8px_0px_rgba(0,0,0,0.15)] z-10 hover:scale-125 transition-transform" x-text="sortedItems[0].emoji"></div>
                        </div>

                        <div class="flex flex-col items-start justify-between gap-4 mt-2 sm:flex-row sm:items-center">
                            <div class="inline-block bg-white px-2 py-1 transform -skew-x-6 border-2 border-border shadow-[2px_2px_0px_var(--border)]">
                                <p class="text-2xl font-black transform skew-x-6 font-price text-foreground">
                                    Rp <span x-text="sortedItems[0].price.toLocaleString('id-ID')"></span>
                                </p>
                            </div>

                            <div class="flex items-center w-full gap-2 sm:w-auto">
                                <template x-if="getItemQty(sortedItems[0].id) > 0">
                                    <div class="flex items-center w-full gap-2">
                                        <button @click="updateCart(sortedItems[0], -1)" class="w-12 h-12 border-2 border-border rounded-xl bg-white font-bold text-xl shadow-[2px_2px_0px_var(--border)]">-</button>
                                        <span class="flex-1 text-2xl font-black text-center font-price" x-text="getItemQty(sortedItems[0].id)"></span>
                                        <button @click="updateCart(sortedItems[0], 1)" :disabled="sortedItems[0].status === 'HABIS'" class="w-12 h-12 border-2 border-border rounded-xl bg-accent font-bold text-xl shadow-[2px_2px_0px_var(--border)]">+</button>
                                    </div>
                                </template>
                                <template x-if="getItemQty(sortedItems[0].id) === 0">
                                    <button @click="updateCart(sortedItems[0], 1)" :disabled="sortedItems[0].status === 'HABIS'" class="brutal-btn bg-foreground text-white w-full sm:w-auto !py-3 shadow-[2px_2px_0px_var(--border)]">
                                        Tambah <x-icon name="heroicon-s-plus" class="w-5 h-5 text-white" />
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>


                <template x-if="sortedItems.length > 1">
                    <div class="flex gap-4 px-1 pt-2 pb-6 overflow-x-auto snap-x hide-scrollbar">
                        <template x-for="item in sortedItems.slice(1)" :key="'mob-'+item.id">
                            <div class="max-w-[200px] md:max-w-max md:min-w-[260px] snap-start flex-shrink-0">
                                <div class="brutal-card p-5 bg-white flex flex-col h-full rounded-[1.5rem] border-2 shadow-[4px_4px_0px_var(--border)]" :class="item.status === 'HABIS' ? 'disabled-card' : ''">
                                    <div class="absolute z-20 -top-3 -left-3">
                                        <template x-if="item.status === 'TERSEDIA'">
                                            <span class="bg-accent border-2 border-border px-2 py-1 rounded-sm text-[9px] font-black uppercase shadow-[2px_2px_0px_var(--border)] transform -rotate-3 inline-block">Tersedia</span>
                                        </template>
                                        <template x-if="item.status === 'SISA SEDIKIT'">
                                            <span class="bg-accent border-2 border-border px-2 py-1 rounded-sm text-[9px] font-black uppercase shadow-[2px_2px_0px_var(--border)] transform rotate-3 inline-block">🔥 Sedikit</span>
                                        </template>
                                        <template x-if="item.status === 'HABIS'">
                                            <span class="bg-gray-200 border-2 border-border px-2 py-1 rounded-sm text-[9px] font-black uppercase shadow-[2px_2px_0px_var(--border)] transform -rotate-2 inline-block text-gray-500">Habis</span>
                                        </template>
                                    </div>

                                    <div class="aspect-square border-4 border-border rounded-2xl flex items-center justify-center relative overflow-visible mt-1 shadow-[4px_4px_0px_var(--border)]" :class="getCategoryColor(item.category)">
                                        <div class="text-[5rem] transform scale-110 z-10" x-text="item.emoji"></div>
                                    </div>

                                    <div class="flex flex-col justify-between flex-1 pt-5">
                                        <div>
                                            <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest mb-1 block" x-text="item.category"></span>
                                            <h4 class="mb-2 text-lg font-extrabold leading-tight uppercase font-heading" x-text="item.name"></h4>
                                            <p class="pl-2 mt-auto mb-4 text-xl font-black border-l-4 font-price text-foreground border-border">
                                                Rp <span x-text="item.price.toLocaleString('id-ID')"></span>
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2 pt-4 mt-auto border-t-2 border-border/20">
                                            <template x-if="getItemQty(item.id) > 0">
                                                <div class="flex items-center w-full gap-2">
                                                    <button @click="updateCart(item, -1)" class="w-12 h-12 border-2 border-border rounded-xl bg-white font-black text-xl shadow-[2px_2px_0px_var(--border)]">-</button>
                                                    <span class="flex-1 text-xl font-black text-center font-price" x-text="getItemQty(item.id)"></span>
                                                    <button @click="updateCart(item, 1)" :disabled="item.status === 'HABIS'" class="w-12 h-12 border-2 border-border rounded-xl bg-accent font-black text-xl shadow-[2px_2px_0px_var(--border)]">+</button>
                                                </div>
                                            </template>
                                            <template x-if="getItemQty(item.id) === 0">
                                                <button @click="updateCart(item, 1)" :disabled="item.status === 'HABIS'" class="brutal-btn w-full !py-3 !text-sm shadow-[2px_2px_0px_var(--border)] bg-white hover:bg-primary hover:text-white uppercase tracking-widest text-foreground">
                                                    Tambah
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>


            <div x-show="sortedItems.length === 0"
                class="text-center py-24 bg-white border-4 border-border border-dashed rounded-[2rem] mt-8"
                style="display: none;">
                <div class="w-20 h-20 bg-secondary border-4 border-border rounded-full flex items-center justify-center mx-auto mb-4 text-4xl transform rotate-12 shadow-[3px_3px_0px_var(--border)]">🔍</div>
                <h3 class="mb-2 text-3xl font-black tracking-tight uppercase font-heading">Ups, Menu Gak Ketemu</h3>
                <p class="text-lg font-medium opacity-70">Coba ubah filter kategori atau kata kunci pencarian Anda.</p>
            </div>
        </section>


        <div x-show="totalCartItems > 0"
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-y-24"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-24"
            class="fixed bottom-20 md:bottom-8 left-4 right-4 lg:left-[calc(50%+128px)] md:left-[calc(50%+128px)] md:-translate-x-1/2 md:w-full md:max-w-2xl z-50"
            style="display: none;">
            <div class="bg-white border-[3px] border-black rounded-2xl p-2.5 md:p-4 shadow-[4px_4px_0px_rgba(0,0,0,1)] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-accent border-2 border-black rounded-xl flex items-center justify-center relative transform -rotate-3 select-none">
                        <x-icon name="heroicon-s-shopping-bag" class="w-5 h-5 text-foreground" />
                        <span class="absolute -top-2 -right-2 bg-accent-red text-white text-[10px] font-black w-6 h-6 flex items-center justify-center rounded-full border-2 border-black shadow-[1px_1px_0px_rgba(0,0,0,1)]" x-text="totalCartItems"></span>
                    </div>
                    <div>
                        <p class="text-foreground/60 text-[9px] font-extrabold uppercase tracking-widest">Total Belanja</p>
                        <p class="text-base md:text-xl font-black leading-none text-foreground font-price">
                            Rp <span x-text="totalCartPrice.toLocaleString('id-ID')"></span>
                        </p>
                    </div>
                </div>
                <button @click="checkoutCart" class="flex items-center gap-1.5 py-2 px-4 md:py-2.5 md:px-6 bg-accent/20 text-foreground border-2 border-black rounded-xl font-bold text-xs md:text-sm shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-y-[1px] active:shadow-none transition-all hover:bg-accent/20/90">
                    Checkout <x-icon name="heroicon-s-arrow-right" class="w-4 h-4 text-foreground" />
                </button>
            </div>
        </div>

        <form id="checkoutSnacksForm" action="{{ route('snacks.checkout') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="cart" id="cartInput">
        </form>
    </div>
</x-app-layout>
