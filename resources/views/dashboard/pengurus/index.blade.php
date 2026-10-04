<x-app-layout>
    @php
        $rupiah = fn ($v) => 'Rp ' . number_format($v ?? 0, 0, ',', '.');
        $safeRoute = fn ($route) => route($route);
        $heroStats = [
            ['label' => 'Anggota', 'value' => (string) ($stats['total_users'] ?? 0), 'meta' => ($stats['anggota_baru'] ?? 0) . ' baru hari ini'],
            ['label' => 'Simpanan', 'value' => $rupiah($stats['total_simpanan'] ?? 0), 'meta' => 'Saldo tercatat'],
            ['label' => 'Pembiayaan', 'value' => $rupiah($stats['pembiayaan_aktif'] ?? 0), 'meta' => 'Aktif berjalan'],
            ['label' => 'Marketplace', 'value' => $rupiah($stats['omzet_marketplace'] ?? 0), 'meta' => 'Omzet bulan ini'],
        ];
        $pengurusTabs = [
            'dashboard' => 'Dashboard Pengurus',
            'anggota' => 'Manajemen Anggota',
            'simpanan' => 'Simpanan',
            'pembiayaan' => 'Pembiayaan',
            'keuangan' => 'Keuangan',
            'marketplace' => 'Marketplace',
            'laporan' => 'Laporan',
            'notifikasi' => 'Notifikasi',
            'pengaturan' => 'Pengaturan',
        ];
    @endphp

    <div
        class="min-h-screen w-full"
        x-data="{
            sidebarOpen: false,
            tabs: {{ Js::from($pengurusTabs) }},
            activeTab: 'dashboard',
            init() {
                const requestedTab = {{ Js::from(request('tab', 'dashboard')) }};
                this.activeTab = Object.prototype.hasOwnProperty.call(this.tabs, requestedTab)
                    ? requestedTab
                    : 'dashboard';
            },
            setActiveTab(tab) {
                if (!Object.prototype.hasOwnProperty.call(this.tabs, tab)) return;

                this.activeTab = tab;
                this.sidebarOpen = false;

                const url = new URL(window.location.href);
                if (tab === 'dashboard') {
                    url.searchParams.delete('tab');
                } else {
                    url.searchParams.set('tab', tab);
                }
                url.hash = '';
                window.history.replaceState({}, '', url);

                this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
            }
        }"
    >

        {{-- Sidebar --}}
        @include('dashboard.pengurus.partials.sidebar')

        {{-- Main Content --}}
        <div class="dashboard-page lg:ml-64 min-h-screen">

            {{-- Navbar --}}
            @include('dashboard.pengurus.partials.navbar')

            {{-- Page Content --}}
            <div class="mx-auto max-w-[1600px] space-y-6 p-4 lg:p-6">

                {{-- Dashboard Overview (default) --}}
                <div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">
                    <div class="dashboard-hero animate-fade-in-up">
                        <div class="dashboard-shell p-5 lg:p-6">
                            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                                <div class="max-w-xl">
                                    <div class="flex items-center gap-3">
                                        <span class="dashboard-badge">
                                            <span class="dashboard-badge-dot"></span>
                                            Realtime overview
                                        </span>
                                        <span class="dashboard-inline-label">
                                            <span class="dot"></span>
                                            live
                                        </span>
                                    </div>
                                    <h2 class="mt-4 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                                        Kinerja koperasi hari ini
                                    </h2>
                                    <p class="mt-3 max-w-lg text-sm leading-6 text-indigo-100 lg:text-base">
                                        Pantau anggota, simpanan, pembiayaan, dan marketplace dari satu dashboard yang lebih ringkas, interaktif, dan siap dipakai untuk pengambilan keputusan cepat.
                                    </p>

                                    <div class="mt-5 flex flex-wrap gap-3">
                                        <button type="button" @click="setActiveTab('pembiayaan')" class="dashboard-action-btn primary">
                                            Review pembiayaan
                                        </button>
                                        <button type="button" @click="setActiveTab('simpanan')" class="dashboard-action-btn secondary">
                                            Pantau simpanan
                                        </button>
                                    </div>

                                    <div class="dashboard-status-row">
                                        <div class="dashboard-status-pill">
                                            <span class="label">Pengajuan</span>
                                            <span class="value">{{ $stats['pengajuan'] ?? 0 }}</span>
                                            <span class="meta">hari ini</span>
                                        </div>
                                        <div class="dashboard-status-pill">
                                            <span class="label">Simpanan</span>
                                            <span class="value">{{ $rupiah($stats['total_simpanan'] ?? 0) }}</span>
                                            <span class="meta">terkumpul</span>
                                        </div>
                                        <div class="dashboard-status-pill">
                                            <span class="label">Toko</span>
                                            <span class="value">{{ $stats['total_toko'] ?? 0 }}</span>
                                            <span class="meta">aktif & pending</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dashboard-compact-grid w-full max-w-xl">
                                    @foreach ($heroStats as $stat)
                                        <div class="dashboard-compact-card">
                                            <span class="meta">{{ $stat['label'] }}</span>
                                            <span class="value">{{ $stat['value'] }}</span>
                                            <span class="sub">{{ $stat['meta'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trends and action queue --}}
                    @include('dashboard.pengurus.partials.insights')

                    {{-- Stats --}}
                    @include('dashboard.pengurus.partials.stats')

                    {{-- Task Zone --}}
                    @include('dashboard.pengurus.partials.task-zone')
                </div>

                {{-- Sub Menu Panels (tab-based) --}}
                @include('dashboard.pengurus.partials.anggota')
                @include('dashboard.pengurus.partials.simpanan')
                @include('dashboard.pengurus.partials.pembiayaan')
                @include('dashboard.pengurus.partials.keuangan')
                @include('dashboard.pengurus.partials.marketplace')
                @include('dashboard.pengurus.partials.laporan')
                @include('dashboard.pengurus.partials.notifikasi')
                @include('dashboard.pengurus.partials.pengaturan')

            </div>
        </div>

    </div>
</x-app-layout>
