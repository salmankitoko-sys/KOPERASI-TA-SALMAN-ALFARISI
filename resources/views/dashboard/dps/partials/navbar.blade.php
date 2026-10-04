@php
    $headerInitials = collect(explode(' ', auth()->user()->name ?? 'DPS'))
        ->filter()
        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<header class="sticky top-0 z-20 border-b border-indigo-100 bg-white/90 backdrop-blur-md">
    <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 lg:px-6">
        {{-- Left: Hamburger + Title --}}
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                @click="sidebarOpen = !sidebarOpen"
                title="Buka menu navigasi"
                aria-label="Buka menu navigasi"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 lg:hidden"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="min-w-0">
                <h1 class="truncate text-lg font-semibold text-indigo-700">Dashboard DPS Syariah</h1>
                <p class="hidden truncate text-sm text-gray-500 sm:block">Pengawasan & Kepatuhan Syariah</p>
            </div>
        </div>

        {{-- Right: Notification + Profile --}}
        <div class="flex shrink-0 items-center gap-3">
            {{-- Date (desktop only) --}}
            <div class="hidden text-right lg:block">
                <p class="text-xs text-gray-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <p class="text-xs font-semibold text-gray-700">Tahun Buku {{ date('Y') }}</p>
            </div>

            <div class="hidden h-7 w-px bg-gray-200 sm:block"></div>

            {{-- Notification Bell --}}
            <div class="relative" x-data="{
                open: false,
                get notifCount() { return notifUnread; },
                getIcon(title) {
                    const t = (title || '').toLowerCase();
                    if (t.includes('disetujui') || t.includes('terverifikasi') || t.includes('selesai')) return { icon: '✓', cls: 'bg-emerald-100 text-emerald-600' };
                    if (t.includes('ditolak')) return { icon: '✕', cls: 'bg-red-100 text-red-600' };
                    if (t.includes('baru') || t.includes('masuk') || t.includes('diajukan')) return { icon: '★', cls: 'bg-blue-100 text-blue-600' };
                    if (t.includes('reminder') || t.includes('jatuh tempo')) return { icon: '⏰', cls: 'bg-amber-100 text-amber-600' };
                    if (t.includes('terlambat')) return { icon: '⚠', cls: 'bg-red-100 text-red-600' };
                    if (t.includes('dikirim') || t.includes('transfer') || t.includes('cair')) return { icon: '↗', cls: 'bg-violet-100 text-violet-600' };
                    if (t.includes('temuan') || t.includes('audit')) return { icon: '🔍', cls: 'bg-orange-100 text-orange-600' };
                    if (t.includes('laporan')) return { icon: '📊', cls: 'bg-indigo-100 text-indigo-600' };
                    return { icon: '●', cls: 'bg-indigo-100 text-indigo-600' };
                },
                timeAgo(dateStr) {
                    const d = new Date(dateStr), now = new Date(), diff = Math.floor((now - d) / 1000);
                    if (diff < 60) return 'Baru saja';
                    if (diff < 3600) return Math.floor(diff / 60) + 'm lalu';
                    if (diff < 86400) return Math.floor(diff / 3600) + 'j lalu';
                    if (diff < 604800) return Math.floor(diff / 86400) + 'h lalu';
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                }
            }">
                <button
                    type="button"
                    @click="open = !open"
                    title="Notifikasi"
                    aria-label="Notifikasi"
                    class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <template x-if="notifCount > 0">
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white animate-pulse" x-text="notifCount > 9 ? '9+' : notifCount"></span>
                    </template>
                </button>

                {{-- Notification Dropdown --}}
                <div
                    x-cloak
                    x-show="open"
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                    class="absolute right-0 z-50 mt-2 w-96 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl"
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Notifikasi</p>
                            <p class="text-xs text-gray-500" x-text="notifCount + ' belum dibaca'"></p>
                        </div>
                        <button @click="loadNotif()" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" title="Muat ulang">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>

                    {{-- Mark All Read --}}
                    <div x-show="notifUnread > 0" class="border-b border-gray-50 bg-gray-50/50 px-4 py-2">
                        <button @click="markAllNotifRead()" class="text-[11px] font-semibold text-indigo-600 transition hover:text-indigo-800">
                            ✓ Tandai semua dibaca
                        </button>
                    </div>

                    {{-- Notification List --}}
                    <div class="max-h-80 divide-y divide-gray-50 overflow-y-auto">
                        <template x-if="notifList.length === 0">
                            <div class="px-4 py-8 text-center">
                                <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                </div>
                                <p class="text-sm text-gray-500">Belum ada notifikasi</p>
                                <p class="mt-1 text-xs text-gray-400">Notifikasi akan muncul di sini</p>
                            </div>
                        </template>
                        <template x-for="n in notifList" :key="n.id">
                            <div
                                class="flex items-start gap-3 px-4 py-3 transition hover:bg-gray-50 cursor-pointer"
                                :class="!n.read ? 'bg-indigo-50/30' : ''"
                                @click="markNotifRead(n.id)"
                            >
                                <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-xs font-bold" :class="getIcon(n.title).cls" x-text="getIcon(n.title).icon"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold leading-tight" :class="n.read ? 'text-gray-500' : 'text-gray-900'" x-text="n.title"></p>
                                    <p class="mt-0.5 text-xs leading-snug" :class="n.read ? 'text-gray-400' : 'text-gray-600'" x-text="n.message ? (n.message.length > 80 ? n.message.substring(0, 80) + '...' : n.message) : ''"></p>
                                    <span class="mt-1 inline-block text-[10px] text-gray-400" x-text="timeAgo(n.created_at)"></span>
                                </div>
                                <template x-if="!n.read">
                                    <button @click.stop="markNotifRead(n.id)" class="shrink-0 rounded-md border border-gray-200 bg-white p-1 text-gray-400 transition hover:border-indigo-300 hover:text-indigo-600" title="Tandai terbaca">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Footer --}}
                    <button
                        @click="open = false; window.scrollTo({top: 0, behavior: 'smooth'});"
                        class="block w-full border-t border-gray-100 px-4 py-3 text-center text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-800"
                    >
                        Lihat semua notifikasi →
                    </button>
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2 rounded-lg py-1 pl-1 pr-2 transition hover:bg-gray-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600 text-xs font-semibold text-white shadow-sm">
                            {{ $headerInitials ?: 'D' }}
                        </span>
                        <span class="hidden max-w-40 text-left sm:block">
                            <span class="block truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'DPS' }}</span>
                            <span class="block truncate text-xs text-gray-500">Dewan Pengawas Syariah</span>
                        </span>
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'DPS' }}</p>
                        <p class="truncate text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                    </div>

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
        </div>
    </div>
</header>
