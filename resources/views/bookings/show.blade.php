<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-black leading-tight text-gray-900">
        Detail Booking
      </h2>
      <a href="{{ route('bookings.index') }}" class="neo-back-button">
        <x-heroicon-o-arrow-left class="w-4 h-4" />
        Kembali
      </a>
    </div>
  </x-slot>

  <div class="max-w-4xl">

      @if($booking->status === 'confirmed')
      <div class="p-4 mb-6 bg-green-100 neo-card-sm">
        <div class="flex items-center gap-3">
          <div class="bg-green-300 neo-icon">
            <x-heroicon-o-check-circle class="w-6 h-6 text-green-700" />
          </div>
          <div>
            <p class="font-black text-green-900">Pembayaran Berhasil!</p>
            <p class="text-sm text-green-800">Tunjukkan QR code ini saat masuk bioskop</p>
          </div>
        </div>
      </div>
      @elseif($booking->status === 'pending_payment')
      <div class="p-4 mb-6 bg-yellow-100 neo-card-sm">
        <div class="flex items-center gap-3">
          <div class="bg-yellow-300 neo-icon">
            <x-heroicon-o-clock class="w-6 h-6 text-yellow-700" />
          </div>
          <div>
            <p class="font-black text-yellow-900">Menunggu Pembayaran</p>
            <p class="text-sm text-yellow-800">Selesaikan pembayaran sebelum {{ $booking->lock_expiry->format('H:i') }}</p>
          </div>
        </div>
      </div>
      @endif

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="space-y-4 lg:col-span-1">

          <div class="p-6 text-center neo-card">
            <h3 class="mb-4 text-lg neo-title">E-Ticket</h3>

            @if($booking->status === 'confirmed')
            <div class="inline-block p-4 bg-white border-4 border-black rounded-xl">
              {!! $qrCode !!}
            </div>
            <p class="mt-4 neo-subtitle">{{ $booking->booking_id }}</p>
            <p class="mt-2 text-xs text-gray-600">Scan QR ini di pintu masuk bioskop</p>
            @else
            <div class="p-4 bg-gray-100 border-4 border-black border-dashed rounded-xl">
              <x-heroicon-o-lock-closed class="w-20 h-20 mx-auto text-gray-400" />
              <p class="mt-2 text-sm font-bold text-gray-500">QR Code akan muncul setelah pembayaran</p>
            </div>
            @endif
          </div>

          @if($booking->status === 'confirmed')
          <a href="{{ route('bookings.pdf', $booking->id) }}" class="inline-block w-full text-center neo-button-primary">
            <x-heroicon-o-arrow-down-tray class="inline w-5 h-5 mr-2" />
            Download E-Ticket PDF
          </a>
          @endif

        </div>

        <div class="space-y-4 lg:col-span-2">

          <div class="p-6 neo-card">
            <h3 class="flex items-center gap-2 mb-4 text-lg neo-title">
              <x-heroicon-o-film class="w-5 h-5" />
              Detail Film
            </h3>

            <div class="flex gap-4">
              <img src="{{ $booking->jadwalTayang->film->poster_url }}"
                alt="{{ $booking->jadwalTayang->film->judul }}"
                class="object-cover w-24 border-4 border-black rounded-lg h-36 shadow-neo-sm">

              <div class="flex-1">
                <h4 class="mb-2 text-xl font-black">{{ $booking->jadwalTayang->film->judul }}</h4>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span class="text-xs neo-badge-blue">{{ $booking->jadwalTayang->film->genre }}</span>
                  <span class="flex flex-row items-center justify-center gap-1 text-xs text-center neo-badge-yellow">
                    <x-heroicon-o-star class="w-3 h-3" />
                    {{ $booking->jadwalTayang->film->rating }}</span>
                  <span class="text-xs neo-badge-green">{{ $booking->jadwalTayang->film->durasi_menit }} menit</span>
                </div>
                <p class="text-sm text-gray-600">{{ $booking->jadwalTayang->film->sinopsis }}</p>
              </div>
            </div>
          </div>

          <div class="p-6 neo-card">
            <h3 class="flex items-center gap-2 mb-4 text-lg neo-title">
              <x-heroicon-o-calendar class="w-5 h-5" />
              Jadwal Tayang
            </h3>

            <div class="grid grid-cols-2 gap-4">
              <div class="neo-info-pink">
                <p class="neo-subtitle">Bioskop</p>
                <p class="neo-text">{{ $booking->jadwalTayang->studio->bioskop->nama }}</p>
                <p class="neo-text-sm">{{ $booking->jadwalTayang->studio->bioskop->alamat }}</p>
              </div>

              <div class="neo-info-blue">
                <p class="neo-subtitle">Studio</p>
                <p class="neo-text">{{ $booking->jadwalTayang->studio->nama }}</p>
                <p class="neo-text-sm">{{ ucfirst($booking->jadwalTayang->studio->tipe) }}</p>
              </div>

              <div class="neo-info-yellow">
                <p class="neo-subtitle">Tanggal</p>
                <p class="neo-text">{{ $booking->jadwalTayang->waktu_mulai->format('d M Y') }}</p>
                <p class="neo-text-sm">{{ $booking->jadwalTayang->waktu_mulai->format('l') }}</p>
              </div>

              <div class="neo-info-green">
                <p class="neo-subtitle">Waktu</p>
                <p class="neo-text">{{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB</p>
                <p class="neo-text-sm">s/d {{ $booking->jadwalTayang->waktu_selesai->format('H:i') }} WIB</p>
              </div>
            </div>
          </div>

          <div class="p-6 neo-card">
            <h3 class="flex items-center gap-2 mb-4 text-lg neo-title">
              <x-heroicon-o-ticket class="w-5 h-5" />
              Kursi Terpilih
            </h3>

            <div class="flex flex-wrap gap-2">
              @foreach($booking->statusKursis as $statusKursi)
              <span class="text-sm font-black bg-purple-200 neo-badge">
                {{ $statusKursi->kursi->label_baris }}{{ $statusKursi->kursi->nomor_kursi }}
              </span>
              @endforeach
            </div>

            <div class="pt-4 mt-4 border-t-2 border-black border-dashed">
              <div class="flex items-center justify-between">
                <span class="neo-subtitle">Total Pembayaran</span>
                <span class="text-2xl neo-price">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
              </div>
              <p class="mt-2 text-xs text-gray-600">
                {{ $booking->statusKursis->count() }} kursi × Rp {{ number_format($booking->jadwalTayang->harga, 0, ',', '.') }}
              </p>
            </div>
          </div>

          <div class="p-6 neo-card">
            <h3 class="flex items-center gap-2 mb-4 text-lg neo-title">
              <x-heroicon-o-credit-card class="w-5 h-5" />
              Informasi Pembayaran
            </h3>

            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">ID Booking</span>
                <span class="font-bold">{{ $booking->booking_id }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Status</span>
                <span class="font-bold uppercase">{{ str_replace('_', ' ', $booking->status) }}</span>
              </div>
              @if($booking->paid_at)
              <div class="flex justify-between">
                <span class="text-gray-600">Dibayar pada</span>
                <span class="font-bold">{{ $booking->paid_at->format('d M Y, H:i') }}</span>
              </div>
              @endif
              <div class="flex justify-between">
                <span class="text-gray-600">Dibuat pada</span>
                <span class="font-bold">{{ $booking->created_at->format('d M Y, H:i') }}</span>
              </div>
            </div>
          </div>

      </div>
    </div>
  </div>
</x-app-layout>