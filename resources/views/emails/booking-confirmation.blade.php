<x-mail::message>
  # Tiket Bioskop Anda Berhasil Dipesan!

  Terima kasih telah memesan tiket di **Ticketra**. Berikut detail pemesanan Anda:

  ---

  ## Informasi Booking

  **ID Booking:** {{ $booking->booking_id }}
  **Status:** <span style="color: green; font-weight: bold;">✓ CONFIRMED</span>

  ---

  ## Detail Film

  **Judul:** {{ $booking->jadwalTayang->film->judul }}
  **Genre:** {{ $booking->jadwalTayang->film->genre }}
  **Durasi:** {{ $booking->jadwalTayang->film->durasi_menit }} menit

  ---

  ## Jadwal Tayang

  **Tanggal:** {{ $booking->jadwalTayang->waktu_mulai->format('l, d F Y') }}
  **Waktu:** {{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} - {{ $booking->jadwalTayang->waktu_selesai->format('H:i') }} WIB
  **Bioskop:** {{ $booking->jadwalTayang->studio->bioskop->nama }}
  **Studio:** {{ $booking->jadwalTayang->studio->nama }} ({{ ucfirst($booking->jadwalTayang->studio->tipe) }})
  **Alamat:** {{ $booking->jadwalTayang->studio->bioskop->alamat }}

  ---

  ## Kursi Terpilih

  @foreach($booking->statusKursis as $statusKursi)
  **{{ $statusKursi->kursi->label_baris }}{{ $statusKursi->kursi->nomor_kursi }}**@if(!$loop->last), @endif
  @endforeach

  ---

  ## Total Pembayaran

  **Rp {{ number_format($booking->total_price, 0, ',', '.') }}**
  ({{ $booking->statusKursis->count() }} kursi × Rp {{ number_format($booking->jadwalTayang->harga, 0, ',', '.') }})

  ---

  ## E-Ticket QR Code

  Silakan tunjukkan QR code berikut kepada petugas bioskop saat masuk:

  <div style="text-align: center; margin: 30px 0;">
    <img src="data:image/png;base64,{{ $qrCodeBase64 }}"
      alt="QR Code E-Ticket"
      style="width: 200px; height: 200px; border: 4px solid #000; border-radius: 8px;">
  </div>

  **{{ $booking->booking_id }}**

  ---

  ## Cara Menggunakan E-Ticket

  1. **Tunjukkan QR code** ini kepada petugas bioskop
  2. **Datang minimal 15 menit** sebelum jadwal tayang
  3. **E-ticket hanya berlaku 1 kali scan**
  4. **Simpan email ini** atau download PDF attachment

  ---

  <x-mail::button :url="route('bookings.show', $booking->id)" color="success">
    Lihat Detail Booking
  </x-mail::button>

  ---

  ## Penting

  - E-ticket ini hanya berlaku untuk 1 kali penggunaan
  - Tidak dapat dicetak ulang, simpan dengan baik
  - Pembatalan hanya dapat dilakukan maksimal 24 jam sebelum jadwal tayang
  - Untuk bantuan, hubungi kami di support@ticketra.web.id

  Terima kasih telah memilih Ticketra! Selamat menonton!

  <x-mail::footer>
    © {{ date('Y') }} Ticketra. All rights reserved.
    www.ticketra.web.id | support@ticketra.web.id
  </x-mail::footer>
</x-mail::message>