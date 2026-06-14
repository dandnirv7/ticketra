<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-black leading-tight text-gray-900">
        Ringkasan Pembayaran
      </h2>
      <a href="{{ route('jadwal.show', $booking->jadwal_tayang_id) }}" class="neo-back-button">
        <x-heroicon-o-arrow-left class="w-4 h-4" />
        Kembali
      </a>
    </div>
  </x-slot>

  <div class="min-h-screen py-8 pb-32 bg-amber-50">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

      @if (session('success'))
      <div class="relative px-4 py-3 mb-6 font-bold text-gray-900 bg-green-200 border-4 border-black rounded-xl shadow-[6px_6px_0px_0px_#000]" role="alert">
        {{ session('success') }}
      </div>
      @endif

      <div class="p-6 neo-card md:p-8">

        <div class="flex flex-col items-start justify-between pb-6 mb-6 border-b-4 border-black border-dashed sm:flex-row sm:items-center">
          <div>
            <p class="neo-subtitle">ID Booking Anda</p>
            <p class="text-3xl font-black tracking-wider text-gray-900">{{ $booking->booking_id }}</p>
          </div>
          <div class="mt-2 text-right sm:mt-0">
            <p class="neo-subtitle">Status</p>
            <span class="text-sm uppercase neo-badge-yellow">Menunggu Pembayaran</span>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

          <div class="space-y-4">
            <h3 class="flex items-center gap-2 text-lg font-black text-gray-900">
              <x-heroicon-o-film class="w-5 h-5" /> Detail Pesanan
            </h3>

            <div class="neo-info-pink">
              <p class="neo-subtitle">Film</p>
              <p class="text-lg neo-text">{{ $booking->jadwalTayang->film->judul }}</p>
              <div class="flex gap-2 mt-1">
                <span class="text-xs neo-badge-blue">{{ $booking->jadwalTayang->film->genre }}</span>
                <span class="text-xs neo-badge-green">{{ $booking->jadwalTayang->film->durasi_menit }} Menit</span>
              </div>
            </div>

            <div class="neo-info-blue">
              <p class="neo-subtitle">Lokasi & Studio</p>
              <p class="neo-text">{{ $booking->jadwalTayang->studio->bioskop->nama }}</p>
              <p class="neo-text-sm">{{ $booking->jadwalTayang->studio->nama }} ({{ ucfirst($booking->jadwalTayang->studio->tipe) }})</p>
            </div>

            <div class="neo-info-yellow">
              <p class="neo-subtitle">Waktu Tayang</p>
              <p class="neo-text">{{ $booking->jadwalTayang->waktu_mulai->format('d F Y') }}</p>
              <p class="neo-text-sm">{{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} - {{ $booking->jadwalTayang->waktu_selesai->format('H:i') }} WIB</p>
            </div>
          </div>

          <div class="space-y-4">
            <h3 class="flex items-center gap-2 text-lg font-black text-gray-900">
              <x-heroicon-o-ticket class="w-5 h-5" /> Kursi Terpilih
            </h3>

            <div class="p-4 bg-purple-100 neo-card-sm">
              <div class="flex flex-wrap gap-2">
                @foreach($booking->statusKursis as $statusKursi)
                <span class="text-sm font-black bg-white neo-badge">
                  {{ $statusKursi->kursi->label_baris }}{{ $statusKursi->kursi->nomor_kursi }}
                </span>
                @endforeach
              </div>
              <p class="mt-3 text-xs font-bold text-gray-600">
                Total {{ $booking->statusKursis->count() }} Kursi x Rp {{ number_format($booking->jadwalTayang->harga, 0, ',', '.') }}
              </p>
            </div>

            <h3 class="flex items-center gap-2 pt-4 text-lg font-black text-gray-900">
              <x-heroicon-o-calculator class="w-5 h-5" /> Rincian Harga
            </h3>

            <div class="p-4 space-y-2 bg-white border-4 border-black rounded-xl">
              <div class="flex justify-between text-sm font-bold">
                <span>Harga Tiket</span>
                <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
              </div>
              <div class="flex justify-between text-sm font-bold text-gray-500">
                <span>Service Fee (Platform)</span>
                <span>Rp 0</span>
              </div>
              <div class="flex justify-between text-sm font-bold text-gray-500">
                <span>Biaya Admin</span>
                <span>Rp 0</span>
              </div>
              <div class="my-2 border-t-2 border-black border-dashed"></div>
              <div class="flex items-center justify-between">
                <span class="neo-subtitle">Total Bayar</span>
                <span class="text-2xl neo-price">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
              </div>
            </div>

            <div class="bg-red-100 border-4 border-black rounded-xl p-3 flex items-center gap-3 shadow-[4px_4px_0px_0px_#000]">
              <x-heroicon-o-clock class="w-6 h-6 text-red-600" />
              <div>
                <p class="text-xs font-bold text-red-800 uppercase">Selesaikan Pembayaran Dalam</p>
                <p class="text-lg font-black text-red-900">09:59</p>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-white border-t-4 border-black shadow-[0px_-5px_15px_-3px_rgba(0,0,0,0.1)]">
      <div class="flex flex-col items-center justify-between max-w-4xl gap-4 mx-auto sm:flex-row">

        <div class="text-center sm:text-left">
          <p class="neo-subtitle">Total yang harus dibayar</p>
          <p class="text-2xl neo-price">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        <button id="pay-button" class="px-8 py-4 text-lg neo-button-primary">
          Bayar Sekarang <x-heroicon-o-credit-card class="inline w-5 h-5 ml-2" />
        </button>
      </div>
    </div>

  </div>
</x-app-layout>

@if($snapToken)
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
  data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');

    if (payButton) {
      payButton.addEventListener('click', function(e) {
        e.preventDefault();

        this.disabled = true;
        this.innerHTML = 'Memproses... <svg class="inline w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        window.snap.pay('{{ $snapToken }}', {
          onSuccess: function(result) {
            console.log('Payment Success:', result);
            window.location.href = '{{ route('
            payment.success ', $booking->id) }}';
          },
          onPending: function(result) {
            console.log('Payment Pending:', result);
            window.location.href = '{{ route('
            payment.pending ', $booking->id) }}';
          },
          onError: function(result) {
            console.log('Payment Error:', result);
            alert('Pembayaran gagal. Silakan coba lagi.');
            payButton.disabled = false;
            payButton.innerHTML = 'Bayar Sekarang <svg class="inline w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>';
          },
          onClose: function() {
            alert('Anda menutup popup pembayaran. Silakan coba lagi jika ingin melanjutkan.');
            payButton.disabled = false;
            payButton.innerHTML = 'Bayar Sekarang <svg class="inline w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>';
          }
        });
      });
    }
  });
</script>
@endif