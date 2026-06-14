<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-black leading-tight text-gray-900">
      Pembayaran Berhasil!
    </h2>
  </x-slot>

  <div class="min-h-screen py-12 bg-amber-50">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

      <div class="p-8 text-center neo-card">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-green-300 border-4 border-black rounded-full shadow-neo">
          <x-heroicon-o-check-circle class="w-12 h-12 text-gray-900" />
        </div>

        <h1 class="mb-4 text-3xl neo-title">Terima Kasih!</h1>
        <p class="mb-6 neo-text-sm">
          Pembayaran Anda telah berhasil diproses. E-ticket akan dikirim ke email Anda.
        </p>

        <div class="mb-6 text-left neo-info-yellow">
          <p class="neo-subtitle">ID Booking</p>
          <p class="text-xl neo-text">{{ $booking->booking_id }}</p>
        </div>

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
          <a href="{{ route('dashboard') }}" class="neo-button-primary">
            Kembali ke Dashboard
          </a>
          <a href="#" class="neo-button-secondary">
            Download E-Ticket
          </a>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>