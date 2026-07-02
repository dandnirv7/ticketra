<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full max-w-2xl mx-auto">
            <h2 class="text-2xl font-black leading-tight text-gray-900">
                Detail E-Ticket F&B
            </h2>
            <a href="{{ route('bookings.index') }}" class="neo-back-button border-black hover:border-accent-red hover:bg-red-50 hover:text-accent-red">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <div class="brutal-box bg-white p-6 md:p-10 relative shadow-[8px_8px_0px_#000] border-4 border-black rounded-[2rem] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-dot-matrix"></div>

            <div class="absolute top-6 right-6">
                @if($snackOrder->status === 'paid')
                    <span class="px-3.5 py-1.5 text-[10px] font-black uppercase tracking-wider bg-background border-2 border-black rounded-lg shadow-[2px_2px_0px_#000] text-accent-green">
                        ✅ Lunas
                    </span>
                @else
                    <span class="px-3.5 py-1.5 text-[10px] font-black uppercase tracking-wider bg-accent/20 border-2 border-black rounded-lg shadow-[2px_2px_0px_#000] text-amber-700">
                        ⏳ {{ strtoupper($snackOrder->status) }}
                    </span>
                @endif
            </div>

            <div class="border-b-4 border-dashed border-border/30 pb-6 mb-8 mt-4">
                <p class="text-[10px] font-extrabold uppercase tracking-widest opacity-50 font-mono">Order ID</p>
                <h1 class="text-3xl md:text-4xl font-black tracking-wider text-black font-mono mt-1">{{ $snackOrder->order_id }}</h1>
                <p class="text-xs font-bold text-gray-500 mt-2">Dipesan pada {{ $snackOrder->created_at->format('d M Y H:i') }} WIB</p>
            </div>

            @if($snackOrder->status === 'paid')
            <div class="flex flex-col items-center justify-center bg-accent/20/20 border-4 border-black p-6 rounded-2xl shadow-[4px_4px_0px_#000] mb-8 text-center">
                <div class="bg-white p-4 border-4 border-black rounded-xl shadow-[3px_3px_0px_#000]">
                    {!! $qrCode !!}
                </div>
                <h3 class="text-lg font-black uppercase tracking-tight mt-6 text-black">Scan QR Untuk Pengambilan</h3>
                <p class="text-xs font-bold text-gray-600 mt-1 max-w-sm">Tunjukkan QR Code ini ke petugas Snack Bar Fast Lane di bioskop terdekat.</p>
            </div>
            @else
            <div class="border-4 border-black bg-brand/20/10 p-6 rounded-2xl shadow-[4px_4px_0px_#000] mb-8 text-center">
                <x-icon name="heroicon-s-exclamation-triangle" class="w-12 h-12 text-accent-red mx-auto mb-3" />
                <h3 class="text-lg font-black uppercase text-black">QR Code Belum Tersedia</h3>
                <p class="text-xs font-bold text-gray-600 mt-1">Silakan selesaikan pembayaran terlebih dahulu untuk menampilkan QR Code penukaran.</p>
                <a href="{{ route('snacks.checkout.show', $snackOrder->id) }}" class="brutal-btn !py-2.5 !px-6 text-xs mt-4 inline-block bg-accent/20">
                    Bayar Sekarang
                </a>
            </div>
            @endif

            <div class="space-y-4">
                <h3 class="text-lg font-black uppercase tracking-tight text-black border-b-2 border-border/10 pb-2">Rincian Item</h3>

                <div class="space-y-3">
                    @foreach($snackOrder->items as $item)
                    <div class="flex items-center justify-between text-sm font-bold text-gray-800">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl shrink-0">{{ $item->snack_emoji }}</span>
                            <div>
                                <p class="text-black font-extrabold">{{ $item->snack_name }}</p>
                                <p class="text-[11px] text-gray-500 font-semibold">{{ $item->qty }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <span class="text-black font-extrabold">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t-4 border-dashed border-border/30 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-50">Total Pembayaran</p>
                        <p class="text-3xl font-price font-black text-accent-red mt-0.5">Rp {{ number_format($snackOrder->fnb_total, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-50">Metode Pengambilan</p>
                        <p class="text-xs font-extrabold text-black mt-1">Fast Lane</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

