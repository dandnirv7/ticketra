<x-filament-panels::page>
    <style>
        .dashboard-content,
        .dashboard-content * {
            --tw-text-opacity: 1;
        }
        .dashboard-content .fi-wi-chart {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .dashboard-content .fi-wi-chart .fi-section {
            border: none !important;
            box-shadow: none !important;
        }
        .dashboard-content .fi-wi-table .fi-section {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
    </style>

    <div class="space-y-6 dashboard-content">
        <div class="flex justify-end items-center">
            <div class="inline-flex gap-2 items-center bg-white rounded-xl border border-gray-100 shadow-sm p-1.5 dark:bg-white">
                <span class="text-[10px] font-bold text-gray-400 pl-2.5 uppercase tracking-wide">Periode:</span>
                <select wire:model.live="filter" class="text-xs font-extrabold text-gray-700 bg-transparent border-none focus:ring-0 focus:outline-none cursor-pointer pr-8 py-1 rounded-lg hover:text-amber-600 transition-colors">
                    <option value="today">Hari Ini</option>
                    <option value="7_days">7 Hari Terakhir</option>
                    <option value="1_month">1 Bulan Terakhir</option>
                    <option value="1_year">1 Tahun Terakhir</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="flex gap-4 items-center p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-white">
                <div class="flex justify-center items-center w-14 h-14 bg-blue-100 rounded-full shrink-0">
                    <x-heroicon-o-users class="w-7 h-7 text-blue-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Okupansi {{ $periodLabel }}</p>
                    <h3 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-gray-900">{{ $occupancyPercent }}%</h3>
                    <p class="flex gap-1 items-center mt-0.5 text-xs font-semibold text-red-500">
                        <span>👥</span>
                        <span>{{ $occupiedSeats }} / {{ $totalSeats }} kursi terisi</span>
                    </p>
                </div>
                <button class="text-gray-300 hover:text-gray-500 shrink-0">
                    <x-heroicon-m-ellipsis-vertical class="w-5 h-5" />
                </button>
            </div>

            <div class="flex gap-4 items-center p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-white">
                <div class="flex justify-center items-center w-14 h-14 bg-purple-100 rounded-full shrink-0">
                    <x-heroicon-o-calendar-days class="w-7 h-7 text-purple-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Jadwal Tayang {{ $periodLabel }}</p>
                    <h3 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-gray-900">{{ $jadwalsCount }}</h3>
                    <p class="flex gap-1 items-center mt-0.5 text-xs font-semibold text-blue-600">
                        <span>🎬</span>
                        <span>Across {{ $studiosCount }} studio</span>
                    </p>
                </div>
                <button class="text-gray-300 hover:text-gray-500 shrink-0">
                    <x-heroicon-m-ellipsis-vertical class="w-5 h-5" />
                </button>
            </div>

            <div class="flex gap-4 items-center p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-white">
                <div class="flex justify-center items-center w-14 h-14 bg-emerald-100 rounded-full shrink-0">
                    <x-heroicon-o-ticket class="w-7 h-7 text-emerald-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Booking Paid {{ $periodLabel }}</p>
                    <h3 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-gray-900">{{ $paymentOkToday }}</h3>
                    <p class="flex gap-1 items-center mt-0.5 text-xs font-semibold text-emerald-600">
                        <span>✅</span>
                        <span>{{ $paymentPendingToday }} masih pending</span>
                    </p>
                </div>
                <button class="text-gray-300 hover:text-gray-500 shrink-0">
                    <x-heroicon-m-ellipsis-vertical class="w-5 h-5" />
                </button>
            </div>

            <div class="flex gap-4 items-center p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-white">
                <div class="flex justify-center items-center w-14 h-14 bg-orange-100 rounded-full shrink-0">
                    <x-heroicon-o-banknotes class="w-7 h-7 text-orange-500" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Revenue {{ $periodLabel }}</p>
                    <h3 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-gray-900">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
                    <p class="flex gap-1 items-center mt-0.5 text-xs font-semibold text-orange-500">
                        <span>💳</span>
                        <span>Total pembayaran sukses</span>
                    </p>
                </div>
                <button class="text-gray-300 hover:text-gray-500 shrink-0">
                    <x-heroicon-m-ellipsis-vertical class="w-5 h-5" />
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-4">
            <div class="overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm xl:col-span-3 dark:bg-white">
                @livewire(\App\Filament\Widgets\BookingTrendChartWidget::class)
            </div>

            <div class="flex flex-col p-5 bg-white rounded-2xl border border-gray-100 shadow-sm xl:col-span-1 dark:bg-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold tracking-wider text-gray-400 uppercase">Revenue {{ $periodLabel }}</p>
                        <h3 class="mt-1 text-2xl font-extrabold leading-tight text-gray-900 dark:text-gray-900">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
                        <p class="mt-1 text-xs font-semibold text-emerald-500">{{ $paymentOkToday }} pembayaran sukses</p>
                    </div>
                    <img src="{{ asset('images/wallet_illustration.png') }}" alt="Wallet" class="object-contain -mt-2 -mr-2 w-20 h-20">
                </div>

                <div class="flex-1 mt-5 space-y-3.5">
                    <div>
                        <div class="flex justify-between items-center mb-1 text-xs">
                            <span class="font-bold text-gray-600">Transfer</span>
                            <div class="flex gap-6 items-center">
                                <span class="font-semibold tabular-nums text-gray-400">{{ $transferPercent }}%</span>
                                <span class="font-bold tabular-nums text-gray-800">Rp {{ number_format($transferRevenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full">
                            <div class="h-2 bg-blue-500 rounded-full transition-all duration-500" style="width: {{ $transferPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1 text-xs">
                            <span class="font-bold text-gray-600">QRIS</span>
                            <div class="flex gap-6 items-center">
                                <span class="font-semibold tabular-nums text-gray-400">{{ $qrisPercent }}%</span>
                                <span class="font-bold tabular-nums text-gray-800">Rp {{ number_format($qrisRevenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full">
                            <div class="h-2 bg-gray-300 rounded-full transition-all duration-500" style="width: {{ max($qrisPercent, 2) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1 text-xs">
                            <span class="font-bold text-gray-600">VA</span>
                            <div class="flex gap-6 items-center">
                                <span class="font-semibold tabular-nums text-gray-400">{{ $vaPercent }}%</span>
                                <span class="font-bold tabular-nums text-gray-800">Rp {{ number_format($vaRevenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full">
                            <div class="h-2 bg-gray-300 rounded-full transition-all duration-500" style="width: {{ max($vaPercent, 2) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 mt-4 text-right border-t border-gray-100">
                    <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}" class="inline-flex gap-1 items-center text-xs font-bold text-blue-600 hover:text-blue-700">
                        Lihat detail pembayaran
                        <x-heroicon-m-arrow-right class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
            <div class="overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm xl:col-span-8 dark:bg-white">
                @livewire(\App\Filament\Widgets\BookingAttentionWidget::class)
            </div>

            <div class="flex flex-col p-5 bg-white rounded-2xl border border-gray-100 shadow-sm xl:col-span-4 dark:bg-white">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-900">Aktivitas Terbaru</h3>
                    <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}" class="text-[11px] font-semibold text-gray-400 hover:text-gray-600 inline-flex items-center gap-0.5">
                        Lihat semua
                        <x-heroicon-m-chevron-right class="w-3 h-3" />
                    </a>
                </div>

                <div class="flex-1 space-y-4">
                    @forelse ($activityItems as $item)
                        <div class="flex gap-3 items-start">
                            <div class="mt-0.5 shrink-0">
                                @if ($item['icon'] === 'check')
                                    <div class="flex justify-center items-center w-8 h-8 bg-emerald-500 rounded-full">
                                        <x-heroicon-o-check class="w-4 h-4 text-white" />
                                    </div>
                                @elseif ($item['icon'] === 'lock')
                                    <div class="flex justify-center items-center w-8 h-8 bg-blue-500 rounded-full">
                                        <x-heroicon-o-lock-closed class="w-4 h-4 text-white" />
                                    </div>
                                @else
                                    <div class="flex justify-center items-center w-8 h-8 bg-red-400 rounded-full">
                                        <x-heroicon-o-clock class="w-4 h-4 text-white" />
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-800 truncate dark:text-gray-800">{{ $item['title'] }}</p>
                                <p class="text-[10px] text-gray-400 font-medium truncate">{{ $item['subtitle'] }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap shrink-0 pt-0.5 tabular-nums">{{ $item['time_formatted'] }}</span>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="text-xs text-gray-400">Belum ada aktivitas 24 jam terakhir</p>
                        </div>
                    @endforelse
                </div>

                @if (count($activityItems) > 0)
                    <div class="pt-3 mt-4 text-right border-t border-gray-100">
                        <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}" class="inline-flex gap-1 items-center text-xs font-bold text-blue-600 hover:text-blue-700">
                            Lihat semua aktivitas
                            <x-heroicon-m-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>


