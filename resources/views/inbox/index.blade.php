<x-dynamic-component :component="auth()->user()->isBendahara() ? 'bendahara-layout' : 'app-layout'">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">Inbox</h2>
                <p class="text-sm text-gray-500 mt-0.5">Notifikasi dan pembaruan terbaru untuk Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <button id="soundToggle" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition" title="Suara notifikasi">
                    🔔
                </button>
                <form method="POST" action="{{ route('inbox.read-all') }}" class="hidden sm:block">
                    @csrf
                    <button class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition">
                        Tandai Semua Terbaca
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- New Notification Alert --}}
            <div id="newNotifAlert" class="hidden mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center justify-between cursor-pointer hover:bg-emerald-100 transition">
                <span>🔔 Ada notifikasi baru! Klik untuk melihat.</span>
                <span class="text-emerald-600">↻</span>
            </div>

            {{-- Filter Tabs --}}
            <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
                <button onclick="filterNotif('all')" class="filter-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition bg-indigo-600 text-white" data-filter="all">
                    Semua
                    <span id="countAll" class="ml-1 px-1.5 py-0.5 rounded-full bg-white/20 text-[10px]">{{ $entries->total() }}</span>
                </button>
                <button onclick="filterNotif('unread')" class="filter-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition bg-gray-100 text-gray-700" data-filter="unread">
                    Belum Dibaca
                    <span id="countUnread" class="ml-1 px-1.5 py-0.5 rounded-full bg-white/20 text-[10px]">{{ $entries->where('is_read', 0)->count() }}</span>
                </button>
                <button onclick="filterNotif('marketplace')" class="filter-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition bg-gray-100 text-gray-700" data-filter="marketplace">
                    🛒 Marketplace
                </button>
                <button onclick="filterNotif('order')" class="filter-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition bg-gray-100 text-gray-700" data-filter="order">
                    📦 Pesanan
                </button>
            </div>

            {{-- Notification List --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div id="notifList" class="divide-y divide-gray-100">
                    @forelse($entries as $entry)
                        @php
                            $data = is_array($entry->data) ? $entry->data : (json_decode($entry->data, true) ?? []);
                            $tab = $data['tab'] ?? 'general';
                            $icon = '🔔';
                            if(str_contains($entry->title, 'Pesanan')) $icon = '📦';
                            elseif(str_contains($entry->title, 'Pembayaran') || str_contains($entry->title, 'Bukti')) $icon = '💰';
                            elseif(str_contains($entry->title, 'Toko')) $icon = '🏪';
                            elseif(str_contains($entry->title, 'Produk')) $icon = '🏷️';
                            elseif(str_contains($entry->title, 'Angsuran')) $icon = '📋';
                            elseif(str_contains($entry->title, 'Simpanan')) $icon = '🏦';
                            $timeAgo = $entry->created_at->diffForHumans();
                        @endphp
                        <div class="notif-item p-4 hover:bg-gray-50 transition cursor-pointer {{ $entry->is_read ? 'opacity-60' : '' }}"
                             data-id="{{ $entry->id }}"
                             data-tab="{{ $tab }}"
                             data-read="{{ $entry->is_read ? '1' : '0' }}"
                             onclick="markAsRead({{ $entry->id }}, this)">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $entry->is_read ? 'bg-gray-100' : 'bg-indigo-100' }} flex items-center justify-center text-lg">
                                    {{ $icon }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-sm {{ $entry->is_read ? 'text-gray-600' : 'text-gray-900' }} truncate">
                                            {{ $entry->title }}
                                        </p>
                                        @if(!$entry->is_read)
                                            <span class="flex-shrink-0 w-2 h-2 rounded-full bg-indigo-500"></span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-0.5" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $entry->message }}</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-xs text-gray-400">{{ $timeAgo }}</span>
                                        @if($tab !== 'general')
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold uppercase">{{ $tab }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if(!$entry->is_read)
                                        <button onclick="event.stopPropagation(); markAsRead({{ $entry->id }}, this.closest('.notif-item'))"
                                                class="p-1 rounded-lg hover:bg-gray-200 transition" title="Tandai terbaca">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="text-sm font-semibold text-gray-700">Belum ada notifikasi</p>
                            <p class="text-xs text-gray-500 mt-1">Notifikasi akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>

                <div id="emptyFilter" class="hidden py-16 text-center">
                    <div class="text-4xl mb-3">🔍</div>
                    <p class="text-sm font-semibold text-gray-700">Tidak ada notifikasi</p>
                    <p class="text-xs text-gray-500 mt-1">Tidak ada notifikasi yang cocok dengan filter ini.</p>
                </div>

                <div id="pagination" class="px-4 py-3 border-t border-gray-100">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        let soundEnabled = localStorage.getItem('notifSoundEnabled') !== 'false';
        let lastCheck = new Date().toISOString();
        let pollInterval = null;

        const soundBtn = document.getElementById('soundToggle');
        soundBtn.textContent = soundEnabled ? '🔔' : '🔕';
        soundBtn.onclick = () => {
            soundEnabled = !soundEnabled;
            localStorage.setItem('notifSoundEnabled', soundEnabled);
            soundBtn.textContent = soundEnabled ? '🔔' : '🔕';
        };

        function filterNotif(filter) {
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('bg-indigo-600', 'text-white');
                tab.classList.add('bg-gray-100', 'text-gray-700');
            });
            const activeTab = document.querySelector(`[data-filter="${filter}"]`);
            if (activeTab) {
                activeTab.classList.add('bg-indigo-600', 'text-white');
                activeTab.classList.remove('bg-gray-100', 'text-gray-700');
            }

            const items = document.querySelectorAll('.notif-item');
            let visibleCount = 0;
            items.forEach(item => {
                const tab = item.dataset.tab;
                const isRead = item.dataset.read === '1';
                let show = false;
                if (filter === 'all') show = true;
                else if (filter === 'unread') show = !isRead;
                else if (filter === 'marketplace') show = tab === 'marketplace';
                else if (filter === 'order') show = tab === 'pesanan';
                item.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            document.getElementById('emptyFilter').classList.toggle('hidden', visibleCount > 0);
        }

        async function markAsRead(id, element) {
            if (element.dataset.read === '1') return;
            try {
                const resp = await fetch('/inbox/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                if (resp.ok) {
                    element.dataset.read = '1';
                    element.classList.add('opacity-60');
                    const dot = element.querySelector('.bg-indigo-500');
                    if (dot) dot.remove();
                    const checkBtn = element.querySelector('button[title="Tandai terbaca"]');
                    if (checkBtn) checkBtn.remove();
                    const countEl = document.getElementById('countUnread');
                    if (countEl) {
                        let count = parseInt(countEl.textContent) - 1;
                        countEl.textContent = count < 0 ? 0 : count;
                    }
                }
            } catch (e) {
                console.error('Gagal menandai terbaca:', e);
            }
        }

        async function checkNewNotifications() {
            try {
                const resp = await fetch('{{ route("anggota.marketplace.notifikasi.api") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();
                const newNotifs = data.notifications.filter(n => new Date(n.created_at) > new Date(lastCheck));
                if (newNotifs.length > 0) {
                    const alert = document.getElementById('newNotifAlert');
                    alert.classList.remove('hidden');
                    alert.querySelector('span').textContent = '🔔 Ada ' + newNotifs.length + ' notifikasi baru! Klik untuk melihat.';
                    alert.onclick = () => location.reload();
                    document.getElementById('countUnread').textContent = data.unread_count;
                }
                lastCheck = new Date().toISOString();
            } catch (e) {}
        }

        pollInterval = setInterval(checkNewNotifications, 15000);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(pollInterval);
            } else {
                checkNewNotifications();
                pollInterval = setInterval(checkNewNotifications, 15000);
            }
        });

        const style = document.createElement('style');
        style.textContent = '.notif-item{animation:slideIn .3s ease-out}@keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}';
        document.head.appendChild(style);
    </script>
</x-dynamic-component>
