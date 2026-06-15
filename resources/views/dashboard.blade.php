<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-amber-50">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="p-6 mb-6 neo-card">
                <h3 class="text-xl neo-title">Selamat Datang, {{ Auth::user()->name }}!</h3>
                <p class="mt-2 neo-text-sm">Siap nonton film hari ini?</p>
                <a href="{{ route('jadwal.index') }}" class="inline-block mt-4 neo-button-primary">
                    Cari Film <x-heroicon-o-film class="inline w-4 h-4 ml-1" />
                </a>
            </div>

            @php
            $upcomingBookings = \App\Models\Booking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->whereHas('jadwalTayang', function($q) {
            $q->where('waktu_mulai', '>', now());
            })
            ->with(['jadwalTayang.film', 'jadwalTayang.studio.bioskop', 'statusKursis'])
            ->orderBy('jadwal_tayang_id')
            ->limit(3)
            ->get();
            @endphp

            @if($upcomingBookings->count() > 0)
            <div class="p-6 mb-6 neo-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="flex items-center gap-2 text-xl neo-title">
                        <x-heroicon-o-ticket class="w-6 h-6" />
                        Tiket Akan Datang
                    </h3>
                    <a href="{{ route('bookings.index') }}" class="text-sm neo-button-secondary">
                        Lihat Semua
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($upcomingBookings as $booking)
                    <a href="{{ route('bookings.show', $booking->id) }}"
                        class="block neo-card-sm p-4 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000] transition-all">

                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-lg font-black">{{ $booking->jadwalTayang->film->judul }}</h4>
                                <div class="flex flex-wrap gap-3 mt-1 text-sm">
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-o-building-storefront class="w-4 h-4" />
                                        {{ $booking->jadwalTayang->studio->bioskop->nama }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-o-calendar class="w-4 h-4" />
                                        {{ $booking->jadwalTayang->waktu_mulai->format('d M Y, H:i') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-o-ticket class="w-4 h-4" />
                                        {{ $booking->statusKursis->count() }} Kursi
                                    </span>
                                </div>
                            </div>
                            <x-heroicon-o-arrow-right class="w-6 h-6 text-gray-400" />
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <a href="{{ route('bookings.index') }}" class="neo-card-sm p-4 bg-green-100 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000] transition-all">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-300 neo-icon">
                            <x-heroicon-o-ticket class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="neo-subtitle">Total Transaksi</p>
                            <p class="text-3xl font-black">{{ \App\Models\Booking::where('user_id', Auth::id())->count() }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('bookings.index') }}" class="neo-card-sm p-4 bg-blue-100 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000] transition-all">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-300 neo-icon">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="neo-subtitle">Film Ditonton</p>
                            <p class="text-3xl font-black">
                                {{ \App\Models\Booking::where('user_id', Auth::id())
                                    ->where('status', 'confirmed')
                                    ->whereHas('jadwalTayang', fn($q) => $q->where('waktu_mulai', '<', now()))
                                    ->count() }}
                            </p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('bookings.index') }}" class="neo-card-sm p-4 bg-yellow-100 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000] transition-all">
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-300 neo-icon">
                            <x-heroicon-o-currency-dollar class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="neo-subtitle">Total Spending</p>
                            <p class="text-2xl font-black">
                                Rp {{ number_format(\App\Models\Booking::where('user_id', Auth::id())
                                    ->where('status', 'confirmed')
                                    ->sum('total_price'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
