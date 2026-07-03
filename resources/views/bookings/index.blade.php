<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-black leading-tight text-gray-900">
      Riwayat Transaksi
    </h2>
  </x-slot>

  <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
    <div class="p-4 bg-[#E8FDF5] neo-card-sm">
      <div class="flex items-center gap-3">
        <div class="bg-secondary neo-icon">
          <x-heroicon-o-ticket class="w-6 h-6" />
        </div>
        <div>
          <p class="neo-subtitle">Akan Datang</p>
          <p class="text-3xl font-black">{{ $stats['upcoming'] }}</p>
        </div>
      </div>
    </div>

    <div class="p-4 bg-[#F1EEFE] neo-card-sm">
      <div class="flex items-center gap-3">
        <div class="bg-primary/20 neo-icon">
          <x-heroicon-o-check-circle class="w-6 h-6" />
        </div>
        <div>
          <p class="neo-subtitle">Selesai</p>
          <p class="text-3xl font-black">{{ $stats['completed'] }}</p>
        </div>
      </div>
    </div>

    <div class="p-4 bg-[#FFF1F2] neo-card-sm">
      <div class="flex items-center gap-3">
        <div class="bg-accent-red/20 neo-icon">
          <x-heroicon-o-x-circle class="w-6 h-6" />
        </div>
        <div>
          <p class="neo-subtitle">Dibatalkan</p>
          <p class="text-3xl font-black">{{ $stats['cancelled'] }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <div class="lg:col-span-2 p-6 neo-card bg-white">
      <h3 class="flex items-center gap-2 mb-6 text-xl font-black uppercase">
        <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-main" />
        Tiket Bioskop
      </h3>

      @if($bookings->count() > 0)
      <div class="space-y-4">
        @foreach($bookings as $booking)
        <a href="{{ route('bookings.show', $booking->id) }}"
          class="block neo-card-sm p-4 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[8px_8px_0px_0px_#000] transition-all">

          <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-black text-gray-600 font-mono">{{ $booking->booking_id }}</span>
                @if($booking->status === 'confirmed')
                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded border border-secondary bg-secondary/25 text-emerald-800">Confirmed</span>
                @elseif($booking->status === 'pending_payment' || $booking->status === 'locked')
                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded border border-primary bg-primary/20 text-primary">Menunggu Pembayaran</span>
                @elseif($booking->status === 'cancelled' || $booking->status === 'failed')
                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded border border-red-600 bg-red-50 text-red-700">Dibatalkan</span>
                @endif
              </div>

              <h4 class="mb-1 text-lg font-black text-black">
                {{ $booking->jadwalTayang->film->judul }}
              </h4>

              <div class="flex flex-wrap gap-3 text-sm">
                <span class="flex items-center gap-1 font-bold text-gray-700">
                  <x-heroicon-o-building-storefront class="w-4 h-4" />
                  {{ $booking->jadwalTayang->studio->bioskop->nama }}
                </span>
                <span class="flex items-center gap-1 font-bold text-gray-700">
                  <x-heroicon-o-video-camera class="w-4 h-4" />
                  {{ $booking->jadwalTayang->studio->nama }}
                </span>
              </div>

              <div class="flex flex-wrap gap-3 mt-2 text-sm">
                <span class="flex items-center gap-1 font-bold text-gray-500">
                  <x-heroicon-o-calendar class="w-4 h-4" />
                  {{ $booking->jadwalTayang->waktu_mulai->format('d M Y') }}
                </span>
                <span class="flex items-center gap-1 font-bold text-gray-500">
                  <x-heroicon-o-clock class="w-4 h-4" />
                  {{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB
                </span>
                <span class="flex items-center gap-1 font-bold text-gray-500">
                  <x-heroicon-o-ticket class="w-4 h-4" />
                  {{ $booking->statusKursis->count() }} Kursi
                </span>
              </div>
            </div>

            <div class="text-right shrink-0">
              <p class="text-2xl font-black font-price text-black">
                Rp {{ number_format($booking->total_price + $booking->service_fee + $booking->fnb_total, 0, ',', '.') }}
              </p>
              <p class="mt-1 text-xs text-gray-600 font-bold">
                {{ $booking->created_at->diffForHumans() }}
              </p>
              <span class="inline-block mt-3 text-xs brutal-btn bg-primary text-white hover:bg-[#A88CF8] !py-1.5 !px-3 shadow-[2px_2px_0px_var(--border)]">
                Lihat Tiket
              </span>
            </div>
          </div>
        </a>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $bookings->appends(request()->except('page_tickets'))->links() }}
      </div>

      @else
      <div class="py-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-primary/20 border-4 border-black rounded-full shadow-[3px_3px_0px_var(--border)]">
          <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-black" />
        </div>
        <h3 class="mb-2 text-xl font-black uppercase">Belum ada tiket</h3>
        <p class="mb-6 font-bold text-sm text-gray-600">Kamu belum pernah memesan tiket film.</p>
        <a href="{{ route('landing') }}" class="brutal-btn bg-primary text-white hover:bg-[#A88CF8] !py-2.5 !px-6 text-sm">
          Cari Film <x-heroicon-o-film class="inline w-4 h-4 ml-1" />
        </a>
      </div>
      @endif
    </div>

    <div class="p-6 neo-card bg-background border-4 border-black shadow-[6px_6px_0px_rgba(0,0,0,1)]">
      <h3 class="flex items-center gap-2 mb-6 text-xl font-black uppercase text-black">
        <x-heroicon-o-shopping-bag class="w-6 h-6 text-black" />
        Pesanan Camilan
      </h3>

      @if($snackOrders->count() > 0)
      <div class="space-y-4">
        @foreach($snackOrders as $order)
        <div class="brutal-box bg-white p-4 relative shadow-[3px_3px_0px_var(--border)]">
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="text-xs font-black text-gray-700 font-mono">{{ $order->order_id }}</span>
            @if($order->status === 'paid')
            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded border border-secondary bg-secondary/25 text-emerald-800">Lunas</span>
            @elseif($order->status === 'locked' || $order->status === 'draft')
            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded border border-primary bg-primary/20 text-primary">Belum Bayar</span>
            @else
            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded border border-red-600 bg-red-50 text-red-700">{{ $order->status }}</span>
            @endif
          </div>

          <div class="space-y-2 border-b-2 border-dashed border-border/20 pb-3 mb-3">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between text-xs font-bold text-gray-800">
              <span class="flex items-center gap-1.5">
                <span class="text-sm shrink-0">{{ $item->snack_emoji }}</span>
                <span>{{ $item->snack_name }} <span class="text-gray-500 font-semibold">&times;{{ $item->qty }}</span></span>
              </span>
              <span>Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
            </div>
            @endforeach
          </div>

          <div class="flex justify-between items-center">
            <div>
              <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ $order->created_at->diffForHumans() }}</p>
              <p class="font-price font-black text-lg text-black mt-0.5">Rp {{ number_format($order->fnb_total, 0, ',', '.') }}</p>
            </div>

            <div class="flex gap-2">
              @if(in_array($order->status, ['draft', 'locked']))
              <a href="{{ route('snacks.checkout.show', $order->id) }}" class="brutal-btn !py-1.5 !px-3 !text-xs bg-primary text-white hover:bg-[#A88CF8]">
                Bayar
              </a>
              @endif
              <a href="{{ route('bookings.snack.show', $order->id) }}" class="brutal-btn !py-1.5 !px-3 !text-xs bg-white hover:bg-gray-50">
                Detail
              </a>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $snackOrders->appends(request()->except('page_snacks'))->links() }}
      </div>

      @else
      <div class="py-12 text-center bg-white border-2 border-black border-dashed rounded-2xl">
        <x-heroicon-o-shopping-bag class="w-10 h-10 text-gray-400 mx-auto mb-3" />
        <p class="text-sm font-black uppercase text-gray-700">Belum ada pesanan camilan</p>
        <p class="text-xs text-gray-500 font-bold mt-1 px-4">Kamu bisa memesan camilan langsung untuk diambil di bioskop.</p>
        <a href="{{ route('snacks.index') }}" class="brutal-btn !py-2 !px-4 !text-xs mt-4 inline-block bg-primary text-white hover:bg-[#A88CF8]">
          Pesan Camilan
        </a>
      </div>
      @endif
    </div>
  </div>
</x-app-layout>

