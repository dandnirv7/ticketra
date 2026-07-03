@php
    $film = $booking->jadwalTayang->film;
    $studio = $booking->jadwalTayang->studio;
    $bioskop = $studio->bioskop;
    $seats = $booking->statusKursis->map(fn($sk) => $sk->kursi->label_baris . $sk->kursi->nomor_kursi)->all();
    $isPayable = in_array($booking->status, ['locked', 'pending_payment']);
    $totalPrice = (float) $booking->total_price;
    $serviceFee = (float) ($booking->service_fee ?? 0);
    $fnbTotal = (float) ($booking->fnb_total ?? 0);
    $discountAmount = (float) ($booking->discount_amount ?? 0);
    $grandTotal = $totalPrice + $serviceFee + $fnbTotal - $discountAmount;
@endphp

<x-app-layout>
    <div
        x-data="{
            countdown: '',
            isPaying: false,
            toastMessage: '',
            showToast: false,
            init() {
                @if ($isPayable && $booking->lock_expiry)
                this.startCountdown('{{ $booking->lock_expiry->toIso8601String() }}');
                @endif
            },
            startCountdown(expiryIso) {
                const expiry = new Date(expiryIso).getTime();
                const tick = () => {
                    const diff = expiry - Date.now();
                    if (diff <= 0) {
                        this.countdown = '00:00';
                        clearInterval(this.timer);
                        return;
                    }
                    const m = Math.floor(diff / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    this.countdown = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                };
                tick();
                this.timer = setInterval(tick, 1000);
            }
        }">

        <x-slot name="header">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 w-full max-w-4xl mx-auto px-4">
                <h2 class="text-xl sm:text-2xl font-black leading-tight text-gray-900">
                    Checkout Tiket
                </h2>
                <a href="{{ route('film.show', $film->id) }}" class="neo-back-button border-black hover:border-accent-red hover:bg-red-50 hover:text-accent-red w-full sm:w-auto text-center">
                    Batalkan Pesanan
                </a>
            </div>
        </x-slot>

        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            @if ($isPayable && $booking->lock_expiry && count($seats) > 0)
            <div class="px-4 py-3 mb-6 text-center border-2 border-black rounded-full shadow-[3px_3px_0px_#000] bg-pastel-lemon">
                <p class="text-sm font-bold text-gray-700 md:text-base">
                    Kursi telah dikunci secara eksklusif. Pesanan otomatis dibatalkan dalam
                    <strong class="ml-1 text-lg font-price text-accent-red" x-text="countdown || '--:--'"></strong>.
                </p>
            </div>
            @endif

            @if (session('success'))
                <div class="px-4 py-4 mb-6 font-bold text-center text-emerald-900 border-4 border-black shadow-[6px_6px_0px_rgba(0,0,0,1)] rounded-xl bg-emerald-100">
                    <x-icon name="heroicon-s-check-circle" class="inline w-5 h-5 mb-0.5 mr-1 text-emerald-600" />
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="px-4 py-4 mb-6 font-bold text-center text-red-900 border-4 border-red-600 shadow-[6px_6px_0px_0px_rgba(220,38,38,0.5)] rounded-xl bg-red-100">
                    <x-icon name="heroicon-s-exclamation-triangle" class="inline w-5 h-5 mb-0.5 mr-1 text-red-600" />
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div class="brutal-box bg-white p-6 md:p-8 relative">
                    @if ($isPayable)
                        <span class="absolute -top-3 right-0 px-3 py-1.5 text-[10px] font-extrabold uppercase bg-pastel-lemon border-2 border-black rounded-md tracking-widest shadow-sm rotate-6 z-10">
                            ⏳ Menunggu Pembayaran
                        </span>
                    @elseif ($booking->status === 'confirmed')
                        <span class="absolute -top-3 right-0 px-3 py-1.5 text-[10px] font-extrabold uppercase border-2 border-black rounded-md tracking-widest shadow-sm bg-pastel-mint text-accent-green -rotate-6 z-10">
                            ✅ Lunas
                        </span>
                    @else
                        <span class="absolute -top-3 right-0 px-3 py-1.5 text-[10px] font-extrabold uppercase bg-gray-200 border-2 border-border rounded-md tracking-widest shadow-sm text-gray-500 -rotate-6 z-10">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    @endif

                    <h3 class="pb-4 mb-6 text-xl uppercase border-b-2 border-dashed border-border">Ringkasan Pesanan</h3>

                    @if (count($seats) > 0)
                    <div class="space-y-4 text-sm font-medium">
                        <div class="flex flex-col items-start justify-between gap-1 pb-4 border-b-2 border-dashed md:flex-row md:items-center border-border/30">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-widest opacity-50">ID Booking Anda</p>
                                <p class="mt-1 text-2xl font-black tracking-wider md:text-3xl text-main-foreground font-mono">{{ $booking->booking_id }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Film</span>
                            <span class="font-extrabold text-right uppercase">{{ $film->judul }}</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Lokasi</span>
                            <span class="font-bold text-right">{{ $bioskop->nama }}, {{ $bioskop->kota ?? '' }}</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Studio</span>
                            <span class="font-bold text-right">{{ $studio->nama }} ({{ ucfirst($studio->tipe) }})</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Jadwal</span>
                            <span class="font-bold text-right">{{ $booking->jadwalTayang->waktu_mulai->format('d F Y') }}, {{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Kursi</span>
                            <span class="font-extrabold text-right uppercase text-main-foreground">{{ implode(', ', $seats) }}</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Jumlah</span>
                            <span class="font-bold text-right">{{ count($seats) }} tiket &times; Rp {{ number_format((float) $booking->jadwalTayang->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @else
                    <div class="space-y-4 text-sm font-medium">
                        <div class="flex flex-col items-start justify-between gap-1 pb-4 border-b-2 border-dashed md:flex-row md:items-center border-border/30">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-widest opacity-50">ID Transaksi F&B</p>
                                <p class="mt-1 text-2xl font-black tracking-wider md:text-3xl text-main-foreground font-mono">{{ $booking->booking_id }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-32 shrink-0">Tujuan Bioskop</span>
                            <span class="font-extrabold text-right uppercase">{{ $bioskop->nama }}</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-32 shrink-0">Lokasi Pengambilan</span>
                            <span class="font-bold text-right">Snack Bar - Jalur Fast Lane</span>
                        </div>

                        <div class="flex flex-col justify-between gap-1 md:flex-row">
                            <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-32 shrink-0">Waktu Pengambilan</span>
                            <span class="font-bold text-right text-emerald-600">Kapan saja hari ini</span>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="space-y-6">
                    @if ($isPayable)
                        <div class="brutal-box bg-white p-6 md:p-8">
                            <h3 class="pb-3 mb-4 text-lg uppercase font-black border-b-2 border-dashed border-border flex items-center justify-between">
                                <span>🎟️ Kode Promo</span>
                                @if ($booking->promo)
                                    <span class="text-xs font-bold text-accent-green uppercase">Aktif</span>
                                @endif
                            </h3>

                            @if ($booking->promo)
                                <div class="flex items-center justify-between p-3 bg-emerald-50 border-2 border-black rounded-lg">
                                    <div>
                                        <p class="font-extrabold text-sm text-emerald-900">{{ $booking->promo->title }} ({{ $booking->promo->code }})</p>
                                        <p class="text-xs text-emerald-700">Diskon Rp {{ number_format($discountAmount, 0, ',', '.') }}</p>
                                    </div>
                                    <form action="{{ route('checkout.promo.remove', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-extrabold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('checkout.promo.apply', $booking->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="promo_code" placeholder="Ketik Kode Promo..." required class="neo-input uppercase flex-1 text-sm font-mono tracking-wider" />
                                    <button type="submit" class="brutal-btn !py-2 !px-4 text-xs font-black">Pakai</button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <div class="brutal-box bg-white p-6 md:p-8">
                        <h3 class="pb-4 mb-4 text-xl uppercase border-b-2 border-dashed border-border">Rincian Harga</h3>
                        <div class="space-y-2 text-sm font-bold">
                            @if (count($seats) > 0)
                            <div class="flex justify-between">
                                <span>Harga Tiket ({{ count($seats) }} &times; Rp {{ number_format((float) $booking->jadwalTayang->harga, 0, ',', '.') }})</span>
                                <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if ($serviceFee > 0)
                            <div class="flex justify-between text-gray-500">
                                <span>Service Fee</span>
                                <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-gray-500">
                                <span>Camilan (F&B)</span>
                                <span>Rp {{ number_format($fnbTotal, 0, ',', '.') }}</span>
                            </div>
                            @if ($discountAmount > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Diskon Promo</span>
                                <span>-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between pt-4 mt-3 border-t-2 border-dashed border-border">
                                <span class="text-xs font-extrabold uppercase tracking-widest">Total Pembayaran</span>
                                <span class="font-price text-3xl font-extrabold text-accent-red">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($isPayable)
                        <div class="brutal-box bg-white p-6 md:p-8 text-center">
                            <p class="mb-2 font-extrabold text-accent-green">
                                <x-icon name="heroicon-s-lock-closed" class="inline w-4 h-4 mr-1" />
                                PEMBAYARAN AMAN
                            </p>
                            <p class="mb-6 text-sm font-medium text-gray-600">Klik tombol di bawah untuk membuka jendela pembayaran Midtrans.</p>

                            <button id="pay-button"
                                    type="button"
                                    @click="isPaying = true; const _t = (m) => { toastMessage = m; showToast = true; setTimeout(() => showToast = false, 3000); }; window.snap.pay('{{ $snapToken }}', {
                                        onSuccess: (r) => { window.location.href = '{{ route('payment.success', $booking->id) }}'; },
                                        onPending: (r) => { window.location.href = '{{ route('payment.pending', $booking->id) }}'; },
                                        onError:   (r) => { _t('Pembayaran gagal. Silakan coba lagi.'); isPaying = false; },
                                        onClose:   ()  => { _t('Anda menutup popup pembayaran. Silakan coba lagi jika ingin melanjutkan.'); isPaying = false; }
                                    })"
                                    :disabled="isPaying"
                                    class="brutal-btn !py-4 !px-10 text-lg w-full justify-center">
                                <span x-show="!isPaying">
                                    <x-icon name="heroicon-s-bolt" class="inline w-5 h-5 mr-2" />
                                    Bayar Sekarang
                                </span>
                                <span x-show="isPaying" style="display: none;">
                                    <x-icon name="heroicon-s-arrow-path" class="inline w-5 h-5 mr-2 animate-spin" />
                                    Memproses Pembayaran...
                                </span>
                            </button>

                            <div x-show="showToast" x-transition.duration.300ms
                                 class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[999] px-6 py-3 bg-white border-[3px] border-border rounded-2xl shadow-[4px_4px_0px_rgba(0,0,0,1)] text-xs font-black text-center whitespace-nowrap"
                                 x-text="toastMessage"></div>

                            <p class="mt-4 text-[10px] font-bold uppercase tracking-widest opacity-60">
                                <x-icon name="heroicon-s-lock-closed" class="inline w-3 h-3 mr-1" />
                                Didukung oleh Midtrans &middot; QRIS &middot; VA &middot; E-Wallet &middot; Kartu Kredit
                            </p>
                        </div>
                    @else
                        <div class="brutal-box bg-white p-6 md:p-8 text-center">
                            <p class="mb-4 text-sm font-bold opacity-80">Booking ini tidak dalam status menunggu pembayaran.</p>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="brutal-btn !py-3 !px-6 text-sm inline-flex justify-center w-full">
                                Lihat Detail Booking <x-icon name="heroicon-s-arrow-right" class="w-4 h-4" />
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <p class="mt-8 text-xs font-bold text-center uppercase opacity-50 tracking-widest">
                <x-icon name="heroicon-s-shield-check" class="inline w-4 h-4" /> Transaksi aman & terenkripsi
            </p>
        </div>
    </div>
</x-app-layout>

@if($snapToken && $isPayable)
<script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif


