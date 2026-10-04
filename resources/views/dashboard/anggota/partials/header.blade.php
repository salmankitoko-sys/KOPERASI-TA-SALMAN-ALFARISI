<header class="flex items-center justify-between bg-white border border-gray-100 rounded-2xl px-5 py-4 shadow-sm mb-6"
    x-data="{ open: false }">

    {{-- Kiri: Sapaan dinamis --}}
    <div>
        <p class="text-[15px] font-semibold text-gray-900">
            Selamat {{ now()->hour < 11 ? 'pagi' : (now()->hour < 15 ? 'siang' : (now()->hour < 18 ? 'sore' : 'malam')) }},
            {{ explode(' ', Auth::user()->name ?? 'User')[0] }} 👋
        </p>
        <p class="text-xs text-gray-400 mt-0.5">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Kanan: Avatar + Dropdown --}}
    <div class="flex items-center gap-4">
        {{-- Avatar + Nama + Dropdown --}}
        <div class="relative">
            <button type="button" @click="open = !open" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center flex-shrink-0 shadow-sm ring-2 ring-white">
                    <span class="text-white text-xs font-bold">
                        {{ collect(explode(' ', Auth::user()->name ?? 'User'))
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)
                            ->implode('') }}
                    </span>
                </div>
                <div class="text-left leading-tight hidden sm:block">
                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-400 transition-transform group-hover:text-gray-600" :class="{ 'rotate-180': open }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown menu --}}
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute right-0 mt-3 w-52 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50"
                style="display: none;">
                <div class="px-4 py-2 border-b border-gray-50 sm:hidden">
                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                </div>
                <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}"
                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Pengaturan Profil
                </a>
                <div class="border-t border-gray-50 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>