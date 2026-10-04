<div x-show="activeTab === 'notifikasi'" x-cloak class="space-y-6" x-init="$watch('activeTab', v => { if (v === 'notifikasi') loadNotif(); })">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-gray-900">Notifikasi</h2>
                <p class="text-sm text-gray-500 mt-0.5">Semua notifikasi dan peringatan dari sistem pengawasan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="notifUnread > 0" class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600">
                    <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                    <span x-text="notifUnread + ' belum dibaca'"></span>
                </span>
                <button @click="loadNotif()" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Muat Ulang
                </button>
                <button x-show="notifUnread > 0" @click="markAllNotifRead()" class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Tandai Semua Dibaca
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Notifikasi</p>
                    <p class="text-xl font-bold text-gray-900" x-text="notifList.length">0</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100">
                    <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Belum Dibaca</p>
                    <p class="text-xl font-bold text-rose-600" x-text="notifUnread">0</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Sudah Dibaca</p>
                    <p class="text-xl font-bold text-emerald-600" x-text="notifList.length - notifUnread">0</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="font-bold text-gray-900">Riwayat Notifikasi</h3>
            <p class="text-xs text-gray-500 mt-0.5">Klik notifikasi untuk menandai sudah dibaca.</p>
        </div>

        <div class="divide-y divide-gray-100">
            {{-- Empty State --}}
            <template x-if="notifList.length === 0">
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Belum ada notifikasi</p>
                    <p class="mt-1 text-xs text-gray-500">Notifikasi akan muncul di sini ketika ada aktivitas sistem.</p>
                </div>
            </template>

            {{-- Notification Items --}}
            <template x-for="n in notifList" :key="n.id">
                <div
                    class="flex items-start gap-4 px-6 py-4 transition hover:bg-gray-50 cursor-pointer"
                    :class="!n.read ? 'bg-indigo-50/30' : ''"
                    @click="markNotifRead(n.id)"
                >
                    {{-- Icon --}}
                    <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-sm font-bold"
                         :class="getIcon(n.title).cls"
                         x-text="getIcon(n.title).icon">
                    </div>

                    {{-- Content --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold leading-tight" :class="n.read ? 'text-gray-500' : 'text-gray-900'" x-text="n.title"></p>
                            <span class="shrink-0 text-[10px] text-gray-400" x-text="timeAgo(n.created_at)"></span>
                        </div>
                        <p class="mt-1 text-xs leading-relaxed" :class="n.read ? 'text-gray-400' : 'text-gray-600'" x-text="n.message || ''"></p>

                        {{-- Extra Data --}}
                        <template x-if="n.extra && Object.keys(n.extra).length > 0">
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <template x-for="(val, key) in n.extra" :key="key">
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">
                                        <span x-text="key + ': ' + val"></span>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Read Status --}}
                    <div class="shrink-0">
                        <template x-if="!n.read">
                            <button @click.stop="markNotifRead(n.id)" class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-400 transition hover:border-indigo-300 hover:text-indigo-600" title="Tandai terbaca">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </template>
                        <template x-if="n.read">
                            <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-[10px] font-medium text-emerald-600">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Dibaca
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-xs text-indigo-800">
        <p class="font-bold mb-1">ℹ️ Tentang Notifikasi</p>
        <p class="opacity-90">
            Notifikasi dikirim secara otomatis oleh sistem ketika ada aktivitas penting seperti temuan audit baru, validasi akad, perubahan status produk, dan lainnya.
            Klik notifikasi untuk menandai sebagai sudah dibaca.
        </p>
    </div>
</div>
