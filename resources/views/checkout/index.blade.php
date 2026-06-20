@php
    $film = $booking->jadwalTayang->film;
    $studio = $booking->jadwalTayang->studio;
    $bioskop = $studio->bioskop;
    $seats = $booking->statusKursis->map(fn($sk) => $sk->kursi->label_baris . $sk->kursi->nomor_kursi)->all();
    $isPayable = in_array($booking->status, ['locked', 'pending_payment']);
    $totalPrice = (float) $booking->total_price;
    $serviceFee = (float) ($booking->service_fee ?? 0);
    $fnbTotal = (float) ($booking->fnb_total ?? 0);
    $grandTotal = $totalPrice + $serviceFee + $fnbTotal;
@endphp

<x-app-layout xData="{
          selectedLocation: 'Jakarta',
          showLocationDropdown: false,
          countdown: '',
          isPaying: false,
          init() {
              @if ($isPayable && $booking->lock_expiry)
              this.startCountdown('{{ $booking->lock_expiry->toIso8601String() }}');
              @endif
          },
          startCountdown(expiryIso) {
              const expiry = new Date(expiryIso).getTime();
              const tick = () => {
                  const diff = expiry - Date.now();
                  if (diff <= 0) {
                      this.countdown = '00:00';
                      clearInterval(this.timer);
                      return;
                  }
                  const m = Math.floor(diff / 60000);
                  const s = Math.floor((diff % 60000) / 1000);
                  this.countdown = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
              };
              tick();
              this.timer = setInterval(tick, 1000);
          }
      }">

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="text-2xl font-black leading-tight text-gray-900">
                Checkout Tiket
            </h2>
            <a href="{{ route('dashboard') }}" class="neo-back-button border-accent-red hover:bg-red-50 hover:text-accent-red">
                Batalkan Pesanan
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        
        <div class="mb-8 space-y-3 text-center">
            <h1 class="text-3xl uppercase md:text-4xl">Amankan Tiket Anda</h1>
            <p class="inline-block px-4 py-2 text-sm font-bold text-gray-700 border-2 border-border rounded-full shadow-sm md:text-base bg-pastel-lemon">
                Kursi telah dikunci secara eksklusif. Pesanan otomatis dibatalkan dalam
                <strong class="ml-1 text-lg font-price text-accent-red" x-text="countdown || '--:--'"></strong>.
            </p>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 mb-6 font-bold text-gray-900 border-4 border-black shadow-[6px_6px_0px_0px_#000] rounded-xl bg-pastel-mint">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="px-4 py-3 mb-6 font-bold text-gray-900 border-4 border-black shadow-[6px_6px_0px_0px_#000] rounded-xl bg-pastel-peach">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            
            <div class="brutal-box bg-white p-6 md:p-8">
                <h3 class="pb-4 mb-6 text-xl uppercase border-b-2 border-dashed border-border">Ringkasan Pesanan</h3>

                <div class="space-y-4 text-sm font-medium">
                    
                    <div class="flex flex-col items-start justify-between gap-1 pb-4 border-b-2 border-dashed md:flex-row md:items-center border-border/30">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-widest opacity-50">ID Booking Anda</p>
                            <p class="mt-1 text-2xl font-black tracking-wider md:text-3xl text-main-foreground font-mono">{{ $booking->booking_id }}</p>
                        </div>
                        @if ($isPayable)
                            <span class="px-3 py-1.5 text-[10px] font-extrabold uppercase bg-pastel-lemon border-2 border-border rounded-md tracking-widest shadow-sm">Menunggu Pembayaran</span>
                        @elseif ($booking->status === 'confirmed')
                            <span class="px-3 py-1.5 text-[10px] font-extrabold uppercase border-2 border-border rounded-md tracking-widest shadow-sm bg-pastel-mint text-accent-green">Lunas</span>
                        @else
                            <span class="px-3 py-1.5 text-[10px] font-extrabold uppercase bg-gray-200 border-2 border-border rounded-md tracking-widest shadow-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                        @endif
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Film</span>
                        <span class="font-extrabold text-right uppercase">{{ $film->judul }}</span>
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Lokasi</span>
                        <span class="font-bold text-right">{{ $bioskop->nama }}, {{ $bioskop->kota ?? '' }}</span>
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Studio</span>
                        <span class="font-bold text-right">{{ $studio->nama }} ({{ ucfirst($studio->tipe) }})</span>
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Jadwal</span>
                        <span class="font-bold text-right">{{ $booking->jadwalTayang->waktu_mulai->format('d F Y') }}, {{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB</span>
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Kursi</span>
                        <span class="font-extrabold text-right uppercase text-main-foreground">{{ implode(', ', $seats) }}</span>
                    </div>

                    <div class="flex flex-col justify-between gap-1 md:flex-row">
                        <span class="text-xs font-bold uppercase opacity-70 tracking-wide md:w-24 shrink-0">Jumlah</span>
                        <span class="font-bold text-right">{{ count($seats) }} tiket &times; Rp {{ number_format((float) $booking->jadwalTayang->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="brutal-box bg-white p-6 md:p-8">
                    <h3 class="pb-4 mb-4 text-xl uppercase border-b-2 border-dashed border-border">Rincian Harga</h3>
                    <div class="space-y-2 text-sm font-bold">
                        <div class="flex justify-between">
                            <span>Harga Tiket ({{ count($seats) }} &times; Rp {{ number_format((float) $booking->jadwalTayang->harga, 0, ',', '.') }})</span>
                            <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Service Fee</span>
                            <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Camilan (F&B)</span>
                            <span>Rp {{ number_format($fnbTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-4 mt-3 border-t-2 border-dashed border-border">
                            <span class="text-xs font-extrabold uppercase tracking-widest">Total Pembayaran</span>
                            <span class="font-price text-3xl font-extrabold text-accent-red">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                
                @if ($isPayable)
                    <div class="brutal-box bg-pastel-mint p-6 md:p-8 text-center">
                        <p class="mb-2 text-xs font-extrabold uppercase tracking-widest opacity-70">Selesaikan Pembayaran</p>
                        <p class="mb-6 text-sm font-medium opacity-80 md:text-base">Klik tombol di bawah untuk membuka jendela pembayaran Midtrans yang aman.</p>

                        <button id="pay-button"
                                type="button"
                                @click="isPaying = true; window.snap.pay('{{ $snapToken }}', {
                                    onSuccess: (r) => { window.location.href = '{{ route('payment.success', $booking->id) }}'; },
                                    onPending: (r) => { window.location.href = '{{ route('payment.pending', $booking->id) }}'; },
                                    onError:   (r) => { alert('Pembayaran gagal. Silakan coba lagi.'); isPaying = false; },
                                    onClose:   ()  => { alert('Anda menutup popup pembayaran. Silakan coba lagi jika ingin melanjutkan.'); isPaying = false; }
                                })"
                                :disabled="isPaying"
                                class="brutal-btn !py-4 !px-10 text-lg w-full justify-center">
                            <span x-show="!isPaying">Bayar Sekarang</span>
                            <span x-show="isPaying" style="display: none;">Memproses...</span>
                            <x-icon name="heroicon-s-bolt" class="w-5 h-5" />
                        </button>

                        <p class="mt-4 text-[10px] font-bold uppercase tracking-widest opacity-60">Didukung oleh Midtrans &middot; QRIS &middot; VA &middot; E-Wallet &middot; Kartu Kredit</p>
                    </div>
                @else
                    <div class="brutal-box bg-white p-6 md:p-8 text-center">
                        <p class="mb-4 text-sm font-bold opacity-80">Booking ini tidak dalam status menunggu pembayaran.</p>
                        <a href="{{ route('bookings.show', $booking->id) }}" class="brutal-btn !py-3 !px-6 text-sm inline-flex justify-center w-full">
                            Lihat Detail Booking <x-icon name="heroicon-s-arrow-right" class="w-4 h-4" />
                        </a>
                    </div>
                @endif
            </div>
        </div>

        
        <p class="mt-8 text-xs font-bold text-center uppercase opacity-50 tracking-widest">
            <x-icon name="heroicon-s-shield-check" class="inline w-4 h-4" /> Transaksi aman & terenkripsi
        </p>
    </div>
</x-app-layout>

@if($snapToken && $isPayable)
<script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
