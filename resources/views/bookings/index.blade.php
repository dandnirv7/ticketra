<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-black leading-tight text-gray-900">
      Riwayat Transaksi
    </h2>
  </x-slot>

  <div class="min-h-screen py-8 bg-amber-50">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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

      <div class="p-6 neo-card">
        <h3 class="flex items-center gap-2 mb-4 text-xl font-black">
          <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
          Semua Transaksi
        </h3>

        @if($bookings->count() > 0)
        <div class="space-y-4">
          @foreach($bookings as $booking)
          <a href="{{ route('bookings.show', $booking->id) }}"
            class="block neo-card-sm p-4 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[8px_8px_0px_0px_#000] transition-all">

            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">

              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-xs font-black text-gray-600">{{ $booking->booking_id }}</span>
                  @if($booking->status === 'confirmed')
                  <span class="text-xs neo-badge-green">Confirmed</span>
                  @elseif($booking->status === 'pending_payment')
                  <span class="text-xs neo-badge-yellow">Menunggu Pembayaran</span>
                  @elseif($booking->status === 'cancelled' || $booking->status === 'failed')
                  <span class="text-xs bg-red-200 neo-badge">Dibatalkan</span>
                  @endif
                </div>

                <h4 class="mb-1 text-lg font-black">
                  {{ $booking->jadwalTayang->film->judul }}
                </h4>

                <div class="flex flex-wrap gap-3 text-sm">
                  <span class="flex items-center gap-1">
                    <x-heroicon-o-building-storefront class="w-4 h-4" />
                    {{ $booking->jadwalTayang->studio->bioskop->nama }}
                  </span>
                  <span class="flex items-center gap-1">
                    <x-heroicon-o-video-camera class="w-4 h-4" />
                    {{ $booking->jadwalTayang->studio->nama }}
                  </span>
                </div>

                <div class="flex flex-wrap gap-3 mt-2 text-sm">
                  <span class="flex items-center gap-1">
                    <x-heroicon-o-calendar class="w-4 h-4" />
                    {{ $booking->jadwalTayang->waktu_mulai->format('d M Y') }}
                  </span>
                  <span class="flex items-center gap-1">
                    <x-heroicon-o-clock class="w-4 h-4" />
                    {{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB
                  </span>
                  <span class="flex items-center gap-1">
                    <x-heroicon-o-ticket class="w-4 h-4" />
                    {{ $booking->statusKursis->count() }} Kursi
                  </span>
                </div>
              </div>

              <div class="text-right">
                <p class="text-2xl font-black">
                  Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-gray-600">
                  {{ $booking->created_at->diffForHumans() }}
                </p>
                <span class="inline-block mt-2 text-xs neo-button-secondary">
                  Lihat Detail <x-heroicon-o-arrow-right class="inline w-3 h-3" />
                </span>
              </div>
            </div>
          </a>
          @endforeach
        </div>

        <div class="mt-6">
          {{ $bookings->links() }}
        </div>

        @else
        <div class="py-12 text-center">
          <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-yellow-200 border-4 border-black rounded-full shadow-neo">
            <x-heroicon-o-clipboard-document-list class="w-10 h-10" />
          </div>
          <h3 class="mb-2 text-xl neo-title">Belum ada transaksi</h3>
          <p class="mb-4 neo-text-sm">Mulai pesan tiket bioskop pertamamu!</p>
          <a href="{{ route('jadwal.index') }}" class="neo-button-primary">
            Cari Film <x-heroicon-o-film class="inline w-4 h-4 ml-1" />
          </a>
        </div>
        @endif
      </div>

    </div>
  </div>
</x-app-layout>