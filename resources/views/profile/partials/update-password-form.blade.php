<section>
    <header>
        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-1 text-xs font-semibold text-gray-500">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" />
            <div class="relative mt-1">
                <x-icon name="heroicon-s-lock-closed" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input id="update_password_current_password" name="current_password" :type="showCurrent ? 'text' : 'password'" class="brutal-input pl-12 pr-12 block w-full" autocomplete="current-password" />
                <button type="button" @click="showCurrent = !showCurrent" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer focus:outline-none select-none">
                    <span x-show="showCurrent"><x-icon name="heroicon-s-eye-slash" class="w-5 h-5 text-gray-400 hover:text-gray-600" /></span>
                    <span x-show="!showCurrent"><x-icon name="heroicon-s-eye" class="w-5 h-5 text-gray-400 hover:text-gray-600" /></span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" />
            <div class="relative mt-1">
                <x-icon name="heroicon-s-lock-closed" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input id="update_password_password" name="password" :type="showNew ? 'text' : 'password'" class="brutal-input pl-12 pr-12 block w-full" autocomplete="new-password" />
                <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer focus:outline-none select-none">
                    <span x-show="showNew"><x-icon name="heroicon-s-eye-slash" class="w-5 h-5 text-gray-400 hover:text-gray-600" /></span>
                    <span x-show="!showNew"><x-icon name="heroicon-s-eye" class="w-5 h-5 text-gray-400 hover:text-gray-600" /></span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <div class="relative mt-1">
                <x-icon name="heroicon-s-lock-closed" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input id="update_password_password_confirmation" name="password_confirmation" :type="showConfirm ? 'text' : 'password'" class="brutal-input pl-12 pr-12 block w-full" autocomplete="new-password" />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer focus:outline-none select-none">
                    <span x-show="showConfirm"><x-icon name="heroicon-s-eye-slash" class="w-5 h-5 text-gray-400 hover:text-gray-600" /></span>
                    <span x-show="!showConfirm"><x-icon name="heroicon-s-eye" class="w-5 h-5 text-gray-400 hover:text-gray-600" /></span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="brutal-btn bg-primary text-border select-none">
                {{ __('Simpan Kata Sandi') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
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
