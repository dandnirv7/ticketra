<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-black leading-tight text-gray-900">
        Pilih Kursi
      </h2>

      <a href="{{ route('jadwal.show', $jadwalTayang->id) }}" class="neo-back-button">
        <x-heroicon-o-arrow-left class="w-4 h-4" />
        Kembali
      </a>
    </div>
  </x-slot>

  <div class="min-h-screen pt-8 pb-40 bg-amber-50">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

      <div class="flex items-start justify-between mb-8">
        <div>
          <p class="neo-subtitle">Film</p>
          <p class="neo-text">{{ $jadwalTayang->film->judul }}</p>
        </div>

        <div class="text-right">
          <p class="neo-subtitle">Studio</p>
          <p class="neo-text">
            {{ $jadwalTayang->studio->nama }}
            &bull;
            {{ $jadwalTayang->waktu_mulai->format('H:i') }}
          </p>
        </div>
      </div>

      <div class="flex flex-col items-center mb-10">
        <div class="w-3/4 h-2 bg-gray-900 rounded-b-[50%] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.3)]"></div>
        <p class="mt-2 text-xs font-bold tracking-widest text-gray-500 uppercase">
          LAYAR
        </p>
      </div>

      <form
        id="bookingForm"
        action="{{ route('jadwal.kursi.store', $jadwalTayang->id) }}"
        method="POST"
        x-data='{
                    selected: [],
                    maxSeats: 6,
                    price: {{ $jadwalTayang->harga }},
                    seats: @json($jadwalTayang->studio->kursis),

                    toggleSeat(seatId) {
                        if (this.selected.includes(seatId)) {
                            this.selected = this.selected.filter(id => id !== seatId);
                        } else {
                            if (this.selected.length < this.maxSeats) {
                                this.selected.push(seatId);
                            } else {
                                alert("Maksimal hanya boleh memilih 6 kursi!");
                            }
                        }
                    },

                    get total() {
                        return this.selected.length * this.price;
                    },

                    get seatLabels() {
                        return this.selected.map(id => {
                            const seat = this.seats.find(s => s.id === id);
                            return seat
                                ? `${seat.label_baris}${seat.nomor_kursi}`
                                : "";
                        }).join(", ");
                    },

                    submitBooking() {
                        if (this.selected.length === 0) {
                            alert("Silakan pilih minimal 1 kursi!");
                            return;
                        }

                        document.getElementById("kursi_ids_input").value =
                            JSON.stringify(this.selected);

                        document.getElementById("bookingForm").submit();
                    }
                }'>
        @csrf

        <input
          type="hidden"
          name="kursi_ids"
          id="kursi_ids_input">

        <div class="flex gap-4 mb-8 text-xs font-bold">
          <div class="flex items-center gap-2">
            <span class="w-6 h-6 bg-green-300 border-2 border-black rounded shadow-[2px_2px_0px_0px_#000]"></span>
            Tersedia
          </div>

          <div class="flex items-center gap-2">
            <span class="w-6 h-6 bg-yellow-300 border-2 border-black rounded shadow-[2px_2px_0px_0px_#000]"></span>
            Dipilih
          </div>

          <div class="flex items-center gap-2">
            <span class="w-6 h-6 bg-red-300 border-2 border-black rounded shadow-[2px_2px_0px_0px_#000]"></span>
            Terisi
          </div>
        </div>

        @php
        $kursiPerBaris = $jadwalTayang->studio->kursis->groupBy('label_baris');
        @endphp

        <div class="flex flex-col gap-3">
          @foreach($kursiPerBaris as $baris => $kursis)
          <div class="flex items-center gap-2">
            <span class="w-6 font-black text-center text-gray-900">
              {{ $baris }}
            </span>

            <div class="flex gap-2">
              @foreach($kursis as $kursi)
              <button
                type="button"
                @click.prevent="toggleSeat('{{ $kursi->id }}')"
                :class="{
                    'bg-green-300 hover:bg-green-400': !selected.includes('{{ $kursi->id }}'),
                    'bg-yellow-300': selected.includes('{{ $kursi->id }}')
                }"
                class="w-8 h-8 md:w-10 md:h-10 border-2 border-black rounded-lg shadow-[3px_3px_0px_0px_#000] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_#000] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all flex items-center justify-center text-[10px] font-bold text-gray-900"
                title="Kursi {{ $kursi->label_baris }}{{ $kursi->nomor_kursi }}">
                {{ $kursi->nomor_kursi }}
              </button>
              @endforeach
            </div>

            <span class="w-6 font-black text-center text-gray-900">
              {{ $baris }}
            </span>
          </div>
          @endforeach
        </div>

        <div class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-white border-t-4 border-black shadow-[0px_-5px_15px_-3px_rgba(0,0,0,0.1)]">
          <div class="flex flex-col items-center justify-between max-w-4xl gap-4 mx-auto sm:flex-row">

            <div class="w-full sm:w-auto">
              <p class="neo-subtitle">
                Kursi Dipilih
                (<span x-text="selected.length"></span>/6)
              </p>

              <p
                class="max-w-xs text-sm truncate neo-text"
                x-text="seatLabels || 'Belum ada kursi dipilih'"></p>
            </div>

            <div class="flex items-center justify-between w-full gap-4 sm:w-auto sm:justify-end">
              <div class="text-right">
                <p class="neo-subtitle">Total Harga</p>

                <p class="text-2xl neo-price">
                  Rp
                  <span x-text="total.toLocaleString('id-ID')"></span>
                </p>
              </div>

              <button
                type="button"
                @click.prevent="submitBooking()"
                :disabled="selected.length === 0"
                class="neo-button-primary disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-x-0 disabled:translate-y-0">
                Lanjutkan
                <x-heroicon-o-arrow-right class="inline w-4 h-4 ml-1" />
              </button>
            </div>

          </div>
        </div>
      </form>

    </div>
  </div>
</x-app-layout>
