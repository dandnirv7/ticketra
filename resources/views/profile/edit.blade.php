<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black leading-tight text-gray-900 uppercase tracking-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl space-y-6" x-data="{ activeTab: 'overview' }">
        
        <div class="flex overflow-x-auto whitespace-nowrap snap-x scroll-smooth pb-2 gap-2.5 border-b-[3px] border-border/10">
            <button @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'bg-pastel-mint text-border border-border shadow-[2px_2px_0px_rgba(0,0,0,1)] translate-x-[1px] translate-y-[1px]' : 'bg-white text-border hover:bg-slate-50 hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5'"
                class="px-4 py-2.5 rounded-xl border-2 border-border text-xs font-black uppercase tracking-wider transition-all focus:outline-none select-none snap-start shrink-0">
                Ringkasan
            </button>
            <button @click="activeTab = 'edit'" 
                :class="activeTab === 'edit' ? 'bg-pastel-mint text-border border-border shadow-[2px_2px_0px_rgba(0,0,0,1)] translate-x-[1px] translate-y-[1px]' : 'bg-white text-border hover:bg-slate-50 hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5'"
                class="px-4 py-2.5 rounded-xl border-2 border-border text-xs font-black uppercase tracking-wider transition-all focus:outline-none select-none snap-start shrink-0">
                Ubah Profil
            </button>
            <button @click="activeTab = 'security'" 
                :class="activeTab === 'security' ? 'bg-pastel-mint text-border border-border shadow-[2px_2px_0px_rgba(0,0,0,1)] translate-x-[1px] translate-y-[1px]' : 'bg-white text-border hover:bg-slate-50 hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5'"
                class="px-4 py-2.5 rounded-xl border-2 border-border text-xs font-black uppercase tracking-wider transition-all focus:outline-none select-none snap-start shrink-0">
                Keamanan
            </button>
            <button @click="activeTab = 'danger'" 
                :class="activeTab === 'danger' ? 'bg-accent-red/20 text-accent-red border-accent-red shadow-[2px_2px_0px_rgba(239,68,68,1)] translate-x-[1px] translate-y-[1px]' : 'bg-white text-border hover:bg-red-50 hover:border-accent-red hover:text-accent-red hover:shadow-[3px_3px_0px_rgba(239,68,68,1)] hover:-translate-y-0.5'"
                class="px-4 py-2.5 rounded-xl border-2 border-border text-xs font-black uppercase tracking-wider transition-all focus:outline-none select-none snap-start shrink-0">
                Zona Bahaya
            </button>
        </div>

        
        <div x-show="activeTab === 'overview'" x-transition.opacity class="space-y-6">
            
            <div class="brutal-card p-6 md:p-8 bg-white flex flex-col md:flex-row items-center gap-6 md:gap-8">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-accent-yellow border-[4px] border-border flex items-center justify-center text-5xl shadow-[4px_4px_0px_rgba(0,0,0,1)] select-none">
                        {{ $user->avatar_emoji ?? '🍿' }}
                    </div>
                </div>
                <div class="flex-1 text-center md:text-left space-y-2">
                    <span class="inline-block px-3.5 py-1.5 rounded-full border-2 border-border text-[10px] font-black uppercase tracking-wider {{ $membershipBadge }} shadow-[1px_1px_0px_rgba(0,0,0,1)]">
                        {{ $membership }}
                    </span>
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight">{{ $user->name }}</h3>
                    <div class="text-xs font-semibold text-gray-550 space-y-1.5 pt-1">
                        <p class="flex items-center justify-center md:justify-start gap-2">
                            <x-icon name="heroicon-s-envelope" class="w-4 h-4 text-gray-550 shrink-0" />
                            <span class="text-gray-700">{{ $user->email }}</span>
                        </p>
                        @if($user->phone)
                            <p class="flex items-center justify-center md:justify-start gap-2">
                                <x-icon name="heroicon-s-phone" class="w-4 h-4 text-gray-550 shrink-0" />
                                <span class="text-gray-700">{{ $user->phone }}</span>
                            </p>
                        @else
                            <p class="flex items-center justify-center md:justify-start gap-2 italic text-gray-400">
                                <x-icon name="heroicon-s-phone" class="w-4 h-4 text-gray-400 shrink-0" />
                                <span>Belum menambahkan nomor telepon</span>
                            </p>
                        @endif
                        <p class="flex items-center justify-center md:justify-start gap-2 text-gray-400 text-[10px] pt-0.5">
                            <x-icon name="heroicon-s-calendar" class="w-4 h-4 text-gray-400 shrink-0" />
                            <span>Bergabung sejak {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="border-[3px] border-border rounded-[20px] bg-pastel-mint p-5 shadow-[4px_4px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase text-border/60 tracking-wider">Total Transaksi</p>
                        <p class="text-3xl font-black text-border mt-1">{{ $bookingsCount }}</p>
                    </div>
                    <x-icon name="heroicon-s-shopping-bag" class="w-8 h-8 text-border mt-4 self-end" />
                </div>
                <div class="border-[3px] border-border rounded-[20px] bg-pastel-lemon p-5 shadow-[4px_4px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase text-border/60 tracking-wider">Tiket Sukses</p>
                        <p class="text-3xl font-black text-border mt-1">{{ $confirmedCount }}</p>
                    </div>
                    <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-border mt-4 self-end" />
                </div>
                <div class="border-[3px] border-border rounded-[20px] bg-pastel-lavender p-5 shadow-[4px_4px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase text-border/60 tracking-wider">Poin Loyalitas</p>
                        <p class="text-3xl font-black text-border mt-1">{{ $loyaltyPoints }} Pts</p>
                    </div>
                    <x-icon name="heroicon-s-sparkles" class="w-8 h-8 text-border mt-4 self-end" />
                </div>
            </div>

            
            <div class="border-[3px] border-border rounded-[20px] bg-white p-5 md:p-6 shadow-[4px_4px_0px_rgba(0,0,0,1)] space-y-4">
                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Riwayat Tiket Terbaru</h3>
                
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                            @php
                                $jadwal = $booking->jadwalTayang;
                                $film = $jadwal?->film;
                                $studio = $jadwal?->studio;
                                $bioskop = $studio?->bioskop;
                            @endphp
                            <div class="border-[3px] border-border rounded-xl p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                @if($film && $film->poster)
                                    <div class="w-16 h-24 border-2 border-border rounded-lg overflow-hidden shrink-0 shadow-[2px_2px_0px_rgba(0,0,0,1)]">
                                        <img src="{{ $film->poster }}" alt="{{ $film->title }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 text-[9px] font-bold border-2 border-border rounded bg-white">
                                            Order #{{ $booking->booking_id }}
                                        </span>
                                        @if($booking->status === 'confirmed')
                                            <span class="px-2 py-0.5 text-[9px] font-black border-2 border-border rounded bg-accent-green text-border uppercase tracking-wider">
                                                Lunas
                                            </span>
                                        @elseif($booking->status === 'pending_payment')
                                            <span class="px-2 py-0.5 text-[9px] font-black border-2 border-border rounded bg-accent-yellow text-border uppercase tracking-wider">
                                                Pending
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-[9px] font-black border-2 border-border rounded bg-accent-red/20 text-accent-red uppercase tracking-wider">
                                                {{ $booking->status }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="text-base font-black text-gray-900 uppercase tracking-tight">
                                        {{ $film?->title ?? 'N/A' }}
                                    </h4>
                                    <p class="text-xs font-semibold text-gray-500">
                                        📍 {{ $bioskop?->nama ?? 'N/A' }} &bull; {{ $studio?->nama ?? 'N/A' }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400">
                                        📅 {{ $jadwal?->waktu_mulai ? $jadwal->waktu_mulai->translatedFormat('d M Y - H:i') : 'N/A' }} WIB
                                    </p>
                                </div>
                                <div class="sm:text-right flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2">
                                    <p class="text-sm font-black text-accent-red">
                                        Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                                    </p>
                                    @if($booking->status === 'confirmed' || $booking->status === 'pending_payment')
                                        <a href="{{ route('bookings.show', $booking->id) }}" class="brutal-btn bg-accent-yellow !py-1.5 !px-3 text-[10px] shadow-[2px_2px_0px_rgba(0,0,0,1)] border-2 border-border rounded-lg select-none">
                                            Detail Tiket
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border-2 border-dashed border-border/20 rounded-xl p-8 text-center space-y-3">
                        <x-icon name="heroicon-s-film" class="w-10 h-10 text-gray-300 mx-auto" />
                        <p class="text-xs font-bold text-gray-400">Belum ada pemesanan tiket bioskop.</p>
                        <a href="{{ route('film.index') }}" class="brutal-btn bg-accent-yellow !py-2 !px-4 text-xs shadow-[2px_2px_0px_rgba(0,0,0,1)] border-2 border-border rounded-lg inline-block">
                            Cari Film Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>

        
        <div x-show="activeTab === 'edit'" x-transition.opacity class="brutal-card p-6 md:p-8 bg-white">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        
        <div x-show="activeTab === 'security'" x-transition.opacity class="brutal-card p-6 md:p-8 bg-white">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        
        <div x-show="activeTab === 'danger'" x-transition.opacity class="brutal-card p-6 md:p-8 bg-white border-accent-red/30">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
