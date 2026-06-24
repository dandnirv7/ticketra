<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ticketra. - Autentikasi</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800;900&family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $currentView = request('view', 'login');
    if (!in_array($currentView, ['login', 'register', 'forgot', 'verify'])) {
        $currentView = 'login';
    }
    $views = [
        'login' => ['bg' => 'bg-pastel-sky', 'icon' => 'heroicon-s-shopping-bag', 'title' => 'Welcome Back!', 'desc' => 'Kursi strategis dan snack favoritmu sudah menanti. Yuk masuk!'],
        'register' => ['bg' => 'bg-pastel-pink', 'icon' => 'heroicon-s-ticket', 'title' => 'Join The Club', 'desc' => 'Daftar sekarang dan nikmati promo pengguna baru hingga 50%.'],
        'forgot' => ['bg' => 'bg-pastel-lemon', 'icon' => 'heroicon-s-question-mark-circle', 'title' => 'Lupa Sandi?', 'desc' => 'Tenang, gak perlu panik. Masukkan emailmu dan kami kirim tautan reset.'],
        'verify' => ['bg' => 'bg-pastel-lavender', 'icon' => 'heroicon-s-envelope-open', 'title' => 'Cek Emailmu', 'desc' => 'Kami sudah mengirim tautan verifikasi ke kotak masukmu.'],
    ];
    $v = $views[$currentView];
@endphp
<body class="bg-dot-matrix min-h-screen flex items-center justify-center p-4 md:p-8 relative font-base"
      x-data="{
          view: '{{ $currentView }}',
          showPassword: false,
          toast: { show: false, msg: '', type: 'info' },
          timer: null,
          init() {
              @if (session('status'))
              this.showToast('{{ addslashes(session('status')) }}', 'info');
              @endif
              @if ($errors->any())
              this.showToast('{{ addslashes($errors->first()) }}', 'error');
              @endif
          },
          goTo(v) {
              this.view = v;
              const u = new URL(window.location);
              if (v === 'login') u.searchParams.delete('view');
              else u.searchParams.set('view', v);
              window.history.replaceState({}, '', u);
          },
          showToast(msg, type = 'info') {
              if (this.timer) clearTimeout(this.timer);
              this.toast = { show: true, msg, type };
              this.timer = setTimeout(() => this.toast.show = false, 3500);
          }
      }">

    
    <div x-show="toast.show" x-transition.opacity.duration.300ms
         :class="toast.type === 'error' ? 'bg-accent-red' : 'bg-auth-foreground'"
         class="fixed top-8 left-1/2 -translate-x-1/2 z-[80] text-white px-6 py-3 rounded-full font-bold text-sm shadow-lg flex items-center gap-2 whitespace-nowrap"
         style="display: none;">
        <x-icon name="heroicon-s-information-circle" class="w-4 h-4 text-accent-yellow" />
        <span x-text="toast.msg"></span>
    </div>

    <div class="w-full max-w-5xl">
        <div class="brutal-card flex flex-col md:flex-row overflow-hidden min-h-[600px] bg-white">

            
            <div class="w-full md:w-5/12 p-8 md:p-12 flex flex-col justify-between border-b-[3px] md:border-b-0 md:border-r-[3px] border-border transition-colors duration-500 relative overflow-hidden {{ $v['bg'] }}">

                <div class="absolute inset-0 opacity-[0.06] pointer-events-none bg-dot-matrix"></div>

                
                <a href="{{ url('/') }}" class="relative z-10 flex items-center gap-2 text-2xl font-heading font-black uppercase tracking-tight">
                    <x-icon name="heroicon-s-ticket" class="w-8 h-8 text-auth-foreground fill-auth-foreground/10" />
                    Ticketra.
                </a>

                
                <div class="relative z-10 mt-12 md:mt-0 flex flex-col items-center md:items-start text-center md:text-left">
                    <div class="w-24 h-24 md:w-32 md:h-32 border-[3px] border-border rounded-3xl bg-white shadow-[4px_4px_0px_var(--border)] flex items-center justify-center mb-6 transform -rotate-6 hover:rotate-0 hover:scale-110 transition-all duration-300">
                        <x-icon name="{{ $v['icon'] }}" class="w-12 h-12 md:w-16 md:h-16 text-border" />
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black uppercase leading-[1.1] mb-3">
                        {{ $v['title'] }}
                    </h2>
                    <p class="font-bold opacity-80 text-sm md:text-base border-l-[3px] border-foreground pl-3 md:pl-4">
                        {{ $v['desc'] }}
                    </p>
                </div>

                
                <div class="hidden md:block relative z-10 mt-auto pt-8">
                    <div class="flex gap-2 opacity-50">
                        <div class="w-3 h-3 rounded-full border-2 border-border {{ $currentView === 'login' ? 'bg-auth-foreground' : '' }}"></div>
                        <div class="w-3 h-3 rounded-full border-2 border-border {{ $currentView === 'register' ? 'bg-auth-foreground' : '' }}"></div>
                        <div class="w-3 h-3 rounded-full border-2 border-border {{ $currentView === 'forgot' ? 'bg-auth-foreground' : '' }}"></div>
                        <div class="w-3 h-3 rounded-full border-2 border-border {{ $currentView === 'verify' ? 'bg-auth-foreground' : '' }}"></div>
                    </div>
                </div>
            </div>

            
            <div class="w-full md:w-7/12 p-8 md:p-12 relative bg-white">
                <a href="{{ url('/') }}" class="absolute top-6 right-6 md:top-8 md:right-8 text-xs font-black uppercase underline decoration-2 hover:text-main flex items-center gap-1 transition-colors">
                    <x-icon name="heroicon-s-arrow-left" class="w-4 h-4" /> Beranda
                </a>

                <div class="h-full flex flex-col justify-center max-w-sm mx-auto w-full pt-8 md:pt-0">

                    
                    <div x-show="view === 'login'" x-transition.opacity class="space-y-6 w-full" style="display: none;">
                        <div class="mb-8">
                            <h3 class="text-3xl uppercase mb-1">Masuk</h3>
                            <p class="font-medium text-gray-500">Lanjutkan keseruan nontonmu.</p>
                        </div>

                        <form action="{{ route('login') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Alamat Email</label>
                                <div class="relative">
                                    <x-icon name="heroicon-s-envelope" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="brutal-input pl-12" required autofocus autocomplete="email">
                                </div>
                                @error('email')<p class="text-xs font-bold text-accent-red mt-1 ml-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <div class="flex justify-between items-end mb-1.5 ml-1">
                                    <label class="block text-xs font-black uppercase tracking-widest">Password</label>
                                    <a href="#" @click.prevent="goTo('forgot')" class="text-[10px] font-bold text-main hover:underline">Lupa Password?</a>
                                </div>
                                <div class="relative">
                                    <x-icon name="heroicon-s-lock-closed" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input :type="showPassword ? 'text' : 'password'" name="password" placeholder="••••••••" class="brutal-input pl-12 pr-12" required autocomplete="current-password">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer">
                                        <span x-show="showPassword"><x-icon name="heroicon-s-eye-slash" class="w-5 h-5 text-gray-400 hover:text-auth-foreground" /></span><span x-show="!showPassword"><x-icon name="heroicon-s-eye" class="w-5 h-5 text-gray-400 hover:text-auth-foreground" /></span>
                                    </button>
                                </div>
                                @error('password')<p class="text-xs font-bold text-accent-red mt-1 ml-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center ml-1">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="remember" value="1" class="w-4 h-4 border-2 border-border rounded cursor-pointer accent-main">
                                    <span class="ms-2 text-sm font-bold text-gray-600">Ingat saya</span>
                                </label>
                            </div>

                            <button type="submit" class="brutal-btn bg-lime-neon w-full !py-4 text-lg mt-2">
                                Masuk <x-icon name="heroicon-s-arrow-right-end-on-rectangle" class="w-5 h-5" />
                            </button>
                        </form>

                        <p class="text-center text-sm font-bold text-gray-500 mt-6">
                            Belum punya akun?
                            <a href="#" @click.prevent="goTo('register')" class="text-auth-foreground underline decoration-2 hover:text-main">Daftar di sini</a>
                        </p>
                    </div>

                    
                    <div x-show="view === 'register'" x-transition.opacity class="space-y-5 w-full" style="display: none;">
                        <div class="mb-6">
                            <h3 class="text-3xl uppercase mb-1">Daftar Baru</h3>
                            <p class="font-medium text-gray-500">Gabung dan klaim promo pengguna baru.</p>
                        </div>

                        <form action="{{ route('register') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                                <div class="relative">
                                    <x-icon name="heroicon-s-user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" class="brutal-input pl-12" required autofocus autocomplete="name">
                                </div>
                                @error('name')<p class="text-xs font-bold text-accent-red mt-1 ml-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Alamat Email</label>
                                <div class="relative">
                                    <x-icon name="heroicon-s-envelope" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="brutal-input pl-12" required autocomplete="email">
                                </div>
                                @error('email')<p class="text-xs font-bold text-accent-red mt-1 ml-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Password</label>
                                <div class="relative">
                                    <x-icon name="heroicon-s-lock-closed" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input :type="showPassword ? 'text' : 'password'" name="password" placeholder="Min. 8 karakter" class="brutal-input pl-12 pr-12" required autocomplete="new-password">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer">
                                        <span x-show="showPassword"><x-icon name="heroicon-s-eye-slash" class="w-5 h-5 text-gray-400 hover:text-auth-foreground" /></span><span x-show="!showPassword"><x-icon name="heroicon-s-eye" class="w-5 h-5 text-gray-400 hover:text-auth-foreground" /></span>
                                    </button>
                                </div>
                                @error('password')<p class="text-xs font-bold text-accent-red mt-1 ml-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Konfirmasi Password</label>
                                <div class="relative">
                                    <x-icon name="heroicon-s-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password" class="brutal-input pl-12 pr-12" required autocomplete="new-password">
                                </div>
                            </div>

                            <button type="submit" class="brutal-btn bg-pastel-pink w-full !py-4 text-lg mt-2">
                                Daftar <x-icon name="heroicon-s-user-plus" class="w-5 h-5" />
                            </button>
                        </form>

                        <p class="text-center text-sm font-bold text-gray-500 mt-6">
                            Sudah punya akun?
                            <a href="#" @click.prevent="goTo('login')" class="text-auth-foreground underline decoration-2 hover:text-main">Masuk di sini</a>
                        </p>
                    </div>

                    
                    <div x-show="view === 'forgot'" x-transition.opacity class="space-y-6 w-full" style="display: none;">
                        <div class="mb-8">
                            <button type="button" @click="goTo('login')" class="w-8 h-8 border-[3px] border-border rounded-full flex items-center justify-center hover:bg-gray-100 mb-4 transition-colors cursor-pointer">
                                <x-icon name="heroicon-s-arrow-left" class="w-4 h-4" />
                            </button>
                            <h3 class="text-3xl uppercase mb-1">Lupa Password?</h3>
                            <p class="font-medium text-gray-500 text-sm">Masukkan email yang terdaftar, kami akan mengirimkan tautan untuk mengatur ulang password.</p>
                        </div>

                        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-1.5 ml-1">Alamat Email</label>
                                <div class="relative">
                                    <x-icon name="heroicon-s-envelope" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="brutal-input pl-12" required autofocus autocomplete="email">
                                </div>
                                @error('email')<p class="text-xs font-bold text-accent-red mt-1 ml-1">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="brutal-btn bg-pastel-lemon w-full !py-4 text-lg mt-4">
                                Kirim Tautan Reset <x-icon name="heroicon-s-paper-airplane" class="w-5 h-5" />
                            </button>
                        </form>
                    </div>

                    
                    <div x-show="view === 'verify'" x-transition.opacity class="space-y-6 w-full" style="display: none;">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto border-[3px] border-border rounded-3xl bg-pastel-mint shadow-[4px_4px_0px_var(--border)] flex items-center justify-center mb-6">
                                <x-icon name="heroicon-s-envelope-open" class="w-10 h-10 text-accent-green" />
                            </div>
                            <h3 class="text-2xl uppercase mb-2">Cek Emailmu!</h3>
                            <p class="font-medium text-gray-600 text-sm mb-1">Kami sudah mengirim tautan verifikasi ke</p>
                            <p class="font-black text-auth-foreground text-base mb-4 break-all">{{ request('email') ?? old('email') }}</p>
                            <p class="text-xs font-bold text-gray-500 mb-6">Klik tautan di email untuk mengaktifkan akun. Tautan hanya berlaku selama 60 menit. Cek folder spam bila tidak ditemukan.</p>

                            <form action="{{ route('verification.send') }}" method="POST" class="mb-4">
                                @csrf
                                <input type="hidden" name="email" value="{{ request('email') ?? old('email') }}">
                                <button type="submit" class="brutal-btn bg-pastel-sky w-full !py-3 text-sm">
                                    Kirim Ulang Email <x-icon name="heroicon-s-arrow-path" class="w-4 h-4" />
                                </button>
                            </form>

                            <a href="#" @click.prevent="goTo('login')" class="block w-full mt-4 text-sm font-bold text-auth-foreground underline decoration-2 hover:text-main cursor-pointer">
                                Kembali ke halaman Masuk
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
