<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticketra. - Email Terverifikasi</title>

    <x-favicons />

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
                <i data-lucide="badge-check" class="w-10 h-10 text-accent-green"></i>
            </div>

            <h2 class="text-2xl uppercase mb-2">Email Terverifikasi!</h2>
            <p class="font-medium text-gray-600 text-sm mb-1">Email <strong class="text-auth-foreground">{{ $email }}</strong></p>
            <p class="font-medium text-gray-600 text-sm mb-6">berhasil diverifikasi. Akun kamu sudah aktif.</p>

            <a href="{{ route('login') }}" class="brutal-btn bg-lime-neon w-full !py-4 text-base">
                Masuk Sekarang <i data-lucide="arrow-right-end-on-rectangle" class="w-5 h-5"></i>
            </a>

            <p class="text-xs font-bold text-gray-500 mt-6">
                Kamu bisa login kapan saja dan langsung pesan tiket.
            </p>

        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
