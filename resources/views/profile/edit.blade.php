<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black leading-tight text-gray-900">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="brutal-card p-6 md:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="brutal-card p-6 md:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="brutal-card p-6 md:p-8 border-accent-red/20">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
