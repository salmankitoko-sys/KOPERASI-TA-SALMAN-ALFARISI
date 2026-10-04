<aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-40 flex w-72 transform flex-col bg-gradient-to-b from-red-950 via-rose-950 to-stone-950 text-rose-100 shadow-2xl transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-20 items-center gap-3 border-b border-rose-800/50 px-5">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-amber-300/40 bg-amber-400/15 text-sm font-bold text-amber-200">KK</div>
        <div><p class="font-semibold tracking-wide text-white">SIPDKS</p><p class="text-xs text-amber-200/80">Ruang Ketua</p></div>
    </div>
    @php
        $groups = [
            'Kepemimpinan' => [
                ['tab' => 'ringkasan', 'label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h14V10'],
                ['tab' => 'kinerja', 'label' => 'Kinerja Koperasi', 'icon' => 'M4 19V9m5 10V5m5 14v-7m5 7V3'],
                ['tab' => 'anggota', 'label' => 'Perkembangan Anggota', 'icon' => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm9 0v6m3-3h-6'],
            ],
            'Pengawasan' => [
                ['tab' => 'keuangan', 'label' => 'Monitoring Keuangan', 'icon' => 'M3 8h18M5 8V6h14v2M7 8v10m10-10v10M3 18h18'],
                ['tab' => 'laporan', 'label' => 'Laporan', 'icon' => 'M7 3h8l4 4v14H7zM15 3v5h5M10 13h6m-6 4h6'],
                ['tab' => 'notifikasi', 'label' => 'Notifikasi', 'icon' => 'M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4'],
            ],
        ];
    @endphp
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        @foreach ($groups as $group => $items)
            <div><p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-amber-300/60">{{ $group }}</p><div class="space-y-1">
                @foreach ($items as $item)
                    <button type="button" @click="setActiveTab('{{ $item['tab'] }}')" :class="activeTab === '{{ $item['tab'] }}' ? 'bg-amber-400 text-red-950 shadow-lg shadow-black/20' : 'text-rose-100 hover:bg-white/10 hover:text-white'" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-current/10"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg></span>
                        <span>{{ $item['label'] }}</span>
                    </button>
                @endforeach
            </div></div>
        @endforeach
    </nav>
    <div class="border-t border-rose-800/50 p-4"><div class="flex items-center gap-3 rounded-xl bg-black/15 p-3"><div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400 font-bold text-red-950">{{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}</div><div class="min-w-0"><p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name ?? 'Ketua' }}</p><p class="text-xs text-rose-300">Ketua Koperasi</p></div></div></div>
</aside>
