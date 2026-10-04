<x-guest-layout>
    <div class="min-h-screen flex items-stretch bg-slate-50">

        {{-- ================= LEFT: FORM PANEL ================= --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-10">
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

                <h1 class="text-3xl font-black text-gray-900">Buat Akun Baru ✨</h1>
                <p class="text-gray-500 mt-1.5 mb-7">Pilih akun belanja marketplace atau akun anggota koperasi.</p>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                         x-data="{ show: true }" x-show="show" x-cloak>
                        <div class="flex items-start gap-2">
                            <span class="text-base leading-none mt-0.5">⚠️</span>
                            <div class="flex-1">
                                <p class="font-bold">Periksa kembali data Anda</p>
                                <ul class="mt-1 list-disc list-inside space-y-0.5 text-red-600/90">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" @click="show = false" class="text-red-400 hover:text-red-600 font-bold px-1">✕</button>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}"
                      x-data="{
                          loading: false,
                          accountType: @js(old('account_type', request('as') === 'pelanggan' ? 'pelanggan' : 'anggota')),
                          showPassword: false,
                          showConfirm: false,
                          password: @js(old('password', '')),
                          confirm: @js(old('password_confirmation', '')),
                          get strength() {
                              let score = 0;
                              const p = this.password;
                              if (!p) return { label: '', color: '', width: 0 };
                              if (p.length >= 8) score++;
                              if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
                              if (/\d/.test(p)) score++;
                              if (/[^A-Za-z0-9]/.test(p)) score++;
                              const map = [
                                  { label: 'Lemah', color: 'bg-red-500', text: 'text-red-600', width: 25 },
                                  { label: 'Cukup', color: 'bg-amber-500', text: 'text-amber-600', width: 50 },
                                  { label: 'Kuat', color: 'bg-emerald-500', text: 'text-emerald-600', width: 75 },
                                  { label: 'Sangat Kuat', color: 'bg-emerald-600', text: 'text-emerald-700', width: 100 },
                              ];
                              return map[score] || { label: 'Lemah', color: 'bg-red-500', text: 'text-red-600', width: 25 };
                          },
                          get passwordMatch() {
                              return this.confirm.length > 0 && this.password === this.confirm;
                          },
                          get confirmMismatch() {
                              return this.confirm.length > 0 && this.password !== this.confirm;
                          }
                      }"
                      @submit="loading = true"
                      class="space-y-4">
                    @csrf

                    {{-- Jenis akun --}}
                    <div>
                        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Daftar sebagai</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative cursor-pointer rounded-2xl border bg-white p-4 transition"
                                   :class="accountType === 'pelanggan' ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200 hover:border-indigo-200'">
                                <input type="radio" name="account_type" value="pelanggan" x-model="accountType" class="sr-only">
                                <span class="block text-sm font-black text-gray-900">Pelanggan Marketplace</span>
                                <span class="mt-1 block text-xs leading-relaxed text-gray-500">Untuk belanja, checkout, dan melihat pesanan.</span>
                            </label>
                            <label class="relative cursor-pointer rounded-2xl border bg-white p-4 transition"
                                   :class="accountType === 'anggota' ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200 hover:border-indigo-200'">
                                <input type="radio" name="account_type" value="anggota" x-model="accountType" class="sr-only">
                                <span class="block text-sm font-black text-gray-900">Anggota Koperasi</span>
                                <span class="mt-1 block text-xs leading-relaxed text-gray-500">Untuk simpanan, pembiayaan, toko, dan marketplace.</span>
                            </label>
                        </div>
                        @error('account_type') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nama lengkap --}}
                    <div>
                        <div class="relative">
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   placeholder=" " required autofocus autocomplete="name"
                                   class="auth-input peer @error('name') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="name" class="auth-label">Nama Lengkap</label>
                        </div>
                        @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- No HP & Tanggal lahir --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="relative">
                                <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}"
                                       placeholder=" " required autocomplete="tel"
                                       class="auth-input peer @error('no_hp') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                                <label for="no_hp" class="auth-label">No. HP / WA</label>
                            </div>
                            @error('no_hp') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div x-show="accountType === 'anggota'" x-cloak>
                            <div class="relative">
                                <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                       placeholder=" " :required="accountType === 'anggota'"
                                       class="auth-input peer @error('tanggal_lahir') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                                <label for="tanggal_lahir" class="auth-label">Tanggal Lahir</label>
                            </div>
                            @error('tanggal_lahir') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   placeholder=" " required autocomplete="email"
                                   class="auth-input peer @error('email') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="email" class="auth-label">Alamat Email</label>
                        </div>
                        @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'"
                                   name="password" placeholder=" " required autocomplete="new-password"
                                   x-model="password"
                                   class="auth-input peer pr-12 @error('password') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="password" class="auth-label">Kata Sandi</label>
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

                        {{-- Strength meter --}}
                        <div class="mt-2" x-cloak x-show="password.length > 0">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         :class="strength.color"
                                         :style="'width: ' + strength.width + '%'"></div>
                                </div>
                                <span class="text-[11px] font-bold" :class="strength.text" x-text="strength.label"></span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Min. 8 karakter, kombinasi huruf, angka, dan simbol.</p>
                        </div>
                        @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Konfirmasi password --}}
                    <div>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                                   name="password_confirmation" placeholder=" " required autocomplete="new-password"
                                   x-model="confirm"
                                   class="auth-input peer pr-12 @error('password') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="password_confirmation" class="auth-label">Ulangi Kata Sandi</label>
                            <button type="button" @click="showConfirm = !showConfirm"
                                    :aria-label="showConfirm ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition p-1">
                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Validator cocok/tidak --}}
                        <div class="mt-1.5 flex items-center gap-1.5 text-xs font-semibold"
                             x-cloak x-show="confirm.length > 0">
                            <template x-if="passwordMatch">
                                <span class="text-emerald-600 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Kata sandi cocok
                                </span>
                            </template>
                            <template x-if="confirmMismatch">
                                <span class="text-red-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Kata sandi tidak cocok
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Syarat & ketentuan --}}
                    <div class="flex items-start gap-2.5 rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3">
                        <input type="checkbox" name="terms" id="terms" required @error('terms') class="mt-1 rounded border-red-400 text-indigo-600 focus:ring-indigo-500" @else class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @enderror>
                        <label for="terms" class="text-xs text-slate-600 leading-relaxed">
                            Saya setuju dengan
                            <a href="#" class="font-semibold text-indigo-600 hover:underline">syarat & ketentuan</a>
                            dan
                            <a href="#" class="font-semibold text-indigo-600 hover:underline">kebijakan privasi</a>
                            koperasi.
                        </label>
                    </div>
                    @error('terms') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                    {{-- Submit --}}
                    <button type="submit"
                            :disabled="loading"
                            class="group relative w-full overflow-hidden rounded-xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-700/30 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <span x-show="!loading" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3.375 19.5a7.125 7.125 0 0114.25 0v.75h-14.25v-.75z"/>
                            </svg>
                            Buat Akun & Masuk
                        </span>
                        <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Membuat akun...
                        </span>
                    </button>

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
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                       class="font-bold text-indigo-600 hover:text-indigo-800 transition underline-offset-4 hover:underline">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

        {{-- ================= RIGHT: BRANDING PANEL ================= --}}
        <div class="hidden lg:flex w-1/2 relative overflow-hidden bg-animate-gradient"
             style="background-image: linear-gradient(135deg, #7c3aed 0%, #6d28d9 30%, #4338ca 65%, #312e81 100%);">

            {{-- Decorative floating shapes --}}
            <div class="absolute -bottom-20 -right-16 w-80 h-80 rounded-full bg-white/10 blur-3xl animate-float-slow"></div>
            <div class="absolute top-16 -left-14 w-72 h-72 rounded-full bg-fuchsia-400/20 blur-3xl animate-float"></div>
            <div class="absolute top-1/4 right-10 w-20 h-20 rounded-2xl bg-white/10 -rotate-12 animate-float"
                 style="animation-delay: 0.8s"></div>
            <div class="absolute bottom-1/3 left-12 w-14 h-14 rounded-full bg-white/10 animate-float-slow"
                 style="animation-delay: 1.4s"></div>

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

                <div class="space-y-8 animate-fade-in-up anim-delay-200">
                    <h2 class="text-3xl font-black leading-tight tracking-tight">
                        Kenapa Bergabung<br> Jadi Anggota?
                    </h2>

                    {{-- Benefit list --}}
                    <div class="space-y-5">
                        <div class="flex items-start gap-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 animate-fade-in-up anim-delay-300">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg">💸</span>
                            <div>
                                <p class="font-bold">Simpanan Syariah</p>
                                <p class="text-sm text-indigo-100/90 mt-0.5">Pokok, wajib, sukarela, hingga mudharabah berbasis bagi hasil.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 animate-fade-in-up anim-delay-400">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg">📈</span>
                            <div>
                                <p class="font-bold">Pembiayaan Tanpa Riba</p>
                                <p class="text-sm text-indigo-100/90 mt-0.5">Akad murabahah, mudharabah, musyarakah, dan ijarah.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 animate-fade-in-up anim-delay-500">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg">🛍️</span>
                            <div>
                                <p class="font-bold">Marketplace Halal</p>
                                <p class="text-sm text-indigo-100/90 mt-0.5">Buka lapak dan jual produk dengan ekosistem syariah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-indigo-200/70 animate-fade-in-up anim-delay-600">
                    © {{ date('Y') }} SIPDKS — Berizin & Diawasi
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
