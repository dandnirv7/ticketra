<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Form Scan Input -->
        <div class="p-6 bg-white dark:bg-gray-900 shadow rounded-xl border border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Scan QR / Masukkan Kode Booking</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Gunakan barcode scanner atau ketik Kode Booking (misal: TK-XXXXX) untuk verifikasi di pintu masuk.</p>

            <form wire:submit.prevent="processScan" class="flex gap-3">
                <div class="relative flex-1">
                    <input
                        type="text"
                        wire:model="searchCode"
                        placeholder="Ketik atau Scan Kode Booking (contoh: TK-123456)..."
                        class="w-full px-4 py-3 text-lg font-mono tracking-wider rounded-lg border-2 border-primary-500 focus:ring-4 focus:ring-primary-200 dark:bg-gray-800 dark:text-white dark:border-primary-600 uppercase"
                        autofocus
                    />
                </div>
                <x-filament::button type="submit" size="lg" icon="heroicon-m-magnifying-glass">
                    Verifikasi
                </x-filament::button>
                @if($scannedBooking)
                    <x-filament::button type="button" color="gray" size="lg" wire:click="resetScan">
                        Reset
                    </x-filament::button>
                @endif
            </form>
        </div>

        <!-- Result Card -->
        @if($scannedBooking)
            <div class="p-6 bg-white dark:bg-gray-900 shadow-lg rounded-xl border-2 {{ $scannedBooking->is_checked_in ? 'border-amber-500' : ($scannedBooking->status === 'confirmed' ? 'border-emerald-500' : 'border-rose-500') }}">

                <!-- Status Header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Kode Booking</span>
                        <h2 class="text-3xl font-black font-mono text-gray-900 dark:text-white">{{ $scannedBooking->booking_id }}</h2>
                    </div>
                    <div>
                        @if($scannedBooking->is_checked_in)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                <x-heroicon-m-check-badge class="w-5 h-5 text-amber-600" />
                                SUDAH CHECK-IN ({{ $scannedBooking->checked_in_at?->format('H:i') }})
                            </span>
                        @elseif($scannedBooking->status === 'confirmed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <x-heroicon-m-ticket class="w-5 h-5 text-emerald-600" />
                                TIKET VALID (SIAP ENTER)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                <x-heroicon-m-x-circle class="w-5 h-5 text-rose-600" />
                                STATUS: {{ strtoupper($scannedBooking->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Film</span>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $scannedBooking->jadwalTayang?->film?->judul ?? '-' }}</p>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Bioskop & Studio</span>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                            {{ $scannedBooking->jadwalTayang?->studio?->bioskop?->nama ?? '-' }}
                        </p>
                        <p class="text-sm font-medium text-primary-600 dark:text-primary-400">
                            {{ $scannedBooking->jadwalTayang?->studio?->nama ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Waktu Tayang</span>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                            {{ $scannedBooking->jadwalTayang?->waktu_mulai?->format('d M Y') }}
                        </p>
                        <p class="text-base font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $scannedBooking->jadwalTayang?->waktu_mulai?->format('H:i') }} WIB
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Pelanggan</span>
                        <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $scannedBooking->user?->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $scannedBooking->user?->email }}</p>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Daftar Kursi</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($scannedBooking->statusKursis as $sk)
                                <span class="px-2 py-0.5 text-xs font-bold rounded bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300">
                                    {{ $sk->kursi?->nomor_kursi }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Total Bayar</span>
                        <p class="text-lg font-black text-gray-900 dark:text-white mt-1">
                            Rp {{ number_format($scannedBooking->total_price + $scannedBooking->service_fee + $scannedBooking->fnb_total - $scannedBooking->discount_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Action Button -->
                @if(!$scannedBooking->is_checked_in && $scannedBooking->status === 'confirmed')
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                        <x-filament::button
                            wire:click="confirmCheckin"
                            color="success"
                            size="xl"
                            icon="heroicon-m-check-circle"
                        >
                            KONFIRMASI MASUK / CHECK-IN SEKARANG
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
