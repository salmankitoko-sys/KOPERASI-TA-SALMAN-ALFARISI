@php
    $headerNotifications = $inbox ?? collect();
    $headerNotificationCount = $jumlahNotif ?? 0;
    $headerInitials = collect(explode(' ', auth()->user()->name ?? 'Anggota'))
        ->filter()
        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
    <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 lg:px-6">
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
                <h1 class="truncate text-lg font-semibold text-indigo-700">Selamat datang, {{ auth()->user()->name ?? 'Anggota' }}</h1>
                <p class="truncate text-sm text-gray-500">Ringkasan aktivitas koperasi Anda hari ini.</p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-3">
            <div class="hidden text-right lg:block">
                <p class="text-xs text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                <p class="text-xs font-semibold text-gray-700">Tahun Buku {{ date('Y') }}</p>
            </div>

            <div class="hidden h-7 w-px bg-gray-200 sm:block"></div>

            <div class="relative" x-data="{
                open: false,
                unreadCount: {{ $headerNotificationCount }},
                recentNotifs: {{ Js::from($headerNotifications->take(5)) }},

                async fetchLatest() {
                    try {
                        const res = await fetch('{{ route('anggota.marketplace.notifikasi.api') }}');
                        const data = await res.json();
                        this.unreadCount = data.unread_count;
                        this.recentNotifs = data.notifications.slice(0, 5);
                    } catch (e) {}
                },

                async markRead(id) {
                    try {
                        await fetch('/inbox/' + id + '/read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const n = this.recentNotifs.find(x => x.id === id);
                        if (n) n.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    } catch (e) {}
                },

                getIcon(title) {
                    const t = (title || '').toLowerCase();
                    if (t.includes('disetujui') || t.includes('terverifikasi') || t.includes('selesai')) return { icon: '✓', cls: 'bg-emerald-100 text-emerald-600' };
                    if (t.includes('ditolak')) return { icon: '✕', cls: 'bg-red-100 text-red-600' };
                    if (t.includes('baru') || t.includes('masuk') || t.includes('diajukan')) return { icon: '★', cls: 'bg-blue-100 text-blue-600' };
                    if (t.includes('reminder') || t.includes('jatuh tempo')) return { icon: '⏰', cls: 'bg-amber-100 text-amber-600' };
                    if (t.includes('terlambat')) return { icon: '⚠', cls: 'bg-red-100 text-red-600' };
                    if (t.includes('dikirim') || t.includes('transfer') || t.includes('cair')) return { icon: '↗', cls: 'bg-violet-100 text-violet-600' };
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
            }" x-init="setInterval(() => fetchLatest(), 30000)">
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
                    <template x-if="unreadCount > 0">
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white animate-pulse" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </template>
                </button>

                <div
                    x-cloak
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="absolute right-0 z-50 mt-2 w-96 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl"
                >
                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Notifikasi</p>
                            <p class="text-xs text-gray-500" x-text="unreadCount + ' belum dibaca'"></p>
                        </div>
                        <button @click="fetchLatest()" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>

                    <div class="max-h-80 divide-y divide-gray-50 overflow-y-auto">
                        <template x-if="recentNotifs.length === 0">
                            <p class="px-4 py-8 text-center text-sm text-gray-500">Belum ada notifikasi</p>
                        </template>
                        <template x-for="n in recentNotifs" :key="n.id">
                            <div class="flex items-start gap-3 px-4 py-3 transition hover:bg-gray-50" :class="!n.is_read ? 'bg-indigo-50/30' : ''">
                                <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-xs font-bold" :class="getIcon(n.title).cls" x-text="getIcon(n.title).icon"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold leading-tight" :class="n.is_read ? 'text-gray-500' : 'text-gray-900'" x-text="n.title"></p>
                                    <p class="mt-0.5 text-xs leading-snug" :class="n.is_read ? 'text-gray-400' : 'text-gray-600'" x-text="n.message ? (n.message.length > 80 ? n.message.substring(0, 80) + '...' : n.message) : ''"></p>
                                    <span class="mt-1 inline-block text-[10px] text-gray-400" x-text="timeAgo(n.created_at)"></span>
                                </div>
                                <template x-if="!n.is_read">
                                    <button @click.stop="markRead(n.id)" class="shrink-0 rounded-md border border-gray-200 bg-white p-1 text-gray-400 transition hover:border-indigo-300 hover:text-indigo-600" title="Tandai terbaca">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <button
                        @click="open = false; activeTab = 'notifikasi'; window.scrollTo({top: 0, behavior: 'smooth'});"
                        class="block w-full border-t border-gray-100 px-4 py-3 text-center text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-800"
                    >
                        Lihat semua notifikasi →
                    </button>
                </div>
            </div>

            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2 rounded-lg py-1 pl-1 pr-2 transition hover:bg-gray-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">
                            {{ $headerInitials ?: 'A' }}
                        </span>
                        <span class="hidden max-w-40 text-left sm:block">
                            <span class="block truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Anggota' }}</span>
                            <span class="block truncate text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</span>
                        </span>
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Anggota' }}</p>
                        <p class="truncate text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-100">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50">Keluar</button>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
