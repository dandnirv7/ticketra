<x-app-layout>
    <div class="max-w-2xl mx-auto text-center px-4">
        <div class="brutal-box bg-pastel-mint p-6 sm:p-10 md:p-14">
            <div class="w-20 h-20 mx-auto mb-6 bg-white border-4 border-black rounded-full flex items-center justify-center shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                <x-icon name="heroicon-s-check-circle" class="w-10 h-10 text-emerald-600" />
            </div>

            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight mb-4">
                Pembayaran Berhasil!
            </h1>
            <p class="text-lg font-bold opacity-80 mb-8">
                Pesanan camilan <strong>{{ $snackOrder->order_id }}</strong> telah dibayar.
            </p>

            <div class="bg-white border-4 border-black rounded-2xl p-6 shadow-[4px_4px_0px_rgba(0,0,0,1)] mb-8 text-left space-y-3">
                @foreach ($snackOrder->items as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $item->snack_emoji }}</span>
                        <span class="font-bold">{{ $item->snack_name }} &times; {{ $item->qty }}</span>
                    </div>
                    <span class="font-extrabold">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="pt-3 mt-3 border-t-2 border-dashed border-border flex justify-between">
                    <span class="font-extrabold uppercase">Total</span>
                    <span class="font-price font-black text-xl text-accent-red">Rp {{ number_format($snackOrder->fnb_total, 0, ',', '.') }}</span>
                </div>
            </div>

            <p class="text-sm font-bold opacity-70 mb-8">
                Tunjukkan kode pesanan ini di Snack Bar Fast Lane untuk mengambil pesananmu.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('bookings.index') }}" class="brutal-btn !py-3 !px-8">
                    <x-icon name="heroicon-s-ticket" class="inline w-5 h-5 mr-2" />
                    Tiket Saya
                </a>
                <a href="{{ route('dashboard') }}" class="brutal-btn !py-3 !px-8 bg-white text-foreground hover:bg-gray-100">
                    <x-icon name="heroicon-s-home" class="inline w-5 h-5 mr-2" />
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
