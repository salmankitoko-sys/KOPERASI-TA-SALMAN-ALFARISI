<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12">
        <div class="w-full max-w-lg animate-fade-in-up">
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-8 sm:p-10 text-center">

                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-lg font-black shadow-lg shadow-indigo-600/25">
                        KD
                    </div>
                    <div class="leading-tight text-left">
                        <p class="font-bold text-gray-900">SIPDKS</p>
                        <p class="text-xs text-gray-500">Verifikasi Email</p>
                    </div>
                </div>

                <div class="mx-auto w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-black text-gray-900">Periksa Email Anda</h1>
                <p class="text-sm text-gray-500 mt-2 mb-6 leading-relaxed">
                    Terima kasih sudah mendaftar! Sebelum mulai, mohon verifikasi email Anda dengan mengklik tautan
                    yang telah kami kirimkan. Tidak menerima email? Kami akan dengan senang hati mengirimkan ulang.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                        Tautan verifikasi baru telah dikirim ke email Anda.
                    </div>
                @endif

                <div class="space-y-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-700/30 hover:-translate-y-0.5 active:translate-y-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                            </svg>
                            Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm font-semibold text-slate-600 transition hover:border-red-300 hover:text-red-700 hover:bg-red-50/50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
