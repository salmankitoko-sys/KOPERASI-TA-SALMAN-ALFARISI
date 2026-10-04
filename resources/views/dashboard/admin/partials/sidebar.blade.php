<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-indigo-950 text-indigo-100 shadow-xl shadow-indigo-950/20 transition-transform duration-200 ease-in-out"
>
    <div class="flex h-16 items-center gap-3 border-b border-indigo-800/70 bg-white/5 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500 text-sm font-bold text-white shadow-lg shadow-sky-900/30">
            KD
        </div>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-white">SIPDKS</p>
            <p class="text-xs text-indigo-300">Panel Admin</p>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4" aria-label="Navigasi admin">
        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-indigo-400">Utama</p>
            <a
                href="{{ route('admin.dashboard') }}"
                @click="sidebarOpen = false"
                @class([
                    'flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-indigo-600 text-white shadow-lg shadow-indigo-950/40' => request()->routeIs('admin.dashboard'),
                    'text-indigo-200 hover:bg-indigo-800/70 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                ])
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10" />
                    </svg>
                </span>
                Dashboard
            </a>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-indigo-400">Administrasi</p>
            <a
                href="{{ route('admin.users.index') }}"
                @click="sidebarOpen = false"
                @class([
                    'flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-indigo-600 text-white shadow-lg shadow-indigo-950/40' => request()->routeIs('admin.users.*'),
                    'text-indigo-200 hover:bg-indigo-800/70 hover:text-white' => ! request()->routeIs('admin.users.*'),
                ])
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m17 0v-2a4 4 0 00-3-3.87M9 11a4 4 0 100-8 4 4 0 000 8zm8 2a4 4 0 100-8" />
                    </svg>
                </span>
                Manajemen User
            </a>
            <a
                href="{{ route('admin.notifikasi.index') }}"
                @click="sidebarOpen = false"
                @class([
                    'flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-indigo-600 text-white shadow-lg shadow-indigo-950/40' => request()->routeIs('admin.notifikasi.*'),
                    'text-indigo-200 hover:bg-indigo-800/70 hover:text-white' => ! request()->routeIs('admin.notifikasi.*'),
                ])
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </span>
                Notifikasi
            </a>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-indigo-400">Akun</p>
            <a
                href="{{ route('profile.edit') }}"
                @click="sidebarOpen = false"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-indigo-200 transition hover:bg-indigo-800/70 hover:text-white"
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                Profil
            </a>
        </div>
    </nav>

    <div class="border-t border-indigo-800/70 p-3">
        <div class="flex items-center gap-3 rounded-lg bg-indigo-900/60 px-2 py-2">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-500 text-xs font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="truncate text-[11px] text-indigo-300">Administrator sistem</p>
            </div>
        </div>
    </div>
</aside>

