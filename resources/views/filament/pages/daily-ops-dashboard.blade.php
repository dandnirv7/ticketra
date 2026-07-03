<x-filament-panels::page>
    <div class="space-y-6" x-data="{ tab: 'today' }">
        <!-- Date Filter -->
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Operasional Harian</h1>
            <div class="w-56">
                <form wire:submit.prevent="refresh">
                    {{ $this->form }}
                </form>
            </div>
        </div>

        @php $stats = $this->getTodayStats(); @endphp

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Tiket Terjual</p>
                <p class="text-3xl font-black mt-1 text-gray-900 dark:text-white">{{ $stats['total_tickets'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total tiket confirmed</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Revenue</p>
                <p class="text-2xl font-black mt-1 text-emerald-600 dark:text-emerald-400">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Tiket + F&B - Diskon</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Okupansi</p>
                <p class="text-3xl font-black mt-1 {{ $stats['occupancy_rate'] > 70 ? 'text-amber-600' : 'text-blue-600' }}">
                    {{ $stats['occupancy_rate'] }}%
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['booked_seats'] }}/{{ $stats['total_seats'] }} kursi terisi</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pesanan F&B</p>
                <p class="text-3xl font-black mt-1 text-gray-900 dark:text-white">{{ $stats['total_dine_in'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($stats['fnb_revenue'], 0, ',', '.') }} revenue</p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Antrean Dapur</p>
                <p class="text-3xl font-black mt-1 text-rose-600">{{ $this->getPendingKitchenOrders() }}</p>
                <p class="text-xs text-gray-500 mt-1">Perlu disiapkan</p>
            </div>
        </div>

        <!-- Tabs: Jadwal vs Check-in -->
        <div class="flex gap-2 mb-2">
            <button @click="tab = 'today'" :class="tab === 'today' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-800'" class="px-4 py-2 rounded-lg text-sm font-bold transition">Jadwal Tayang</button>
            <button @click="tab = 'checkin'" :class="tab === 'checkin' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-800'" class="px-4 py-2 rounded-lg text-sm font-bold transition">Check-in Terbaru</button>
        </div>

        <!-- Tab: Jadwal Tayang Hari Ini -->
        <div x-show="tab === 'today'" class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-800">
                <h2 class="font-bold text-lg flex items-center gap-2">
                    <x-heroicon-m-calendar-days class="w-5 h-5 text-primary-500" />
                    Jadwal Tayang Hari Ini
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-400">
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">Film</th>
                            <th class="px-5 py-3">Bioskop</th>
                            <th class="px-5 py-3">Studio</th>
                            <th class="px-5 py-3">Harga</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->getUpcomingShowtimes() as $jadwal)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                <td class="px-5 py-3 font-bold">{{ \Carbon\Carbon::parse($jadwal['waktu_mulai'])->format('H:i') }}</td>
                                <td class="px-5 py-3 font-semibold">{{ $jadwal['film']['judul'] ?? '-' }}</td>
                                <td class="px-5 py-3">{{ $jadwal['studio']['bioskop']['nama'] ?? '-' }}</td>
                                <td class="px-5 py-3">{{ $jadwal['studio']['nama'] ?? '-' }}</td>
                                <td class="px-5 py-3 font-bold">Rp {{ number_format((float)($jadwal['harga'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $start = \Carbon\Carbon::parse($jadwal['waktu_mulai']);
                                        $isPast = $start->isPast();
                                        $isSoon = $start->diffInMinutes(now()) <= 15 && !$isPast;
                                    @endphp
                                    @if($isPast)
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">Sudah Tayang</span>
                                    @elseif($isSoon)
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Segera Mulai</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Akan Datang</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400">Tidak ada jadwal tayang hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Check-in Terbaru -->
        <div x-show="tab === 'checkin'" class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-800">
                <h2 class="font-bold text-lg flex items-center gap-2">
                    <x-heroicon-m-check-circle class="w-5 h-5 text-emerald-500" />
                    Check-in Terbaru Hari Ini
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-400">
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">Kode Booking</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Film</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->getRecentCheckedIn() as $checkin)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                <td class="px-5 py-3 font-bold">{{ \Carbon\Carbon::parse($checkin['checked_in_at'])->format('H:i:s') }}</td>
                                <td class="px-5 py-3 font-mono font-bold text-primary-600">{{ $checkin['booking_id'] }}</td>
                                <td class="px-5 py-3">{{ $checkin['user']['name'] ?? '-' }}</td>
                                <td class="px-5 py-3">{{ $checkin['jadwal_tayang']['film']['judul'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada check-in hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
