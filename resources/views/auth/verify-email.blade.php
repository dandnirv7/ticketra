<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ticketra. - Verifikasi Email</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800;900&family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dot-matrix min-h-screen flex items-center justify-center p-4 md:p-8 relative font-base">
    <div class="w-full max-w-md">
        <div class="brutal-card p-8 md:p-12 bg-white text-center">

            <a href="{{ url('/') }}" class="flex items-center gap-2 text-xl font-heading font-black uppercase tracking-tight mb-8 justify-center">
                <i data-lucide="ticket" class="w-7 h-7 text-auth-foreground fill-auth-foreground/10"></i> Ticketra.
            </a>

            <div class="w-20 h-20 mx-auto border-[3px] border-border rounded-3xl bg-pastel-mint shadow-[4px_4px_0px_var(--border)] flex items-center justify-center mb-6">
                <i data-lucide="mail-check" class="w-10 h-10 text-accent-green"></i>
            </div>

            <h2 class="text-2xl uppercase mb-2">Verifikasi Email</h2>
            <p class="font-medium text-gray-600 text-sm mb-1">Terima kasih sudah mendaftar! Sebelum lanjut,</p>
            <p class="font-medium text-gray-600 text-sm mb-6">klik tautan verifikasi yang baru saja kami kirim ke emailmu.</p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 p-3 bg-accent-green/10 border-2 border-accent-green rounded-lg text-left">
                    <p class="text-sm font-bold text-accent-green">Tautan verifikasi baru telah dikirim ke emailmu.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit" class="brutal-btn bg-pastel-sky w-full !py-3 text-sm">
                    Kirim Ulang Email <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-bold text-auth-foreground underline decoration-2 hover:text-main">
                    Logout
                </button>
            </form>

            <p class="text-xs font-bold text-gray-500 mt-6">
                Tautan hanya berlaku selama 60 menit. Cek folder spam bila tidak ditemukan.
            </p>

        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
