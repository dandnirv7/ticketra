<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-black leading-tight text-gray-900">
        {{ __('Jadwal Tayang Film') }}
      </h2>
    </div>
  </x-slot>

  @if (session('success'))
  <div class="relative px-4 py-3 mb-6 font-bold text-gray-900 bg-green-200 border-4 border-black rounded-xl shadow-[6px_6px_0px_0px_#000]" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
  </div>
  @endif

  @if($jadwals->count() > 0)
  <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
    @foreach($jadwals as $jadwal)

    <div class="flex flex-col neo-card">

      <div class="flex items-start justify-between gap-3 p-5 pb-0">
        <div class="flex-1 min-w-0">
          <h3 class="mb-1 text-xl truncate neo-title" title="{{ $jadwal->film?->judul ?? 'Film tidak tersedia' }}">
            {{ $jadwal->film?->judul ?? 'Film tidak tersedia' }}
          </h3>
          <p class="flex items-center gap-1 neo-text-sm">
            <x-heroicon-o-building-storefront class="flex-shrink-0 w-4 h-4" />
            <span class="truncate">{{ $jadwal->studio?->bioskop?->nama ?? 'Bioskop tidak tersedia' }}</span>
          </p>
          <p class="flex items-center gap-1 mt-1 neo-text-sm">
            <x-heroicon-o-video-camera class="flex-shrink-0 w-4 h-4" />
            {{ $jadwal->studio?->nama ?? 'Studio tidak tersedia' }}
          </p>
        </div>
        <span class="flex-shrink-0 neo-badge-pink">
          {{ ucfirst($jadwal->studio?->tipe ?? '-') }}
        </span>
      </div>

      <div class="mx-5 neo-divider"></div>

      <div class="flex flex-col justify-between flex-1 p-5 pt-0">
        <div class="mb-4">
          <div class="flex items-center gap-2 mb-2">
            <x-heroicon-o-calendar class="w-5 h-5 text-gray-900" />
            <p class="neo-text">
              {{ $jadwal->waktu_mulai?->format('d M Y') ?? '-' }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <x-heroicon-o-clock class="w-5 h-5 text-gray-900" />
            <p class="font-bold neo-text-sm">
              {{ $jadwal->waktu_mulai?->format('H:i') ?? '-' }} - {{ $jadwal->waktu_selesai?->format('H:i') ?? '-' }} WIB
            </p>
          </div>
        </div>

        <div class="flex items-end justify-between pt-4 mt-auto border-t-2 border-black border-dashed">
          <div>
            <p class="neo-subtitle">Harga</p>
            <p class="text-2xl neo-price">
              Rp {{ number_format($jadwal->harga ?? 0, 0, ',', '.') }}
            </p>
          </div>

          <a href="{{ route('jadwal.show', $jadwal->id) }}"
            class="px-4 py-2 text-sm neo-button-primary">
            Pilih <x-heroicon-o-arrow-right class="inline w-4 h-4 ml-1" />
          </a>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="mt-8">
    {{ $jadwals->links() }}
  </div>

  @else
  <div class="max-w-lg p-12 mx-auto text-center neo-card">
    <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-yellow-200 border-4 border-black rounded-full shadow-neo">
      <x-heroicon-o-calendar-days class="w-10 h-10 text-gray-900" />
    </div>
    <h3 class="mb-2 text-xl neo-title">Belum ada jadwal tayang</h3>
    <p class="neo-text-sm">Silakan cek kembali nanti atau hubungi admin.</p>
  </div>
  @endif
</x-app-layout>