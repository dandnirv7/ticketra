<section>
    <header>
        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-xs font-semibold text-gray-500">
            {{ __("Perbarui informasi profil akun dan alamat email Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        
        <div x-data="{ selectedEmoji: '{{ old('avatar_emoji', $user->avatar_emoji ?? '🍿') }}' }">
            <x-input-label :value="__('Pilih Avatar Emoji')" class="mb-2" />
            <input type="hidden" name="avatar_emoji" :value="selectedEmoji">
            
            <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-9 gap-2 p-3 border-[3px] border-border rounded-xl bg-slate-50">
                @foreach(['🍿', '🎬', '🕶️', '🎟️', '🥤', '🍕', '🦁', '🐱', '🐶', '🦄', '👽', '🤖', '👑', '⭐', '🔥', '⚡', '🎮', '🎧', '🎸', '🎨', '🚀', '🔮', '🧸', '💡'] as $emoji)
                    <button type="button" 
                        @click="selectedEmoji = '{{ $emoji }}'"
                        :class="selectedEmoji === '{{ $emoji }}' ? 'bg-accent border-border shadow-[2px_2px_0px_rgba(0,0,0,1)] scale-110' : 'bg-white border-border/20 hover:border-border hover:shadow-[2px_2px_0px_rgba(0,0,0,1)]'"
                        class="aspect-square flex items-center justify-center text-2xl rounded-lg border-2 transition-all active:scale-95 focus:outline-none">
                        {{ $emoji }}
                    </button>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar_emoji')" />
        </div>

        
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-xs font-bold text-gray-800">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline text-xs text-gray-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-black text-xs text-green-600">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        
        <div>
            <x-input-label for="phone" :value="__('Nomor Telepon')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" placeholder="Contoh: 081234567890" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        
        <div class="flex items-center gap-4">
            <x-primary-button class="brutal-btn bg-primary text-border select-none">
                {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-bold text-green-600 flex items-center gap-1"
                >
                    <span>✓</span> {{ __('Berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
