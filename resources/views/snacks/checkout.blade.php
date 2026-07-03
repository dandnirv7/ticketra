<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 w-full max-w-4xl mx-auto px-4">
            <h2 class="text-xl sm:text-2xl font-black leading-tight text-gray-900">
                Checkout Camilan
            </h2>
            <a href="{{ route('snacks.index') }}" class="neo-back-button border-black hover:border-accent-red hover:bg-red-50 hover:text-accent-red w-full sm:w-auto text-center">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6">

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
                @if ($snackOrder->status === 'locked')
                    <span class="absolute -top-3 right-0 px-3 py-1.5 text-[10px] font-extrabold uppercase bg-primary/20 border-2 border-black rounded-md tracking-widest shadow-sm rotate-6 z-10 text-primary">
                        ⏳ Menunggu Pembayaran
                    </span>
                @elseif ($snackOrder->status === 'paid')
                    <span class="absolute -top-3 right-0 px-3 py-1.5 text-[10px] font-extrabold uppercase border-2 border-black rounded-md tracking-widest shadow-sm bg-secondary/40 text-emerald-800 -rotate-6 z-10">
                        ✅ Lunas
                    </span>
                @else
                    <span class="absolute -top-3 right-0 px-3 py-1.5 text-[10px] font-extrabold uppercase bg-gray-200 border-2 border-border rounded-md tracking-widest shadow-sm text-gray-500 -rotate-6 z-10">
                        {{ ucfirst(str_replace('_', ' ', $snackOrder->status)) }}
                    </span>
                @endif

                <h3 class="pb-4 mb-6 text-xl uppercase border-b-2 border-dashed border-border">Ringkasan Pesanan</h3>

                <div class="space-y-4 text-sm font-medium">
                    <div class="flex flex-col items-start justify-between gap-1 pb-4 border-b-2 border-dashed md:flex-row md:items-center border-border/30">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-widest opacity-50">ID Pesanan F&B</p>
                            <p class="mt-1 text-2xl font-black tracking-wider md:text-3xl text-main-foreground font-mono">{{ $snackOrder->order_id }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($snackOrder->items as $item)
                        <div class="flex items-center justify-between py-2 border-b border-dashed border-border/20">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $item->snack_emoji }}</span>
                                <div>
                                    <p class="font-extrabold text-sm">{{ $item->snack_name }}</p>
                                    <p class="text-xs opacity-60">{{ $item->qty }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <span class="font-extrabold text-sm">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Pengambilan</span>
                        <span class="font-bold text-right">Snack Bar - Fast Lane</span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="brutal-box bg-white p-6 md:p-8">
                    <h3 class="pb-4 mb-4 text-xl uppercase border-b-2 border-dashed border-border">Rincian Harga</h3>
                    <div class="space-y-2 text-sm font-bold">
                        @foreach ($snackOrder->items as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->snack_name }} ({{ $item->qty }} &times; Rp {{ number_format($item->price, 0, ',', '.') }})</span>
                            <span>Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-4 mt-3 border-t-2 border-dashed border-border">
                            <span class="text-xs font-extrabold uppercase tracking-widest">Total Pembayaran</span>
                            <span class="font-price text-3xl font-extrabold text-accent-red">Rp {{ number_format($snackOrder->fnb_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if (in_array($snackOrder->status, ['locked']))
                    <div class="brutal-box bg-white p-6 md:p-8 text-center" x-data="{ isPaying: false }">
                        <p class="mb-2 font-extrabold text-primary">
                            <x-icon name="heroicon-s-lock-closed" class="inline w-4 h-4 mr-1" />
                            PEMBAYARAN AMAN
                        </p>
                        <p class="mb-6 text-sm font-medium text-gray-600">Klik tombol di bawah untuk membuka jendela pembayaran Midtrans.</p>

                        <button id="pay-button"
                                type="button"
                                @click="isPaying = true; window.snap.pay('{{ $snapToken }}', {
                                    onSuccess: (r) => { window.location.href = '{{ route('snacks.payment.success', $snackOrder->id) }}'; },
                                    onPending: (r) => { window.location.href = '{{ route('snacks.payment.pending', $snackOrder->id) }}'; },
                                    onError:   (r) => { alert('Pembayaran gagal. Silakan coba lagi.'); isPaying = false; },
                                    onClose:   ()  => { alert('Anda menutup popup pembayaran. Silakan coba lagi jika ingin melanjutkan.'); isPaying = false; }
                                })"
                                :disabled="isPaying"
                                class="brutal-btn bg-primary text-white hover:bg-[#A88CF8] shadow-[5px_5px_0px_#000] !py-4 !px-10 text-lg w-full justify-center">
                            <span x-show="!isPaying">
                                <x-icon name="heroicon-s-bolt" class="inline w-5 h-5 mr-2" />
                                Bayar Sekarang
                            </span>
                            <span x-show="isPaying" style="display: none;">
                                <x-icon name="heroicon-s-arrow-path" class="inline w-5 h-5 mr-2 animate-spin" />
                                Memproses Pembayaran...
                            </span>
                        </button>

                        <p class="mt-4 text-[10px] font-bold uppercase tracking-widest opacity-60">
                            <x-icon name="heroicon-s-lock-closed" class="inline w-3 h-3 mr-1" />
                            Didukung oleh Midtrans &middot; QRIS &middot; VA &middot; E-Wallet &middot; Kartu Kredit
                        </p>
                    </div>
                @else
                    <div class="brutal-box bg-white p-6 md:p-8 text-center">
                        <p class="mb-4 text-sm font-bold opacity-80">Pesanan ini tidak dalam status menunggu pembayaran.</p>
                    </div>
                @endif
            </div>
        </div>

        <p class="mt-8 text-xs font-bold text-center uppercase opacity-50 tracking-widest">
            <x-icon name="heroicon-s-shield-check" class="inline w-4 h-4" /> Transaksi aman & terenkripsi
        </p>
    </div>
</x-app-layout>

@if($snapToken && in_array($snackOrder->status, ['locked']))
<script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif


