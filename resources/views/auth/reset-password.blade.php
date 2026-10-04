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
                        <p class="text-xs text-gray-500">Atur Ulang Kata Sandi</p>
                    </div>
                </div>

                <h1 class="text-2xl font-black text-gray-900">Buat Kata Sandi Baru</h1>
                <p class="text-sm text-gray-500 mt-1.5 mb-6">
                    Masukkan kata sandi baru Anda untuk melanjutkan.
                </p>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                                   placeholder=" " required autofocus autocomplete="username"
                                   class="auth-input peer @error('email') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="email" class="auth-label">Alamat Email</label>
                        </div>
                        @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="relative">
                            <input id="password" type="password" name="password" placeholder=" " required
                                   autocomplete="new-password"
                                   class="auth-input peer pr-12 @error('password') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="password" class="auth-label">Kata Sandi Baru</label>
                        </div>
                        @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   placeholder=" " required autocomplete="new-password"
                                   class="auth-input peer @error('password_confirmation') border-red-400 focus:border-red-400 focus:ring-red-500/10 @enderror">
                            <label for="password_confirmation" class="auth-label">Ulangi Kata Sandi Baru</label>
                        </div>
                        @error('password_confirmation') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-700/30 hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Atur Ulang Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
