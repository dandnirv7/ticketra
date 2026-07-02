@props([
    'bottomActive' => '',
    'bottomInactive' => '',
])

<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-surface border-t-4 border-black px-4 py-2 flex items-center justify-between shadow-[0px_-4px_10px_rgba(0,0,0,0.05)]">
    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? $bottomActive : $bottomInactive }} flex-1">
        <x-icon name="heroicon-s-home" class="w-5 h-5 text-border" />
        <span>Beranda</span>
    </a>
    <a href="{{ route('snacks.index') }}"
       class="{{ request()->routeIs('snacks.*') ? $bottomActive : $bottomInactive }} flex-1">
        <x-icon name="heroicon-s-shopping-bag" class="w-5 h-5 text-border" />
        <span>Snacks</span>
    </a>
    <a href="{{ route('bookings.index') }}"
       class="{{ request()->routeIs('bookings.*') ? $bottomActive : $bottomInactive }} flex-1">
        <x-icon name="heroicon-s-ticket" class="w-5 h-5 text-border" />
        <span>Tiket</span>
    </a>
    <a href="{{ route('profile.edit') }}"
       class="{{ request()->routeIs('profile.*') ? $bottomActive : $bottomInactive }} flex-1">
        <x-icon name="heroicon-s-user" class="w-5 h-5 text-border" />
        <span>Profile</span>
    </a>
</div>

<div x-data="{ open: false }"
     x-on:toggle-mobile-menu.window="open = !open"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex justify-start"
     style="display: none;">

    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="w-80 max-w-full bg-surface border-r-4 border-black h-full p-6 flex flex-col justify-between shadow-2xl relative overflow-y-auto">

        <div class="space-y-6">
            <div class="flex items-center justify-between border-b-4 border-black pb-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xl font-heading font-extrabold tracking-tight">
                    <x-icon name="heroicon-s-ticket" class="w-7 h-7 text-accent-green" />
                    Ticketra<span class="text-accent-green">.</span>
                </a>
                <button @click="open = false" class="p-2 border-2 border-black bg-white hover:bg-red-50 rounded-xl transition-colors">
                    <x-icon name="heroicon-s-x-mark" class="w-5 h-5" />
                </button>
            </div>

            @php $user = auth()->user(); @endphp
            <div class="border-[3px] border-border rounded-[20px] bg-brand/20 p-4 relative overflow-hidden shadow-[3px_3px_0px_var(--border)]">
                <div class="pr-12">
                    <p class="text-sm font-extrabold text-foreground">Hi, {{ explode(' ', $user->name ?? 'User')[0] }}!</p>
                    <p class="text-[11px] font-medium text-foreground/80 mt-1 leading-snug">Kelola akun dan cek promo menarik.</p>
                </div>
                <x-icon name="heroicon-s-ticket" class="absolute right-2 bottom-2 w-8 h-8 text-border/25 transform rotate-12" />
            </div>

            <nav class="flex flex-col gap-2 text-sm font-bold tracking-wide">
                <a href="{{ route('dashboard') }}" @click="open = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none">
                    <x-icon name="heroicon-s-home" class="w-5 h-5 text-border" />
                    Beranda
                </a>
                <a href="{{ route('bookings.index') }}" @click="open = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none">
                    <x-icon name="heroicon-s-ticket" class="w-5 h-5 text-border" />
                    Tiket Saya
                </a>
                <a href="{{ route('snacks.index') }}" @click="open = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none">
                    <x-icon name="heroicon-s-shopping-bag" class="w-5 h-5 text-border" />
                    Snack Bar
                </a>
                <a href="{{ route('profile.edit') }}" @click="open = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl border-[3px] border-transparent hover:bg-white hover:border-border hover:shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-all focus:outline-none">
                    <x-icon name="heroicon-s-user" class="w-5 h-5 text-border" />
                    Profil
                </a>
            </nav>
        </div>

        <div class="pt-6 border-t-2 border-dashed border-border mt-8">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-brand/20 border-[3px] border-black rounded-xl font-bold shadow-[3px_3px_0px_rgba(0,0,0,1)] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] transition-all">
                    <x-icon name="heroicon-s-arrow-left-on-rectangle" class="w-5 h-5 text-border" />
                    Keluar Akun
                </button>
            </form>
        </div>
    </div>
</div>

