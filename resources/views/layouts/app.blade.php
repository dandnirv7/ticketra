<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <x-favicons />

    @yield('seo')
    
    <link href="https:

    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $user = auth()->user();
    $avatarInitial = strtoupper(substr($user?->name ?? 'A', 0, 1));

    $isActive = fn($route) => request()->routeIs($route);
    $linkClass = fn($routes) => collect($routes)->contains(fn($r) => request()->routeIs($r))
        ? 'flex items-center gap-3 px-4 py-3 bg-pastel-mint border-[3px] border-border rounded-xl font-bold shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] transition-all focus:outline-none'
        : 'flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none';

    $bottomActive = 'flex flex-col items-center justify-center gap-1 text-[10px] font-black text-black bg-pastel-mint border-[3px] border-black rounded-xl p-1.5 shadow-[2px_2px_0px_rgba(0,0,0,1)] transition-all';
    $bottomInactive = 'flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-gray-700 hover:text-black border-[3px] border-transparent p-1.5 transition-all';
@endphp

<body class="font-base min-h-screen"
      x-data="{
          selectedLocation: 'Jakarta',
          showLocationDropdown: false,
      }">

    <div class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 lg:p-8 min-h-screen max-w-[1600px] mx-auto pb-24 lg:pb-8">

        
        <aside class="hidden lg:block lg:w-64 lg:shrink-0">
            <div class="lg:sticky lg:top-8 border-[3px] border-border rounded-[24px] bg-secondary-background p-5 space-y-6 shadow-[6px_6px_0px_rgba(0,0,0,1)]">

                
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-2xl font-heading font-extrabold tracking-tight">
                    <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-accent-green" />
                    Ticketra<span class="text-accent-green">.</span>
                </a>

                
                <nav class="flex flex-col gap-1.5 text-sm font-bold tracking-wide">
                    <a href="{{ route('dashboard') }}" class="{{ $linkClass(['dashboard']) }}">
                        <x-icon name="heroicon-s-home" class="w-5 h-5 text-border" />
                        Beranda
                    </a>
                    <a href="#" class="{{ $linkClass(['bioskop.*']) }}">
                        <x-icon name="heroicon-s-building-storefront" class="w-5 h-5 text-border" />
                        Bioskop
                    </a>
                    <a href="{{ route('film.index') }}" class="{{ $linkClass(['film.*']) }}">
                        <x-icon name="heroicon-s-film" class="w-5 h-5 text-border" />
                        Film
                    </a>
                    <a href="{{ route('bookings.index') }}" class="{{ $linkClass(['bookings.*']) }}">
                        <x-icon name="heroicon-s-ticket" class="w-5 h-5 text-border" />
                        Tiket Saya
                    </a>
                    <a href="#" class="{{ $linkClass(['promo.*']) }}">
                        <x-icon name="heroicon-s-tag" class="w-5 h-5 text-border" />
                        Promo
                    </a>
                    <a href="{{ route('snacks.index') }}" class="{{ $linkClass(['snacks.*']) }}">
                        <x-icon name="heroicon-s-shopping-bag" class="w-5 h-5 text-border" />
                        Snack Bar
                    </a>
                    <a href="#" class="{{ $linkClass(['wishlist.*']) }}">
                        <x-icon name="heroicon-s-heart" class="w-5 h-5 text-border" />
                        Wishlist
                    </a>
                    <a href="{{ route('profile.edit') }}" class="{{ $linkClass(['profile.*']) }}">
                        <x-icon name="heroicon-s-user" class="w-5 h-5 text-border" />
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-red-50 hover:border-accent-red hover:text-accent-red hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-accent-red focus:ring-offset-2 text-left font-bold mt-2">
                            <x-icon name="heroicon-s-arrow-left-on-rectangle" class="w-5 h-5 text-border group-hover:text-accent-red" />
                            Keluar
                        </button>
                    </form>
                </nav>

                
                <div class="border-[3px] border-border rounded-[20px] bg-pastel-lavender p-4 relative overflow-hidden shadow-[3px_3px_0px_var(--border)]">
                    <div class="pr-12">
                        <p class="text-sm font-extrabold text-foreground">Hi, {{ explode(' ', $user?->name ?? 'Andi')[0] }}!</p>
                        <p class="text-[11px] font-medium text-foreground/80 mt-1 leading-snug">Selamat datang di Ticketra.</p>
                    </div>
                    <x-icon name="heroicon-s-ticket" class="absolute right-2 bottom-2 w-8 h-8 text-border/25 transform rotate-12" />
                </div>
            </div>
        </aside>

        
        <div class="flex-1 min-w-0 flex flex-col gap-4">

            
            <header class="flex items-center justify-between gap-3 py-2 px-1">

                <button @click="$dispatch('toggle-mobile-menu')"
                        class="lg:hidden w-10 h-10 bg-secondary-background border-[3px] border-border rounded-xl flex items-center justify-center hover:bg-pastel-lemon/20 transition-colors shrink-0">
                    <x-icon name="heroicon-s-bars-3" class="w-5 h-5 text-border" />
                </button>

                <div class="flex-1">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="text-xl font-heading font-black text-foreground">Ticketra.</h2>
                    @endisset
                </div>

                <div class="flex items-center justify-end gap-3 shrink-0">
                    
                    <div class="relative">
                        <button @click="showLocationDropdown = !showLocationDropdown" @click.away="showLocationDropdown = false"
                                class="flex items-center gap-2 px-4 py-2.5 bg-secondary-background border-[3px] border-border rounded-xl text-xs font-bold whitespace-nowrap shadow-none hover:shadow-[3px_3px_0px_var(--border)] hover:-translate-y-0.5 hover:bg-pastel-lemon/20 transition-all focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                            <x-icon name="heroicon-s-map-pin" class="w-4 h-4 text-border" />
                            <span x-text="selectedLocation">Jakarta</span>
                            <x-icon name="heroicon-s-chevron-down" class="w-3 h-3 text-border" />
                        </button>
                        <div x-show="showLocationDropdown" x-transition.opacity
                             class="absolute right-0 mt-2 w-44 bg-white border-[3px] border-border rounded-xl shadow-[4px_4px_0px_var(--border)] z-50 py-1 text-xs font-bold text-foreground">
                            <template x-for="loc in ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi']">
                                <button @click="selectedLocation = loc; showLocationDropdown = false"
                                        class="w-full text-left px-4 py-2.5 hover:bg-pastel-mint/30 transition-colors border-b-2 border-border/10 last:border-b-0"
                                        x-text="loc">
                                </button>
                            </template>
                        </div>
                    </div>

                    
                    <button class="relative w-11 h-11 bg-secondary-background border-[3px] border-border rounded-full flex items-center justify-center hover:bg-pastel-lemon/20 transition-colors focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
                        <x-icon name="heroicon-s-bell" class="w-5 h-5 text-border" />
                        <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-accent-red rounded-full border-2 border-border"></span>
                    </button>

                    
                    <a href="{{ route('profile.edit') }}" class="w-11 h-11 bg-accent-yellow border-[3px] border-border rounded-full flex items-center justify-center text-xl hover:scale-105 transition-transform focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2 select-none">
                        {{ $user->avatar_emoji ?? '🍿' }}
                    </a>
                </div>
            </header>

            
            <main class="flex-1 border-[3px] border-border rounded-[24px] bg-slate-50 p-6 md:p-8 space-y-6 shadow-[8px_8px_0px_var(--border)] overflow-y-auto min-h-0">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-mobile-nav :bottomActive="$bottomActive" :bottomInactive="$bottomInactive" />
</body>
</html>


