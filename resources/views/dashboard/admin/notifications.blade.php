<x-admin-layout title="Notifikasi Admin">
    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Header --}}
        <section class="overflow-hidden rounded-lg border border-indigo-900 bg-indigo-950 px-5 py-6 text-white shadow-lg shadow-indigo-950/15 lg:px-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-300">Pusat Notifikasi</p>
                    <h2 class="mt-3 text-2xl font-semibold">Notifikasi & Peringatan Sistem</h2>
                    <p class="mt-2 max-w-xl text-sm text-indigo-100">
                        Semua notifikasi dari DPS, pengurus, dan aktivitas sistem akan muncul di sini.
                    </p>
                </div>
                <div class="flex gap-3">
                    @if($unreadCount > 0 || $unreadInbox > 0)
                        <form method="POST" action="{{ route('admin.notifikasi.read-all') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-indigo-300/50 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        {{-- Quick Stats --}}
        <section class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-medium text-amber-700">Menunggu Validasi DPS</p>
                <p class="mt-1 text-2xl font-semibold text-amber-900">{{ $pendingDps }}</p>
            </div>
            <div class="rounded-lg border border-violet-200 bg-violet-50 p-4">
                <p class="text-xs font-medium text-violet-700">Toko Pending</p>
                <p class="mt-1 text-2xl font-semibold text-violet-900">{{ $pendingToko }}</p>
            </div>
            <div class="rounded-lg border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs font-medium text-sky-700">Produk Pending</p>
                <p class="mt-1 text-2xl font-semibold text-sky-900">{{ $pendingProduk }}</p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-medium text-emerald-700">User Baru (7 hari)</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-900">{{ $newUsers }}</p>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_350px]">
            {{-- Database Notifications --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Notifikasi Sistem</h3>
                    <p class="mt-1 text-sm text-gray-500">Notifikasi dari aktivitas DPS, pengurus, dan sistem.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($notifications as $notification)
                        @php $data = $notification->data ?? []; @endphp
                        <div class="flex items-start gap-3 px-5 py-4 {{ is_null($notification->read_at) ? 'bg-indigo-50/50' : '' }}">
                            <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ is_null($notification->read_at) ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $data['title'] ?? 'Notifikasi' }}</p>
                                <p class="mt-0.5 text-sm text-gray-600">{{ $data['message'] ?? '' }}</p>
                                <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(is_null($notification->read_at))
                                <form method="POST" action="{{ route('admin.notifikasi.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                            <p class="mt-3 text-sm">Belum ada notifikasi.</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="border-t border-gray-200 px-5 py-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>

            {{-- Inbox Entries --}}
            <aside class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Inbox</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $unreadInbox }} belum dibaca</p>
                </div>

                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                    @forelse ($inboxEntries as $entry)
                        <div class="px-5 py-3 {{ !$entry->is_read ? 'bg-indigo-50/50' : '' }}">
                            <p class="text-sm font-medium text-gray-900">{{ $entry->title }}</p>
                            <p class="mt-0.5 text-xs text-gray-600 line-clamp-2">{{ $entry->message }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $entry->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-500">Inbox kosong.</div>
                    @endforelse
                </div>

                @if($inboxEntries->hasPages())
                    <div class="border-t border-gray-200 px-5 py-3">
                        {{ $inboxEntries->links() }}
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-admin-layout>
