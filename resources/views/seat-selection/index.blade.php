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

      @if (session('success'))
          <div class="px-4 py-3 mb-6 font-bold text-gray-900 border-4 border-black shadow-[6px_6px_0px_0px_#000] rounded-xl bg-pastel-mint flex items-center gap-3">
              <x-icon name="heroicon-s-check-circle" class="w-5 h-5 text-border shrink-0" />
              <div>{{ session('success') }}</div>
          </div>
      @endif

      @if (session('error'))
          <div class="px-4 py-3 mb-6 font-bold text-gray-900 border-4 border-black shadow-[6px_6px_0px_0px_#000] rounded-xl bg-pastel-peach flex items-center gap-3">
              <x-icon name="heroicon-s-exclamation-triangle" class="w-5 h-5 text-border shrink-0" />
              <div>{{ session('error') }}</div>
          </div>
      @endif

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
                    selectedSnacks: [],
                    maxSeats: 6,
                    price: {{ $jadwalTayang->harga }},
                    seats: @json($jadwalTayang->studio->kursis),
                    showSnackModal: false,
                    snacks: @json($snackSuggestions),

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

                    toggleSnack(snackId) {
                        if (this.selectedSnacks.includes(snackId)) {
                            this.selectedSnacks = this.selectedSnacks.filter(id => id !== snackId);
                        } else {
                            this.selectedSnacks.push(snackId);
                        }
                    },

                    isSnackSelected(snackId) {
                        return this.selectedSnacks.includes(snackId);
                    },

                    get snackTotal() {
                        return this.snacks
                            .filter(s => this.selectedSnacks.includes(s.id))
                            .reduce((sum, s) => sum + Number(s.price), 0);
                    },

                    get total() {
                        return this.selected.length * this.price;
                    },

                    get grandTotal() {
                        return this.total + this.snackTotal;
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
                        this.showSnackModal = true;
                    },

                    goToPayment() {
                        document.getElementById("kursi_ids_input").value =
                            JSON.stringify(this.selected);
                        document.getElementById("snack_ids_input").value =
                            JSON.stringify(this.selectedSnacks);
                        document.getElementById("bookingForm").submit();
                    },

                    goToSnacks() {
                        window.location.href = "{{ route('snacks.index') }}";
                    }
                }'>
        @csrf

        <input
          type="hidden"
          name="kursi_ids"
          id="kursi_ids_input">

        <input
          type="hidden"
          name="snack_ids"
          id="snack_ids_input">

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
        $cols = $kursiPerBaris->first()?->count() ?? 10;
        @endphp

        <div class="overflow-x-auto pb-4">
          <div class="flex flex-col items-center gap-3 mx-auto" style="width: fit-content;">
            @foreach($kursiPerBaris as $baris => $kursis)
            <div class="flex items-center justify-center gap-3">
              <span class="w-5 text-xs font-black text-center text-gray-500 md:w-6">
                {{ $baris }}
              </span>

              <div class="grid gap-1.5 md:gap-2" style="grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr));">
                @foreach($kursis as $kursi)
                  @php
                    $isOccupied = in_array($kursi->id, $occupiedSeats);
                  @endphp
                  @if($isOccupied)
                    <button
                      type="button"
                      disabled
                      class="w-8 h-8 md:w-10 md:h-10 border-2 border-black rounded-lg bg-red-300 text-gray-500 cursor-not-allowed shadow-none flex items-center justify-center text-[10px] font-bold"
                      title="Kursi {{ $kursi->label_baris }}{{ $kursi->nomor_kursi }} (Terisi)">
                      {{ $kursi->nomor_kursi }}
                    </button>
                  @else
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
                  @endif
                @endforeach
              </div>

              <span class="w-5 text-xs font-black text-center text-gray-500 md:w-6">
                {{ $baris }}
              </span>
            </div>
            @endforeach
          </div>
        </div>

        <div
          x-show="showSnackModal"
          x-cloak
          class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
          @click.away="showSnackModal = false"
          x-transition.opacity>
          <div class="w-full max-w-lg border-4 border-black rounded-2xl bg-white shadow-[10px_10px_0px_#000]" @click.stop>
            <div class="p-6 text-center border-b-4 border-black bg-pastel-lemon rounded-t-2xl">
              <div class="text-3xl mb-2">🍿</div>
              <h3 class="text-xl font-black uppercase">Tambahkan Camilan?</h3>
              <p class="mt-1 text-sm font-bold opacity-70">Pesan sekarang atau nanti di snack bar!</p>
            </div>

            <div class="p-6 space-y-3">
              <template x-for="snack in snacks" :key="snack.id">
                <button
                  type="button"
                  @click="toggleSnack(snack.id)"
                  :class="{
                      'border-accent-green bg-green-50 shadow-[3px_3px_0px_#059669]': isSnackSelected(snack.id),
                      'border-black bg-slate-50 shadow-[3px_3px_0px_#000]': !isSnackSelected(snack.id)
                  }"
                  class="flex items-center gap-4 p-4 border-2 rounded-xl transition-all hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_#000] w-full text-left">
                  <span class="flex items-center justify-center w-12 h-12 text-2xl bg-white border-2 border-black rounded-lg shrink-0" x-text="snack.emoji"></span>
                  <div class="flex-1 min-w-0">
                    <p class="font-black text-gray-900" x-text="snack.name"></p>
                    <p class="text-xs font-bold text-gray-500 truncate" x-text="snack.desc"></p>
                  </div>
                  <div class="flex flex-col items-end gap-1 shrink-0">
                    <p class="font-black whitespace-nowrap text-accent-red" x-text="'Rp ' + Number(snack.price).toLocaleString('id-ID')"></p>
                    <span
                      x-show="isSnackSelected(snack.id)"
                      class="text-[10px] font-extrabold text-accent-green uppercase tracking-wider">
                      ✓ Dipilih
                    </span>
                  </div>
                </button>
              </template>
            </div>

            <div class="px-6 pb-4">
              <div class="flex items-center justify-between p-4 border-2 border-black rounded-xl bg-pastel-lemon shadow-[3px_3px_0px_#000]">
                <span class="text-xs font-extrabold uppercase tracking-widest">Total Belanja</span>
                <span class="font-black text-accent-red" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span>
              </div>
            </div>

            <div class="p-6 pt-0 space-y-3">
              <button
                type="button"
                @click="goToPayment()"
                class="w-full py-4 font-black text-center uppercase border-4 border-black rounded-xl bg-accent-green text-white shadow-[5px_5px_0px_#000] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[3px_3px_0px_#000] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all">
                Lanjutkan ke Pembayaran
                <x-heroicon-o-arrow-right class="inline w-5 h-5 ml-1" />
              </button>

              <button
                type="button"
                @click="goToSnacks()"
                class="w-full py-3 font-bold text-center uppercase border-2 border-black rounded-xl bg-white shadow-[3px_3px_0px_#000] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_#000] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none transition-all">
                <x-heroicon-o-shopping-bag class="inline w-4 h-4 mr-1" />
                Pesan Makanan
              </button>

              <button
                type="button"
                @click="showSnackModal = false"
                class="w-full py-2 text-xs font-bold text-center text-gray-500 uppercase hover:text-gray-900 transition-colors">
                Nanti Saja, Lewati
              </button>
            </div>
          </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-secondary-background border-t-4 border-border shadow-[0px_-5px_15px_-3px_rgba(0,0,0,0.08)]">
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


