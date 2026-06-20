<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ticketra. - Atur Ulang Password</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800;900&family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dot-matrix min-h-screen flex items-center justify-center p-4 md:p-8 relative font-base">
    <div class="w-full max-w-md">
        <div class="brutal-card p-8 md:p-12 bg-white">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-xl font-heading font-black uppercase tracking-tight mb-8">
                <i data-lucide="ticket" class="w-7 h-7 text-auth-foreground fill-auth-foreground/10"></i> Ticketra.
            </a>

            <h2 class="text-3xl uppercase mb-1">Password Baru</h2>
            <p class="font-medium text-gray-500 text-sm mb-6">Buat password baru yang kuat dan mudah diingat untuk akunmu.</p>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-accent-red/10 border-2 border-accent-red rounded-lg">
                    <p class="text-sm font-bold text-accent-red">
                        @foreach ($errors->all() as $error)
                            {{ $error }}@if (!$loop->last)<br>@endif
                        @endforeach
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Alamat Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="nama@email.com" class="brutal-input pl-12" required autofocus autocomplete="username">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Password Baru</label>
                    <div class="relative">
                        <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="password" name="password" placeholder="Min. 8 karakter" class="brutal-input pl-12" required autocomplete="new-password">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Konfirmasi Password</label>
                    <div class="relative">
                        <i data-lucide="check" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password" class="brutal-input pl-12" required autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="brutal-btn bg-accent-green text-white w-full !py-4 text-lg mt-4">
                    Simpan Password <i data-lucide="save" class="w-5 h-5"></i>
                </button>
            </form>

            <p class="text-center text-sm font-bold text-gray-500 mt-6">
                <a href="{{ route('auth.page') }}" class="text-auth-foreground underline decoration-2 hover:text-main">Kembali ke halaman masuk</a>
            </p>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
