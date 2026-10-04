<section x-show="activeTab === 'pengaturan'" x-cloak>
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Pengaturan Akun</h3>
        <p class="text-sm text-gray-500">Tinjau informasi akun dan kelola keamanan profil pengurus.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-lg font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ auth()->user()->name }}</h4>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                    Kelola profil
                </a>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 border-t border-gray-100 pt-6 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Peran</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">Pengurus koperasi</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Nomor HP</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ auth()->user()->no_hp ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Bergabung</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ auth()->user()->created_at->locale('id')->translatedFormat('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Verifikasi email</dt>
                    <dd class="mt-1 text-sm font-medium {{ auth()->user()->email_verified_at ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ auth()->user()->email_verified_at ? 'Terverifikasi' : 'Belum terverifikasi' }}
                    </dd>
                </div>
            </dl>
        </div>

        <aside class="rounded-lg border border-amber-200 bg-amber-50 p-6">
            <h4 class="font-semibold text-amber-900">Keamanan akun</h4>
            <p class="mt-2 text-sm leading-6 text-amber-800">Perubahan nama, email, nomor HP, dan password dilakukan melalui halaman profil agar setiap perubahan tervalidasi dan tersimpan dengan benar.</p>
            <a href="{{ route('profile.edit') }}" class="mt-5 inline-flex text-sm font-medium text-amber-900 underline underline-offset-4">Buka halaman profil</a>
        </aside>
    </div>
</section>
