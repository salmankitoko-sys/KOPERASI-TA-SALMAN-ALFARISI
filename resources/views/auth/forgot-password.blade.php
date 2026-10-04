<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12">
        <div class="w-full max-w-lg animate-fade-in-up">
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-8 sm:p-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-lg font-black shadow-lg shadow-indigo-600/25">
                        KD
                    </div>
                    <div class="leading-tight">
                        <p class="font-bold text-gray-900">SIPDKS</p>
                        <p class="text-xs text-gray-500">Reset Kata Sandi</p>
                    </div>
                </div>

                <h1 class="text-2xl font-black text-gray-900">Lupa Kata Sandi?</h1>
                <p class="text-sm text-gray-500 mt-1.5 mb-6 leading-relaxed">
                    Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                </p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first('email') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   placeholder=" " required autofocus autocomplete="username"
                                   class="auth-input peer @error('email') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="email" class="auth-label">Alamat Email</label>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-700/30 hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                        Kirim Tautan Reset
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 font-semibold text-indigo-600 hover:text-indigo-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Kembali ke halaman masuk
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
