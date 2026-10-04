<x-app-layout>
    @php
        $rupiah = fn ($v) => 'Rp ' . number_format($v ?? 0, 0, ',', '.');
    @endphp

    <div class="min-h-screen w-full" x-data="{
        sidebarOpen: false,
        activeTab: 'dashboard',
        notifOpen: false,
        profileOpen: false,
        notifList: [],
        notifUnread: 0,
        async loadNotif() {
            try {
                const res = await fetch('{{ route("dps.notifikasi.index") }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.notifList = data.data || [];
                this.notifUnread = data.unread || 0;
            } catch (e) { console.error('Gagal memuat notifikasi:', e); }
        },
        async markNotifRead(id) {
            try {
                await fetch('{{ route("dps.notifikasi.read", ":id") }}'.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name\'csrf-token\']')?.getAttribute('content') ?? '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const n = this.notifList.find(x => x.id === id);
                if (n && !n.read) {
                    n.read = true;
                    this.notifUnread = Math.max(0, this.notifUnread - 1);
                }
            } catch (e) { console.error(e); }
        },
        async markAllNotifRead() {
            try {
                await fetch('{{ route("dps.notifikasi.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name\'csrf-token\']')?.getAttribute('content') ?? '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                this.notifList.forEach(n => n.read = true);
                this.notifUnread = 0;
            } catch (e) { console.error(e); }
        },
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
    }" x-init="loadNotif()">

        {{-- Sidebar --}}
        @include('dashboard.dps.partials.sidebar')

        {{-- Main Content --}}
        <div class="lg:ml-64 min-h-screen bg-gray-50">

            {{-- Navbar --}}
            @include('dashboard.dps.partials.navbar')

            {{-- Page Content --}}
            <div class="p-4 lg:p-6 space-y-6 w-full">

                {{-- Dashboard Overview (default) --}}
                @include('dashboard.dps.partials.dashboard')

                {{-- Sub Menu Panels (tab-based) --}}
                @include('dashboard.dps.partials.audit')
                @include('dashboard.dps.partials.laporan')
                @include('dashboard.dps.partials.notifikasi')

            </div>
        </div>

        {{-- Overlay Mobile --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-30 lg:hidden"
        ></div>

    </div>
</x-app-layout>

