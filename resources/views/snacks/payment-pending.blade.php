<x-app-layout>
    <div class="max-w-2xl mx-auto text-center px-4">
        <div class="brutal-box bg-accent/20 p-6 sm:p-10 md:p-14">
            <div class="w-20 h-20 mx-auto mb-6 bg-white border-4 border-black rounded-full flex items-center justify-center shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                <x-icon name="heroicon-s-clock" class="w-10 h-10 text-amber-600" />
            </div>

            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight mb-4">
                Menunggu Pembayaran
            </h1>
            <p class="text-lg font-bold opacity-80 mb-4">
                Pesanan camilan <strong>{{ $snackOrder->order_id }}</strong> sedang menunggu pembayaran.
            </p>
            <p class="text-sm font-medium opacity-70 mb-8">
                Silakan selesaikan pembayaran melalui popup yang sudah terbuka. Jika popup tertutup, kamu bisa kembali ke halaman checkout untuk membayar ulang.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('snacks.checkout.show', $snackOrder->id) }}" class="brutal-btn !py-3 !px-8">
                    <x-icon name="heroicon-s-credit-card" class="inline w-5 h-5 mr-2" />
                    Kembali ke Pembayaran
                </a>
                <a href="{{ route('dashboard') }}" class="brutal-btn !py-3 !px-8 bg-white text-foreground hover:bg-gray-100">
                    <x-icon name="heroicon-s-home" class="inline w-5 h-5 mr-2" />
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
