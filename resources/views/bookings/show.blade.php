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
      <div class="p-4 mb-6 bg-[#E8FDF5] border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] rounded-xl">
        <div class="flex items-center gap-3">
          <div class="bg-secondary border-2 border-black rounded-lg w-10 h-10 flex items-center justify-center shrink-0">
            <x-heroicon-o-check-circle class="w-6 h-6 text-emerald-800" />
          </div>
          <div>
            <p class="font-black text-emerald-950">Pembayaran Berhasil!</p>
            <p class="text-sm text-emerald-900/80 font-semibold">Tunjukkan QR code ini saat masuk bioskop</p>
          </div>
        </div>
      </div>
      @elseif($booking->status === 'pending_payment')
      <div class="p-4 mb-6 bg-[#FFFBEB] border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] rounded-xl">
        <div class="flex items-center gap-3">
          <div class="bg-accent-yellow border-2 border-black rounded-lg w-10 h-10 flex items-center justify-center shrink-0">
            <x-heroicon-o-clock class="w-6 h-6 text-yellow-800" />
          </div>
          <div>
            <p class="font-black text-yellow-950">Menunggu Pembayaran</p>
            <p class="text-sm text-yellow-900/80 font-semibold">Selesaikan pembayaran sebelum {{ $booking->lock_expiry->format('H:i') }}</p>
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

            <div class="flex flex-col sm:flex-row gap-4">
              <img src="{{ $booking->jadwalTayang->film->poster_url }}"
                alt="{{ $booking->jadwalTayang->film->judul }}"
                class="object-cover w-24 h-36 border-4 border-black rounded-lg shadow-neo-sm mx-auto sm:mx-0 shrink-0">

              <div class="flex-1 text-center sm:text-left">
                <h4 class="mb-2 text-xl font-black text-black">{{ $booking->jadwalTayang->film->judul }}</h4>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mb-3">
                  <span class="text-xs px-2.5 py-1 rounded-lg bg-primary/20 text-primary border-2 border-border font-extrabold shadow-[1px_1px_0px_#000]">{{ $booking->jadwalTayang->film->genre }}</span>
                  <span class="flex flex-row items-center justify-center gap-1 text-xs px-2.5 py-1 rounded-lg bg-accent-yellow border-2 border-border font-extrabold text-black shadow-[1px_1px_0px_#000]">
                    <x-heroicon-o-star class="w-3.5 h-3.5 fill-current text-black" />
                    {{ $booking->jadwalTayang->film->rating }}</span>
                  <span class="text-xs px-2.5 py-1 rounded-lg bg-secondary/40 border-2 border-border font-extrabold text-emerald-800 shadow-[1px_1px_0px_#000]">{{ $booking->jadwalTayang->film->durasi_menit }} menit</span>
                </div>
                <p class="text-sm text-gray-600 font-medium">{{ $booking->jadwalTayang->film->sinopsis }}</p>
              </div>
            </div>
          </div>

          <div class="p-6 neo-card">
            <h3 class="flex items-center gap-2 mb-4 text-lg neo-title">
              <x-heroicon-o-calendar class="w-5 h-5" />
              Jadwal Tayang
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="bg-[#FFF1F2] border-[3px] border-border p-4 rounded-xl shadow-[3px_3px_0px_#000]">
                <p class="neo-subtitle">Bioskop</p>
                <p class="neo-text">{{ $booking->jadwalTayang->studio->bioskop->nama }}</p>
                <p class="neo-text-sm">{{ $booking->jadwalTayang->studio->bioskop->alamat }}</p>
              </div>

              <div class="bg-[#F1EEFE] border-[3px] border-border p-4 rounded-xl shadow-[3px_3px_0px_#000]">
                <p class="neo-subtitle">Studio</p>
                <p class="neo-text">{{ $booking->jadwalTayang->studio->nama }}</p>
                <p class="neo-text-sm">{{ ucfirst($booking->jadwalTayang->studio->tipe) }}</p>
              </div>

              <div class="bg-[#FEF08A]/35 border-[3px] border-border p-4 rounded-xl shadow-[3px_3px_0px_#000]">
                <p class="neo-subtitle">Tanggal</p>
                <p class="neo-text">{{ $booking->jadwalTayang->waktu_mulai->format('d M Y') }}</p>
                <p class="neo-text-sm">{{ $booking->jadwalTayang->waktu_mulai->format('l') }}</p>
              </div>

              <div class="bg-secondary/30 border-[3px] border-border p-4 rounded-xl shadow-[3px_3px_0px_#000]">
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
              <span class="text-sm px-3 py-1.5 rounded-lg bg-primary text-white border-2 border-border font-black shadow-[1.5px_1.5px_0px_#000]">
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