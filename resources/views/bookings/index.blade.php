<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-black leading-tight text-gray-900">
      Riwayat Transaksi
    </h2>
  </x-slot>

  <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
    <div class="p-4 bg-green-100 neo-card-sm">
      <div class="flex items-center gap-3">
        <div class="bg-green-300 neo-icon">
          <x-heroicon-o-ticket class="w-6 h-6" />
        </div>
        <div>
          <p class="neo-subtitle">Akan Datang</p>
          <p class="text-3xl font-black">{{ $stats['upcoming'] }}</p>
        </div>
      </div>
    </div>

    <div class="p-4 bg-blue-100 neo-card-sm">
      <div class="flex items-center gap-3">
        <div class="bg-blue-300 neo-icon">
          <x-heroicon-o-check-circle class="w-6 h-6" />
        </div>
        <div>
          <p class="neo-subtitle">Selesai</p>
          <p class="text-3xl font-black">{{ $stats['completed'] }}</p>
        </div>
      </div>
    </div>

    <div class="p-4 bg-red-100 neo-card-sm">
      <div class="flex items-center gap-3">
        <div class="bg-red-300 neo-icon">
          <x-heroicon-o-x-circle class="w-6 h-6" />
        </div>
        <div>
          <p class="neo-subtitle">Dibatalkan</p>
          <p class="text-3xl font-black">{{ $stats['cancelled'] }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="grid items-start grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="p-6 bg-white lg:col-span-2 neo-card">
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
                <span class="font-mono text-xs font-black text-gray-600">{{ $booking->booking_id }}</span>
                @if($booking->status === 'confirmed')
                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded border border-emerald-600 bg-emerald-50 text-emerald-700">Confirmed</span>
                @elseif($booking->status === 'pending_payment' || $booking->status === 'locked')
                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded border border-amber-600 bg-amber-50 text-amber-700">Menunggu Pembayaran</span>
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
              <p class="text-2xl font-black text-black font-price">
                Rp {{ number_format($booking->total_price + $booking->service_fee + $booking->fnb_total, 0, ',', '.') }}
              </p>
              <p class="mt-1 text-xs font-bold text-gray-600">
                {{ $booking->created_at->diffForHumans() }}
              </p>
              <span class="inline-block mt-3 text-xs brutal-btn !py-1.5 !px-3 shadow-[2px_2px_0px_var(--border)]">
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
        <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-pastel-sky border-4 border-black rounded-full shadow-[3px_3px_0px_var(--border)]">
          <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-black" />
        </div>
        <h3 class="mb-2 text-xl font-black uppercase">Belum ada tiket</h3>
        <p class="mb-6 text-sm font-bold text-gray-600">Kamu belum pernah memesan tiket film.</p>
        <a href="{{ route('landing') }}" class="brutal-btn !py-2.5 !px-6 text-sm">
          Cari Film <x-heroicon-o-film class="inline w-4 h-4 ml-1" />
        </a>
      </div>
      @endif
    </div>

    <div class="p-6 neo-card bg-pastel-peach border-4 border-black shadow-[6px_6px_0px_rgba(0,0,0,1)]">
      <h3 class="flex items-center gap-2 mb-6 text-xl font-black text-black uppercase">
        <x-heroicon-o-shopping-bag class="w-6 h-6 text-black" />
        Pesanan Camilan
      </h3>

      @if($snackOrders->count() > 0)
      <div class="space-y-4">
        @foreach($snackOrders as $order)
        <div class="brutal-box bg-white p-4 relative shadow-[3px_3px_0px_var(--border)]">
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="font-mono text-xs font-black text-gray-700">{{ $order->order_id }}</span>
            @if($order->status === 'paid')
            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded border border-emerald-600 bg-emerald-50 text-emerald-700">Lunas</span>
            @elseif($order->status === 'locked' || $order->status === 'draft')
            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded border border-amber-600 bg-amber-50 text-amber-700">Belum Bayar</span>
            @else
            <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded border border-red-600 bg-red-50 text-red-700">{{ $order->status }}</span>
            @endif
          </div>

          <div class="pb-3 mb-3 space-y-2 border-b-2 border-dashed border-border/20">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between text-xs font-bold text-gray-800">
              <span class="flex items-center gap-1.5">
                <span class="text-sm shrink-0">{{ $item->snack_emoji }}</span>
                <span>{{ $item->snack_name }} <span class="font-semibold text-gray-500">&times;{{ $item->qty }}</span></span>
              </span>
              <span>Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
            </div>
            @endforeach
          </div>

          <div class="flex items-center justify-between">
            <div>
              <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ $order->created_at->diffForHumans() }}</p>
              <p class="font-price font-black text-lg text-black mt-0.5">Rp {{ number_format($order->fnb_total, 0, ',', '.') }}</p>
            </div>

            <div class="flex gap-2">
              @if(in_array($order->status, ['draft', 'locked']))
              <a href="{{ route('snacks.checkout.show', $order->id) }}" class="brutal-btn !py-1.5 !px-3 !text-xs bg-pastel-lemon">
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
        <x-heroicon-o-shopping-bag class="w-10 h-10 mx-auto mb-3 text-gray-400" />
        <p class="text-sm font-black text-gray-700 uppercase">Belum ada pesanan camilan</p>
        <p class="px-4 mt-1 text-xs font-bold text-gray-500">Kamu bisa memesan camilan langsung untuk diambil di bioskop.</p>
        <a href="{{ route('snacks.index') }}" class="brutal-btn !py-2 !px-4 !text-xs mt-4 inline-block bg-pastel-mint">
          Pesan Camilan
        </a>
      </div>
      @endif
    </div>
  </div>
</x-app-layout>

