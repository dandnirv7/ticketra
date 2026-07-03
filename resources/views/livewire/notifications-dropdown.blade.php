<div wire:poll.30s class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" 
            class="relative w-11 h-11 bg-secondary-background border-[3px] border-border rounded-full flex items-center justify-center hover:bg-pastel-lemon/20 transition-colors focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2">
        <x-icon name="heroicon-s-bell" class="w-5 h-5 text-border" />
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-accent-red border-2 border-border text-[10px] font-black text-white">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-transition.opacity
         class="absolute right-0 mt-3 w-80 bg-white border-[3px] border-border rounded-2xl shadow-[6px_6px_0px_rgba(0,0,0,1)] z-50 overflow-hidden text-xs font-bold text-foreground">
        
        <div class="flex items-center justify-between p-4 border-b-[3px] border-border bg-pastel-mint/30">
            <span class="text-sm font-black font-heading text-foreground">Notifikasi</span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-border hover:underline focus:outline-none">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="overflow-y-auto divide-y-2 max-h-64 divide-border/10 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
            @forelse($notifications as $notification)
                @php $notifUrl = data_get($notification->data, 'url'); @endphp
                <div @if($notifUrl)
                         @click.prevent="
                             $wire.markAsRead('{{ $notification->id }}').then(() => {
                                 window.location = '{{ $notifUrl }}';
                             });
                         "
                     @endif
                     class="flex items-start gap-2 p-4 transition-colors hover:bg-pastel-lemon/10{{ $notifUrl ? ' cursor-pointer' : '' }}">
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-heading font-black text-foreground text-[11px]">
                                {{ data_get($notification->data, 'title', 'Info Baru') }}
                            </span>
                            <span class="text-[9px] text-border/60 whitespace-nowrap">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-[11px] text-border/80 font-medium leading-snug">
                            {{ data_get($notification->data, 'message', '') }}
                        </p>
                        @if(data_get($notification->data, 'url'))
                            <a href="{{ data_get($notification->data, 'url') }}" 
                               @click.prevent="
                                   $wire.markAsRead('{{ $notification->id }}').then(() => {
                                       window.location = '{{ data_get($notification->data, 'url') }}';
                                   });
                               "
                               class="inline-block mt-2 text-[10px] text-indigo-600 hover:underline cursor-pointer">
                                Lihat Detail &rarr;
                            </a>
                        @else
                            <button wire:click="markAsRead('{{ $notification->id }}')" 
                                    class="inline-block mt-2 text-[10px] text-border/50 hover:text-foreground hover:underline">
                                Tandai dibaca
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center gap-2 p-6 font-medium text-center text-border/40">
                    <x-icon name="heroicon-o-bell-slash" class="w-8 h-8 opacity-40" />
                    <span>Tidak ada notifikasi baru</span>
                </div>
            @endforelse
        </div>
    </div>
</div>
