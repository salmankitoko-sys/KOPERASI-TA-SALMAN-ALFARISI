{{-- ========== PANEL: NOTIFIKASI ========== --}}
<div
    x-show="activeTab === 'notifikasi'"
    x-cloak
    x-data="{
        notifications: {{ Js::from($inbox ?? collect()) }},
        unreadCount: {{ $jumlahNotif ?? 0 }},
        loading: false,

        init() {
            // Polling notifikasi setiap 30 detik
            this.startPolling();
        },

        startPolling() {
            setInterval(() => {
                this.fetchNotifications();
            }, 30000);
        },

        async fetchNotifications() {
            try {
                const res = await fetch('{{ route('anggota.marketplace.notifikasi.api') }}');
                const data = await res.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (e) {
                console.error('Gagal memuat notifikasi:', e);
            }
        },

        async markRead(id) {
            try {
                await fetch('/inbox/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const notif = this.notifications.find(n => n.id === id);
                if (notif) {
                    notif.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (e) {
                console.error('Gagal menandai terbaca:', e);
            }
        },

        async markAllRead() {
            try {
                await fetch('{{ route('inbox.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
            } catch (e) {
                console.error('Gagal menandai semua terbaca:', e);
            }
        },

        getIcon(title) {
            const t = title.toLowerCase();
            if (t.includes('disetujui') || t.includes('terverifikasi') || t.includes('selesai') || t.includes('berhasil')) return { icon: '✓', color: 'text-emerald-600 bg-emerald-50' };
            if (t.includes('ditolak')) return { icon: '✕', color: 'text-red-600 bg-red-50' };
            if (t.includes('baru') || t.includes('masuk') || t.includes('diajukan')) return { icon: '★', color: 'text-blue-600 bg-blue-50' };
            if (t.includes('reminder') || t.includes('jatuh tempo')) return { icon: '⏰', color: 'text-amber-600 bg-amber-50' };
            if (t.includes('terlambat')) return { icon: '⚠', color: 'text-red-600 bg-red-50' };
            if (t.includes('dikirim') || t.includes('transfer') || t.includes('cair')) return { icon: '↗', color: 'text-violet-600 bg-violet-50' };
            if (t.includes('populer') || t.includes('wishlist')) return { icon: '♥', color: 'text-pink-600 bg-pink-50' };
            return { icon: '●', color: 'text-indigo-600 bg-indigo-50' };
        },

        timeAgo(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },

        get recentUnread() {
            return this.notifications.filter(n => !n.is_read).slice(0, 5);
        },

        get allNotifications() {
            return this.notifications;
        }
    }"
    class="space-y-6"
>
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Notifikasi Saya</h3>
            <p class="text-sm text-gray-500 mt-0.5">Semua informasi dan pembaruan terkait aktivitas Anda di koperasi.</p>
        </div>
        <div class="flex items-center gap-3">
            @if(($jumlahNotif ?? 0) > 0)
                <button
                    @click="markAllRead()"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 hover:border-indigo-300"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tandai semua terbaca
                </button>
            @endif
            <button
                @click="fetchNotifications()"
                :disabled="loading"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
            >
                <svg class="h-4 w-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- Stats Ringkas --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Belum Dibaca</p>
                    <p class="text-2xl font-bold text-amber-700" x-text="unreadCount">0</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Total Notifikasi</p>
                    <p class="text-2xl font-bold text-blue-700" x-text="notifications.length">0</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Sudah Dibaca</p>
                    <p class="text-2xl font-bold text-emerald-700" x-text="notifications.filter(n => n.is_read).length">0</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Belum Dibaca --}}
    <template x-if="recentUnread.length > 0">
        <section class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
            <div class="border-b border-amber-100 bg-gradient-to-r from-amber-50/80 to-white px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100">
                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Belum Dibaca</h3>
                        <p class="mt-0.5 text-sm text-gray-500">Notifikasi baru yang perlu Anda perhatikan.</p>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-amber-50">
                <template x-for="notif in recentUnread" :key="notif.id">
                    <div class="flex items-start gap-4 px-6 py-4 transition hover:bg-amber-50/30" :class="{ 'bg-amber-50/20': !notif.is_read }">
                        <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-sm font-bold" :class="getIcon(notif.title).color">
                            <span x-text="getIcon(notif.title).icon"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900" x-text="notif.title"></p>
                                    <p class="mt-1 text-sm text-gray-600 leading-relaxed" x-text="notif.message"></p>
                                </div>
                                <button
                                    @click="markRead(notif.id)"
                                    class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700"
                                >
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Baca
                                </button>
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="timeAgo(notif.created_at)"></span>
                                </span>
                                @if(!empty($extra ?? null))
                                    <template x-if="notif.data && notif.data.tab">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-500" x-text="notif.data.tab"></span>
                                    </template>
                                @endif
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    </template>

    {{-- Semua Notifikasi --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50/80 to-white px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Semua Notifikasi</h3>
                    <p class="mt-0.5 text-sm text-gray-500">Riwayat lengkap notifikasi yang pernah Anda terima.</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            <template x-if="notifications.length === 0">
                <div class="flex flex-col items-center py-16">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                    </div>
                    <p class="mt-4 text-base font-semibold text-gray-500">Belum ada notifikasi</p>
                    <p class="mt-1 text-sm text-gray-400">Notifikasi akan muncul di sini saat ada aktivitas terkait akun Anda.</p>
                </div>
            </template>

            <template x-for="notif in notifications" :key="notif.id">
                <div
                    class="flex items-start gap-4 px-6 py-4 transition hover:bg-gray-50/50"
                    :class="{ 'bg-indigo-50/20 border-l-4 border-l-indigo-400': !notif.is_read }"
                >
                    <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-sm font-bold" :class="getIcon(notif.title).color">
                        <span x-text="getIcon(notif.title).icon"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold" :class="notif.is_read ? 'text-gray-600' : 'text-gray-900'" x-text="notif.title"></p>
                                <p class="mt-1 text-sm leading-relaxed" :class="notif.is_read ? 'text-gray-400' : 'text-gray-600'" x-text="notif.message"></p>
                            </div>
                            <template x-if="!notif.is_read">
                                <button
                                    @click="markRead(notif.id)"
                                    class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700"
                                >
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Tandai baca
                                </button>
                            </template>
                            <template x-if="notif.is_read">
                                <span class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-400">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Terbaca
                                </span>
                            </template>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 text-xs" :class="notif.is_read ? 'text-gray-300' : 'text-gray-400'">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="timeAgo(notif.created_at)"></span>
                            </span>
                            <template x-if="notif.data && notif.data.tab">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider" :class="notif.is_read ? 'bg-gray-50 text-gray-300' : 'bg-gray-100 text-gray-500'" x-text="notif.data.tab"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>
</div>
