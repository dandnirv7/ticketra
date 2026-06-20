<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black leading-tight text-gray-900">
            Status Pembayaran
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-8">
        <div class="brutal-box bg-white p-8 md:p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 mb-6 border-4 border-border rounded-full shadow-[6px_6px_0px_0px_#000] bg-pastel-mint">
                <x-icon name="heroicon-s-check" class="w-12 h-12 text-accent-green" />
            </div>

            <h1 class="mb-3 text-3xl uppercase md:text-4xl">Terima Kasih!</h1>
            <p class="mb-8 text-base font-medium opacity-80 md:text-lg">
                Pembayaran Anda telah berhasil diproses. E-ticket akan dikirim ke email Anda.
            </p>

            <div class="brutal-box bg-pastel-lemon p-5 mb-8 text-left">
                <p class="text-[10px] font-extrabold uppercase opacity-50 tracking-widest">ID Booking</p>
                <p class="mt-1 text-2xl font-black tracking-wider md:text-3xl font-mono">{{ $booking->booking_id }}</p>
            </div>

            <div class="flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('dashboard') }}" class="brutal-btn !py-3 !px-6 text-sm justify-center">
                    Kembali ke Dashboard
                    <x-icon name="heroicon-s-arrow-right" class="w-4 h-4" />
                </a>
                <a href="{{ route('bookings.show', $booking->id) }}" class="brutal-btn brutal-btn-secondary !py-3 !px-6 text-sm justify-center">
                    Lihat E-Ticket
                    <x-icon name="heroicon-s-document-duplicate" class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
