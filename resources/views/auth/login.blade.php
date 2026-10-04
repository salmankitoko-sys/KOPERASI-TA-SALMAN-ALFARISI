<x-guest-layout>
    <div class="min-h-screen flex items-stretch bg-slate-50">
        <div class="hidden lg:flex w-1/2 relative overflow-hidden bg-animate-gradient"
             style="background-image: linear-gradient(135deg, #312e81 0%, #4338ca 40%, #6d28d9 70%, #7c3aed 100%);">

            {{-- Decorative floating shapes --}}
            <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full bg-white/10 blur-2xl animate-float"></div>
            <div class="absolute bottom-10 -left-20 w-80 h-80 rounded-full bg-fuchsia-400/20 blur-3xl animate-float-slow"></div>
            <div class="absolute top-1/3 left-8 w-24 h-24 rounded-3xl bg-white/10 rotate-12 animate-float-slow"
                 style="animation-delay: 1.2s"></div>
            <div class="absolute bottom-1/4 right-12 w-16 h-16 rounded-full bg-white/10 animate-float"
                 style="animation-delay: 0.6s"></div>

            {{-- Dot grid overlay --}}
            <div class="absolute inset-0 bg-dot-grid"></div>

            {{-- Branding content --}}
            <div class="relative z-10 flex flex-col justify-between px-12 py-14 text-white max-w-xl">
                <div class="animate-fade-in-up">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-lg font-black shadow-lg">
                            KD
                        </div>
                        <div class="leading-tight">
                            <p class="font-bold text-lg">SIPDKS</p>
                            <p class="text-xs text-indigo-200">Sistem Informasi Platform Digital Koperasi Syariah</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-7 animate-fade-in-up anim-delay-200">
                    <div class="relative">
                        <span class="absolute inline-flex h-12 w-12 rounded-full bg-white/20 pulse-ring"></span>
                        <span class="relative inline-flex h-12 w-12 rounded-full bg-white/25 backdrop-blur items-center justify-center text-2xl">🕌</span>
                    </div>

                    <h1 class="text-4xl font-black leading-tight tracking-tight">
                        Simpanan, Pembiayaan<br> & MarketPlace<br> Dalam Satu Ekosistem
                    </h1>
                    <p class="text-indigo-100/90 text-base leading-relaxed max-w-md">
                        Kelola simpanan syariah, pengajuan pembiayaan, hingga jual-beli produk halal
                        secara transparan dan sesuai prinsip syariah.
                    </p>

                    {{-- Mini statistik animasi --}}
                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 text-center animate-fade-in-up anim-delay-300">
                            <p class="text-2xl font-black">4+</p>
                            <p class="text-[11px] text-indigo-200 mt-1">Akad Syariah</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 text-center animate-fade-in-up anim-delay-400">
                            <p class="text-2xl font-black">100%</p>
                            <p class="text-[11px] text-indigo-200 mt-1">Transparan</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 text-center animate-fade-in-up anim-delay-500">
                            <p class="text-2xl font-black">24/7</p>
                            <p class="text-[11px] text-indigo-200 mt-1">Akses Online</p>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-indigo-200/70 animate-fade-in-up anim-delay-600">
                    © {{ date('Y') }} SIPDKS — Berizin & Diawasi
                </div>
            </div>
        </div>

        {{-- ================= RIGHT: FORM PANEL ================= --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-lg animate-fade-in-up">

                {{-- Logo mobile --}}
                <div class="flex lg:hidden items-center justify-center gap-3 mb-8">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-lg font-black shadow-lg shadow-indigo-600/30">
                        KD
                    </div>
                    <div class="leading-tight">
                        <p class="font-bold text-gray-900">SIPDKS</p>
                        <p class="text-xs text-gray-500">Sistem Informasi Platform Digital Koperasi Syariah</p>
                    </div>
                </div>

                <h1 class="text-3xl font-black text-gray-900">Selamat Datang 👋</h1>
                <p class="text-gray-500 mt-1.5 mb-8">Masuk untuk melanjutkan ke dashboard anggota Anda.</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Alert error global --}}
                @if ($errors->has('email') || $errors->has('password'))
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-start gap-2"
                         x-data="{ show: true }" x-show="show" x-cloak>
                        <span class="text-base leading-none mt-0.5">⚠️</span>
                        <div class="flex-1">
                            <p class="font-bold">Gagal masuk</p>
                            <p class="text-red-600/90 mt-0.5">{{ $errors->first('email') ?? $errors->first('password') }}</p>
                        </div>
                        <button type="button" @click="show = false" class="text-red-400 hover:text-red-600 font-bold px-1">✕</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}"
                      x-data="{
                          loading: false,
                          showPassword: false,
                          shake: false
                      }"
                      @submit="loading = true"
                      class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <div class="relative">
                            <input id="email" type="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder=" "
                                   required autofocus
                                   autocomplete="username"
                                   class="auth-input peer @error('email') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="email" class="auth-label">Alamat Email</label>
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="relative {{ $errors->has('password') ? 'animate-shake' : '' }}">
                            <input id="password" :type="showPassword ? 'text' : 'password'"
                                   name="password" placeholder=" " required
                                   autocomplete="current-password"
                                   class="auth-input peer pr-12 @error('password') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="password" class="auth-label">Kata Sandi</label>

                            {{-- Toggle password --}}
                            <button type="button" @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition p-1">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember & forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none group">
                            <input type="checkbox" name="remember"
                                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition group-hover:border-indigo-400">
                            Ingat saya
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            :disabled="loading"
                            class="group relative w-full overflow-hidden rounded-xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-700/30 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <span x-show="!loading" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                            Masuk Sekarang
                        </span>
                        <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>

                    {{-- Divider --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">atau</span>
                        </div>
                    </div>

                    {{-- Back to home --}}
                    <a href="{{ route('home') }}"
                       class="flex items-center justify-center gap-2 w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm font-semibold text-slate-600 transition hover:border-indigo-300 hover:text-indigo-700 hover:bg-indigo-50/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </form>

                <p class="mt-8 text-center text-sm text-slate-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                       class="font-bold text-indigo-600 hover:text-indigo-800 transition underline-offset-4 hover:underline">
                        Daftar sebagai anggota
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
