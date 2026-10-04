<x-app-layout>
    @php
        $anggota            = $anggota ?? auth()->user();
        $simpanan           = $simpanan ?? (object) ['pokok' => 0, 'wajib' => 0, 'sukarela' => 0, 'total' => 0, 'pending' => 0];
        $riwayatSimpanan    = $riwayatSimpanan ?? collect();
        $pembiayaanAktif    = $pembiayaanAktif ?? collect();
        $pembiayaanSummary  = $pembiayaanSummary ?? (object) ['total_aktif' => 0, 'total_plafon' => 0, 'total_sisa_pokok' => 0, 'total_angsuran_bulanan' => 0, 'total_tagihan_mendatang' => 0, 'jatuh_tempo_terdekat' => null];
        $bagiHasil          = $bagiHasil ?? (object) ['estimasi' => 0, 'periode' => null];
        $notifikasiAngsuran = $notifikasiAngsuran ?? collect();
        $lapak              = $lapak ?? null;
        $pembiayaanUsahaAktif = $pembiayaanUsahaAktif ?? null;
        $skorKredit         = $skorKredit ?? null;
        $inbox              = $inbox ?? collect();
        $produkList         = $produkList ?? collect();
        $alurStatus         = $alurStatus ?? 'belum_verifikasi';

        $rupiah = fn ($v) => class_exists(\App\Helpers\RupiahHelper::class)
            ? \App\Helpers\RupiahHelper::format($v ?? 0)
            : 'Rp ' . number_format($v ?? 0, 0, ',', '.');

        $alurSteps = [
            ['key' => 'verifikasi',    'title' => 'Verifikasi',      'desc' => 'Status aktif & simpanan pokok'],
            ['key' => 'buka_toko',     'title' => 'Buka Toko',       'desc' => 'Isi profil & kategori toko'],
            ['key' => 'upload_produk', 'title' => 'Upload Produk',   'desc' => 'Nama, harga, & deskripsi'],
            ['key' => 'moderasi',      'title' => 'Moderasi',        'desc' => 'Pengecekan oleh pengurus'],
            ['key' => 'tayang',        'title' => 'Produk Tayang',   'desc' => 'Tersedia di marketplace'],
        ];
        $alurOrder = array_column($alurSteps, 'key');
        $alurCurrentIndex = array_search($alurStatus, $alurOrder);
        $alurCurrentIndex = $alurCurrentIndex === false ? -1 : $alurCurrentIndex;

        // Daftar menu tunggal, dipakai untuk sidebar & mapping panel konten
        $menuGroups = [
            [
                'label' => null,
                'items' => [
                    ['key' => 'dashboard', 'title' => 'Dashboard', 'icon' => 'home'],
                ],
            ],
            [
                'label' => 'Zona Koperasi',
                'items' => [
                    ['key' => 'pembiayaan', 'title' => 'Pembiayaan', 'icon' => 'doc'],                    

                    ['key' => 'angsuran', 'title' => 'Angsuran', 'icon' => 'money'],
                    ['key' => 'simpanan', 'title' => 'Simpanan', 'icon' => 'wave'],
                    ['key' => 'keuangan', 'title' => 'Keuangan', 'icon' => 'wallet'],
                    ['key' => 'notifikasi', 'title' => 'Notifikasi', 'icon' => 'inbox'],


                ],
            ],
            [
                'label' => 'Marketplace',
                'items' => [
                    ['key' => 'toko', 'title' => 'Toko Saya', 'icon' => 'store'],
                    ['key' => 'produk', 'title' => 'Produk & Stok', 'icon' => 'clip'],
                    ['key' => 'pesanan-masuk', 'title' => 'Pesanan Masuk', 'icon' => 'inbox'],
                ],
            ],
        ];

        $anggotaTabs = collect($menuGroups)
            ->flatMap(fn (array $group) => collect($group['items'])->pluck('key'))
            ->push('profil')
            ->values()
            ->all();
    @endphp

    <div
        class="min-h-screen w-full"
        x-data="{
            sidebarOpen: false,
            tabs: @js($anggotaTabs),
            activeTab: 'dashboard',
            init() {
                const url = new URL(window.location.href);
                const requestedTab = url.searchParams.get('tab') ?? url.searchParams.get('active_tab') ?? 'dashboard';

                this.activeTab = this.tabs.includes(requestedTab) ? requestedTab : 'dashboard';
                this.$watch('activeTab', (tab) => this.updateTabInUrl(tab));

                if (url.searchParams.has('tab') || url.searchParams.has('active_tab')) {
                    this.updateTabInUrl(this.activeTab);
                }
            },
            updateTabInUrl(tab) {
                if (!this.tabs.includes(tab)) return;

                const url = new URL(window.location.href);
                if (tab === 'dashboard') {
                    url.searchParams.delete('tab');
                } else {
                    url.searchParams.set('tab', tab);
                }
                url.searchParams.delete('active_tab');
                url.hash = '';
                window.history.replaceState({}, '', url);
            },
            setActiveTab(tab) {
                if (!this.tabs.includes(tab)) return;

                this.activeTab = tab;
                this.sidebarOpen = false;
                this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
            }
        }"
    >

    @include('dashboard.anggota.partials.sidebar')

    <div class="min-h-screen lg:ml-72">
        @include('dashboard.anggota.partials.navbar')

        <main class="p-4 lg:p-6">
            {{-- ================= MAIN CONTENT (semua panel di-render, ditampilkan via x-show) ================= --}}
            <div class="w-full space-y-6">

                @include('dashboard.anggota.partials.dashboard')
                @include('dashboard.anggota.partials.simpanan')
                @include('dashboard.anggota.partials.pembiayaan')
                @include('dashboard.anggota.partials.toko')
                @include('dashboard.anggota.partials.produk')
                @include('dashboard.anggota.partials.pesanan-masuk')
                @include('dashboard.anggota.partials.profil')
                @include('dashboard.anggota.partials.angsuran')
                @include('dashboard.anggota.partials.keuangan')
                @include('dashboard.anggota.partials.notifikasi')

            </div>
        </main>
    </div>
</div>
</x-app-layout>
