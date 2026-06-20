<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-black leading-tight text-gray-900">
        {{ __('Detail Jadwal Tayang') }}
      </h2>
      <a href="{{ route('jadwal.index') }}" class="neo-back-button">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
      </a>
    </div>
  </x-slot>

  <div class="neo-card">

    <div class="neo-banner">
      <div class="flex items-center gap-2">
        <span class="inline-block w-3 h-3 bg-red-400 border-2 border-black rounded-full"></span>
        <span class="inline-block w-3 h-3 bg-yellow-400 border-2 border-black rounded-full"></span>
        <span class="inline-block w-3 h-3 bg-green-400 border-2 border-black rounded-full"></span>
        <span class="ml-3 text-sm font-black tracking-wide text-gray-900">NOW_SHOWING.exe</span>
      </div>
    </div>

    <div class="p-6 text-gray-900 md:p-8">

      <div class="flex flex-col gap-6 md:flex-row">

        <div class="flex-shrink-0 w-full md:w-1/3">
          <div class="relative">
            <div class="neo-poster-frame"></div>
            <img
              src="{{ $jadwalTayang->film?->poster_url ?? 'https://placehold.co/300x450/FFB6C1/000?text=No+Poster' }}"
              alt="{{ $jadwalTayang->film?->judul ?? 'Film tidak tersedia' }}"
              class="neo-poster">
          </div>
        </div>

        <div class="flex flex-col justify-between w-full md:w-2/3">
          <div>
            <h1 class="mb-3 neo-title">
              {{ $jadwalTayang->film?->judul ?? 'Film tidak tersedia' }}
            </h1>

            <div class="flex flex-wrap gap-2 mb-5">
              <span class="neo-badge-blue">
                <x-heroicon-o-film class="inline w-4 h-4 mr-1" />
                {{ $jadwalTayang->film?->genre ?? 'N/A' }}
              </span>
              <span class="neo-badge-yellow">
                <x-heroicon-s-star class="inline w-4 h-4 mr-1" />
                {{ $jadwalTayang->film?->rating ?? '0' }}
              </span>

              <span class="neo-badge-green">
                <x-heroicon-o-clock class="inline w-4 h-4 mr-1" />
                {{ $jadwalTayang->film?->durasi_menit ?? '0' }} Menit
              </span>
            </div>

            <div class="neo-divider"></div>

            <div class="mb-3 neo-info-pink">
              <div class="flex items-start gap-3">
                <div class="neo-icon">
                  <x-heroicon-o-building-storefront class="w-5 h-5 text-gray-900" />
                </div>

                <div>
                  <p class="neo-subtitle">Lokasi Nonton</p>
                  <p class="neo-text">{{ $jadwalTayang->studio?->bioskop?->nama ?? 'Bioskop Tidak Ditemukan' }}</p>
                  <p class="neo-text-sm">{{ $jadwalTayang->studio?->bioskop?->alamat ?? '-' }}, {{ $jadwalTayang->studio?->bioskop?->kota ?? '-' }}</p>
                </div>
              </div>
            </div>

            <div class="mb-3 neo-info-blue">
              <div class="flex items-start gap-3">
                <div class="neo-icon">
                  <x-heroicon-o-video-camera class="w-5 h-5 text-gray-900" />
                </div>

                <div>
                  <p class="neo-subtitle">Studio</p>
                  <p class="neo-text">{{ $jadwalTayang->studio?->nama ?? 'Studio Tidak Ditemukan' }}</p>
                  <div class="flex flex-wrap gap-2 mt-1">
                    <span class="px-2 py-0.5 text-xs font-bold bg-white border-2 border-black rounded">
                      {{ ucfirst($jadwalTayang->studio?->tipe ?? 'reguler') }}
                    </span>
                    <span class="px-2 py-0.5 text-xs font-bold bg-white border-2 border-black rounded">
                      {{ $jadwalTayang->studio?->kapasitas ?? '0' }} Kursi
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="neo-info-yellow">
              <div class="flex items-start gap-3">
                <div class="neo-icon">
                  <x-heroicon-o-clock class="w-5 h-5 text-gray-900" />
                </div>
                <div>
                  <p class="neo-subtitle">Jadwal Tayang</p>
                  <p class="neo-text">{{ $jadwalTayang->waktu_mulai->format('d F Y') }}</p>
                  <p class="neo-text-sm">
                    {{ $jadwalTayang->waktu_mulai->format('H:i') }} - {{ $jadwalTayang->waktu_selesai->format('H:i') }} WIB
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-col items-center justify-between gap-4 pt-6 mt-6 border-t-4 border-black border-dashed sm:flex-row">
            <div class="text-center sm:text-left">
              <p class="neo-subtitle">Harga per kursi</p>
              <p class="neo-price">
                Rp {{ number_format($jadwalTayang->harga, 0, ',', '.') }}
                <span class="inline-block w-3 h-3 align-middle bg-green-400 border-2 border-black rounded-full animate-pulse"></span>
              </p>
            </div>

            <a href="{{ route('jadwal.kursi', $jadwalTayang->id) }}" class="neo-button-primary">
              Pilih Kursi <x-heroicon-o-arrow-right class="inline w-4 h-4 ml-1" />
            </a>
          </div>

        </div>
      </div>

    </div>
  </div>

  <div class="neo-dots">
    <span class="neo-dot-pink"></span>
    <span class="neo-dot-yellow"></span>
    <span class="neo-dot-blue"></span>
    <span class="neo-dot-green"></span>
  </div>
</x-app-layout>