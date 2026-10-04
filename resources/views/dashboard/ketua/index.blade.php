<x-app-layout>
    <div class="min-h-screen bg-stone-50" x-data="{
        sidebarOpen: false,
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'ringkasan',
        tabs: { ringkasan: 'Dashboard', kinerja: 'Kinerja Koperasi', anggota: 'Perkembangan Anggota', keuangan: 'Monitoring Keuangan', laporan: 'Laporan', notifikasi: 'Notifikasi' },
        setActiveTab(tab) {
            this.activeTab = tab;
            this.sidebarOpen = false;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }
    }">
        @include('dashboard.ketua.partials.sidebar')
        <div class="min-h-screen lg:ml-72">
            @include('dashboard.ketua.partials.navbar')
            <main class="mx-auto max-w-7xl space-y-6 p-4 lg:p-6">
            <section x-show="activeTab === 'ringkasan'" x-cloak class="rounded-2xl bg-gradient-to-br from-rose-950 via-red-950 to-stone-950 p-8 text-white shadow-xl">
                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">Akses baca-saja</span>
                <h2 class="mt-4 text-3xl font-bold">Pantau pelaksanaan program dan usaha koperasi</h2>
                <p class="mt-3 max-w-2xl text-rose-100">Ketua menerima laporan, mengevaluasi perkembangan usaha, dan memberikan arahan tanpa mengubah transaksi operasional.</p>
            </section>
            @php
                $cards = [
                    ['Anggota aktif', number_format($stats['anggota_aktif']), 'dari '.number_format($stats['anggota']).' anggota', 'border-indigo-200 bg-indigo-50'],
                    ['Simpanan masuk', 'Rp '.number_format($stats['total_simpanan'], 0, ',', '.'), 'total terverifikasi', 'border-emerald-200 bg-emerald-50'],
                    ['Pembiayaan aktif', 'Rp '.number_format($stats['pembiayaan_aktif'], 0, ',', '.'), $stats['pengajuan_menunggu'].' menunggu review', 'border-amber-200 bg-amber-50'],
                    ['Omzet bulan ini', 'Rp '.number_format($stats['omzet_bulan_ini'], 0, ',', '.'), 'pesanan selesai', 'border-sky-200 bg-sky-50'],
                ];
            @endphp
            <section x-show="activeTab === 'ringkasan' || activeTab === 'kinerja'" x-cloak class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">@foreach ($cards as [$label, $value, $note, $class])
                <article class="rounded-xl border p-5 {{ $class }}"><p class="text-sm text-slate-600">{{ $label }}</p><p class="mt-3 text-2xl font-bold text-slate-950">{{ $value }}</p><p class="mt-2 text-xs text-slate-500">{{ $note }}</p></article>
            @endforeach</section>
            <section x-show="activeTab === 'ringkasan'" x-cloak class="space-y-4">
                <div><h3 class="text-lg font-bold text-stone-900">Statistik Pembiayaan</h3><p class="text-sm text-stone-500">Ringkasan status portofolio untuk pemantauan Ketua tanpa akses perubahan data.</p></div>
                @php
                    $statusChart = [
                        ['label' => 'Menunggu', 'value' => $stats['pengajuan_menunggu'], 'color' => '#f59e0b'],
                        ['label' => 'Aktif/berjalan', 'value' => $pembiayaan['aktif'], 'color' => '#4f46e5'],
                        ['label' => 'Lunas', 'value' => $pembiayaan['lunas'], 'color' => '#059669'],
                        ['label' => 'Ditolak', 'value' => $pembiayaan['ditolak'], 'color' => '#e11d48'],
                    ];
                    $statusTotal = (int) collect($statusChart)->sum('value');
                    $statusOffset = 0;
                    $statusGradient = $statusTotal > 0 ? collect($statusChart)->map(function ($item) use (&$statusOffset, $statusTotal) {
                        $start = $statusOffset;
                        $statusOffset += ($item['value'] / $statusTotal) * 360;
                        return $item['color'].' '.$start.'deg '.$statusOffset.'deg';
                    })->implode(', ') : '#e7e5e4 0deg 360deg';
                    $akadColors = ['#991b1b', '#c2410c', '#ca8a04', '#047857', '#0369a1', '#6d28d9'];
                    $akadChart = $pembiayaan['akad']->map(fn ($jumlah, $akad, $index = 0) => ['label' => str($akad)->replace('_', ' ')->title(), 'value' => (int) $jumlah])->values();
                    $akadChart = $akadChart->map(fn ($item, $index) => array_merge($item, ['color' => $akadColors[$index % count($akadColors)]]));
                    $akadTotal = (int) $akadChart->sum('value');
                    $akadOffset = 0;
                    $akadGradient = $akadTotal > 0 ? $akadChart->map(function ($item) use (&$akadOffset, $akadTotal) {
                        $start = $akadOffset;
                        $akadOffset += ($item['value'] / $akadTotal) * 360;
                        return $item['color'].' '.$start.'deg '.$akadOffset.'deg';
                    })->implode(', ') : '#e7e5e4 0deg 360deg';
                @endphp
                <div class="grid gap-5 lg:grid-cols-2">
                    <article class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                        <div><h4 class="font-semibold text-stone-900">Status Pembiayaan</h4><p class="text-xs text-stone-500">Proporsi jumlah berdasarkan status</p></div>
                        <div class="mt-6 grid items-center gap-6 sm:grid-cols-[190px_1fr]"><div class="relative mx-auto h-44 w-44 rounded-full" style="background: conic-gradient({{ $statusGradient }})"><div class="absolute inset-[24%] flex flex-col items-center justify-center rounded-full bg-white shadow-inner"><strong class="text-2xl text-stone-900">{{ $statusTotal }}</strong><span class="text-[10px] uppercase text-stone-500">Total</span></div></div><div class="space-y-3">@foreach($statusChart as $item)<div class="flex items-center justify-between gap-3 text-sm"><span class="flex items-center gap-2 text-stone-700"><i class="h-3 w-3 rounded-full" style="background-color: {{ $item['color'] }}"></i>{{ $item['label'] }}</span><strong>{{ $item['value'] }} <small class="font-normal text-stone-400">({{ $statusTotal > 0 ? round(($item['value'] / $statusTotal) * 100) : 0 }}%)</small></strong></div>@endforeach</div></div>
                    </article>
                    <article class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                        <div><h4 class="font-semibold text-stone-900">Komposisi Akad</h4><p class="text-xs text-stone-500">Proporsi pembiayaan berdasarkan akad</p></div>
                        <div class="mt-6 grid items-center gap-6 sm:grid-cols-[190px_1fr]"><div class="relative mx-auto h-44 w-44 rounded-full" style="background: conic-gradient({{ $akadGradient }})"><div class="absolute inset-[24%] flex flex-col items-center justify-center rounded-full bg-white shadow-inner"><strong class="text-2xl text-stone-900">{{ $akadTotal }}</strong><span class="text-[10px] uppercase text-stone-500">Total</span></div></div><div class="space-y-3">@forelse($akadChart as $item)<div class="flex items-center justify-between gap-3 text-sm"><span class="flex items-center gap-2 text-stone-700"><i class="h-3 w-3 rounded-full" style="background-color: {{ $item['color'] }}"></i>{{ $item['label'] }}</span><strong>{{ $item['value'] }} <small class="font-normal text-stone-400">({{ $akadTotal > 0 ? round(($item['value'] / $akadTotal) * 100) : 0 }}%)</small></strong></div>@empty<p class="text-sm text-stone-500">Belum ada data akad.</p>@endforelse</div></div>
                    </article>
                </div>
            </section>
            <section x-show="activeTab === 'ringkasan'" x-cloak class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b px-5 py-4"><h3 class="font-semibold">Laporan Pembiayaan Terbaru</h3><p class="text-sm text-slate-500">Pemantauan tanpa aksi persetujuan atau perubahan data.</p></div>
                    <div class="overflow-x-auto"><table class="w-full min-w-[620px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Anggota</th><th class="px-5 py-3">Nilai</th><th class="px-5 py-3">Status</th></tr></thead>
                        <tbody class="divide-y">@forelse ($pembiayaanTerbaru as $item)<tr><td class="px-5 py-4 font-medium">{{ $item->kode }}</td><td class="px-5 py-4">{{ $item->user?->name ?? '-' }}</td><td class="px-5 py-4">Rp {{ number_format((float) $item->jumlah_pembiayaan, 0, ',', '.') }}</td><td class="px-5 py-4">{{ str($item->status)->replace('_', ' ')->title() }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">Belum ada data pembiayaan.</td></tr>@endforelse</tbody>
                    </table></div>
                </div>
                <aside class="space-y-4">
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-5"><h3 class="font-semibold text-indigo-950">Keterangan Statistik</h3><dl class="mt-3 space-y-2 text-sm text-indigo-900"><div><dt class="font-semibold">Menunggu</dt><dd>Pengajuan yang belum memperoleh keputusan.</dd></div><div><dt class="font-semibold">Aktif/berjalan</dt><dd>Pembiayaan yang disetujui dan masih berjalan.</dd></div><div><dt class="font-semibold">Tertunggak</dt><dd>Angsuran yang melewati jatuh tempo dan belum dibayar.</dd></div></dl></div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5"><p class="font-semibold text-amber-900">Perlu perhatian</p><p class="mt-3 text-3xl font-bold">{{ $stats['angsuran_tertunggak'] }}</p><p class="text-sm text-amber-800">angsuran belum dibayar atau terlambat</p></div>
                    <div class="rounded-xl border bg-white p-5"><h3 class="font-semibold">Kedudukan Ketua</h3><ul class="mt-3 space-y-2 text-sm text-slate-600"><li>• Memantau program dan unit usaha</li><li>• Menerima laporan Pengurus</li><li>• Memberikan arahan dan evaluasi</li><li>• Bertanggung jawab kepada Rapat Anggota</li></ul></div>
                </aside>
            </section>
            @include('dashboard.ketua.partials.panels')
            </main>
        </div>
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 lg:hidden"></div>
    </div>
</x-app-layout>
