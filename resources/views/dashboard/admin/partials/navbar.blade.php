<header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-indigo-100 bg-white/90 px-4 backdrop-blur lg:px-6">
    <div class="flex min-w-0 items-center gap-3">
        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            title="Buka menu navigasi"
            aria-label="Buka menu navigasi"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 lg:hidden"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="min-w-0">
            <h1 class="truncate text-base font-semibold text-gray-900">{{ $title }}</h1>
            <p class="hidden text-xs text-gray-500 sm:block">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center gap-2 rounded-lg py-1 pl-1 pr-2 transition hover:bg-gray-100">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="hidden max-w-32 truncate text-sm font-medium text-gray-700 sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="truncate text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
            </div>

            <a href="{{ route('profile.edit') }}" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 transition hover:bg-gray-100">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50">
                    <svg class="h-4 w-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar
                </button>
            </form>
        </x-slot>
    </x-dropdown>
</header>
