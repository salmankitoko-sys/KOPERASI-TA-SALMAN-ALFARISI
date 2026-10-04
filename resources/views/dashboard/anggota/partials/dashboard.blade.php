{{-- ========== PANEL: DASHBOARD ========== --}}
<div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">
    @php
        $statistikPesananMasuk = $statistikPesananMasuk ?? (object) ['menunggu' => 0, 'dikemas' => 0, 'dikirim' => 0, 'selesai' => 0];
        $tokoSummary = $tokoSummary ?? (object) ['pesanan_aktif' => 0, 'omzet_selesai' => 0];
        $statusBadgeClass = [
            'diajukan' => 'bg-amber-100 text-amber-700',
            'disetujui' => 'bg-blue-100 text-blue-700',
            'berjalan' => 'bg-emerald-100 text-emerald-700',
            'ditolak' => 'bg-red-100 text-red-700',
            'lunas' => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    {{-- Ringkasan Metrik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm hover:border-indigo-200 transition">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Simpanan</span>
                        <p class="text-xs text-gray-500">Gabungan pokok, wajib & sukarela</p>
                    </div>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 tracking-tight">{{ $rupiah($simpanan->total) }}</p>
            <button @click="activeTab = 'simpanan'" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 mt-3 inline-flex items-center gap-1">
                Lihat Rincian
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm hover:border-indigo-200 transition">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pembiayaan Berjalan</span>
                        <p class="text-xs text-gray-500">{{ $pembiayaanSummary->total_aktif ?? 0 }} akad aktif</p>
                    </div>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 tracking-tight">{{ $rupiah($pembiayaanSummary->total_sisa_pokok ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">Angsuran/bulan {{ $rupiah($pembiayaanSummary->total_angsuran_bulanan ?? 0) }}</p>
            <button @click="activeTab = 'pembiayaan'" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 mt-3 inline-flex items-center gap-1">
                Kelola Pembiayaan
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm hover:border-indigo-200 transition">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-violet-50 text-violet-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 11H4L5 9z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pesanan Masuk</span>
                        <p class="text-xs text-gray-500">{{ $statistikPesananMasuk->menunggu ?? 0 }} menunggu diproses</p>
                    </div>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 tracking-tight">
                {{ $tokoSummary->pesanan_aktif ?? 0 }} <span class="text-sm font-semibold text-gray-400">Aktif</span>
            </p>
            <button @click="setActiveTab('pesanan-masuk')" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 mt-3 inline-flex items-center gap-1">
                Kelola Pesanan Masuk
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </div>


    </div>

    
   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Card 1 --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-800">Status Pembiayaan Aktif / Akad</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Total plafon {{ $rupiah($pembiayaanSummary->total_plafon ?? 0) }}
                    @if(!empty($pembiayaanSummary->jatuh_tempo_terdekat))
                        · jatuh tempo terdekat {{ \Carbon\Carbon::parse($pembiayaanSummary->jatuh_tempo_terdekat)->translatedFormat('d M Y') }}
                    @endif
                </p>
            </div>
            <button type="button" @click="activeTab = 'pembiayaan'" class="text-sm text-blue-600 hover:underline">Detail</button>
        </div>

        {{-- List --}}
        <div class="divide-y divide-gray-100">
            @forelse($pembiayaanAktif->take(4) as $p)
                <div class="py-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-gray-800 text-sm">{{ $p->kode }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold uppercase">{{ $p->akad_label }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $statusBadgeClass[$p->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $p->status_label }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $p->objek_pembiayaan ?: ($p->detail->objek ?? 'Objek pembiayaan belum diisi') }}</p>
                            <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] text-gray-500">
                                <span>Tenor: <strong class="text-gray-700">{{ $p->tenor }} bulan</strong></span>
                                <span>Angsuran: <strong class="text-gray-700">{{ $rupiah($p->angsuran_bulanan) }}</strong></span>
                                <span>Jatuh tempo: <strong class="text-gray-700">{{ $p->jatuhTempo ? \Carbon\Carbon::parse($p->jatuhTempo)->translatedFormat('d M Y') : '-' }}</strong></span>
                                <span>Sisa tagihan: <strong class="text-gray-700">{{ $rupiah($p->sisaTagihan) }}</strong></span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-sm text-gray-900">{{ $rupiah($p->sisaPokok) }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">Sisa pokok</p>
                        </div>
                    </div>
                    <div class="mt-3 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-indigo-600" style="width: {{ $p->progressPersen }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $p->angsuranLunas }} dari {{ $p->tenor }} angsuran selesai</p>
                </div>
            @empty
                <div class="py-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm font-semibold text-gray-700">Belum ada pembiayaan aktif.</p>
                    <p class="text-xs text-gray-500 mt-1">Pengajuan yang sudah dibuat akan tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Card 2 --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        @php
            $pesananMasuk = $pesananMasuk ?? collect();
            $statusPesananClass = [
                'menunggu' => 'bg-amber-100 text-amber-700',
                'dikemas' => 'bg-blue-100 text-blue-700',
                'dikirim' => 'bg-violet-100 text-violet-700',
                'selesai' => 'bg-emerald-100 text-emerald-700',
                'batal' => 'bg-red-100 text-red-700',
            ];
            $statusPesananLabel = [
                'menunggu' => 'Menunggu',
                'dikemas' => 'Dikemas',
                'dikirim' => 'Dikirim',
                'selesai' => 'Selesai',
                'batal' => 'Batal',
            ];
        @endphp

        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-800">Pesanan Masuk</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $tokoSummary->pesanan_aktif ?? 0 }} pesanan aktif
                </p>
            </div>
            @if(isset($lapak) && $lapak)
                <button type="button" @click="activeTab = 'pesanan-masuk'" class="text-sm text-blue-600 hover:underline">Lihat Semua</button>
            @endif
        </div>

        @if(!isset($lapak) || !$lapak)
            <div class="py-8 text-center bg-amber-50/50 rounded-xl border border-dashed border-amber-200">
                <p class="text-sm font-semibold text-amber-800">Belum ada toko.</p>
                <p class="text-xs text-gray-500 mt-1">Buka toko terlebih dahulu untuk menerima pesanan.</p>
                <button type="button" @click="activeTab = 'toko'" class="mt-3 text-xs font-bold text-indigo-600 hover:text-indigo-700">Buka Toko</button>
            </div>
        @elseif($pesananMasuk->isEmpty())
            <div class="py-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <p class="text-sm font-semibold text-gray-700">Belum ada pesanan masuk.</p>
                <p class="text-xs text-gray-500 mt-1">Pesanan pembeli akan tampil otomatis di sini.</p>
            </div>
        @else
            <div class="grid grid-cols-4 gap-2 mb-3">
                <div class="rounded-lg bg-amber-50 border border-amber-100 p-2 text-center">
                    <p class="text-[10px] font-bold text-amber-600 uppercase">Baru</p>
                    <p class="text-sm font-black text-amber-700">{{ $statistikPesananMasuk->menunggu ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-blue-50 border border-blue-100 p-2 text-center">
                    <p class="text-[10px] font-bold text-blue-600 uppercase">Dikemas</p>
                    <p class="text-sm font-black text-blue-700">{{ $statistikPesananMasuk->dikemas ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-violet-50 border border-violet-100 p-2 text-center">
                    <p class="text-[10px] font-bold text-violet-600 uppercase">Dikirim</p>
                    <p class="text-sm font-black text-violet-700">{{ $statistikPesananMasuk->dikirim ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-2 text-center">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase">Selesai</p>
                    <p class="text-sm font-black text-emerald-700">{{ $statistikPesananMasuk->selesai ?? 0 }}</p>
                </div>
            </div>

            {{-- List --}}
            <div class="divide-y divide-gray-100">
                @foreach($pesananMasuk->take(3) as $pm)
                    @php
                        $produkPesanan = $pm->items->pluck('nama_produk_snapshot')->filter()->take(2)->implode(', ') ?: 'Produk pesanan';
                        $totalPesanan = $pm->total ?? $pm->items->sum('subtotal');
                    @endphp
                    <div class="flex items-start justify-between gap-3 py-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-indigo-600">
                                    {{ collect(explode(' ', $pm->pembeli->name ?? 'P'))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 text-sm truncate">{{ $produkPesanan }}</p>
                                <p class="text-xs text-gray-500 truncate">Pembeli: {{ $pm->pembeli->name ?? 'Pembeli' }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $pm->items->count() }} item &middot; {{ \Carbon\Carbon::parse($pm->created_at)->translatedFormat('d M H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-semibold text-sm text-gray-800">{{ $rupiah($totalPesanan) }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full mt-1 inline-block font-bold {{ $statusPesananClass[$pm->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusPesananLabel[$pm->status] ?? ucfirst($pm->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

    {{-- ALUR PROSES BUKA LAPAK --}}
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-3">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900">Alur Membuka Lapak Usaha</h4>
                <p class="text-xs text-gray-500">Tahapan verifikasi menjadi merchant mitra koperasi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 relative">
            @foreach($alurSteps as $i => $step)
                <div class="flex md:flex-col items-center md:items-start p-3 rounded-xl transition {{ $i <= $alurCurrentIndex ? 'bg-emerald-50/60 border border-emerald-100' : 'bg-gray-50 border border-gray-100/70' }}">
                    <div @class([
                        'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 shadow-sm mr-3 md:mr-0 md:mb-2.5',
                        'bg-emerald-600 text-white' => $i <= $alurCurrentIndex,
                        'bg-gray-200 text-gray-500' => $i > $alurCurrentIndex,
                    ])>
                        @if($i < $alurCurrentIndex)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-900">{{ $step['title'] }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 bg-indigo-50/50 border border-indigo-100/70 rounded-xl p-4 flex gap-3 items-start">
            <span class="text-lg">💡</span>
            <p class="text-xs text-indigo-900 leading-relaxed">
                <strong>Koneksi Strategis:</strong> Data performa omzet toko Anda terhubung langsung dengan sistem penilaian <strong>Skor Kredit Syariah</strong> di koperasi. Semakin konsisten penjualan Anda, semakin besar plafon pembiayaan modal usaha tanpa jaminan yang bisa Anda dapatkan.
            </p>
        </div>
    </div>

    {{-- DETAIL 2-KOLOM --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-sm">
            <h5 class="font-bold text-sm text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2.5 mb-3 text-indigo-600">Rincian Finansial Syariah</h5>
            <ul class="space-y-3">
                <li class="flex justify-between items-center bg-gray-50 p-2.5 rounded-xl text-xs">
                    <span class="text-gray-600 font-medium">Estimasi Bagi Hasil</span>
                    <span class="font-bold text-gray-900">{{ $rupiah($bagiHasil->estimasi) }} <span class="text-[10px] text-gray-400 font-normal">({{ $bagiHasil->periode ?? '-' }})</span></span>
                </li>
                <li class="flex justify-between items-center bg-gray-50 p-2.5 rounded-xl text-xs">
                    <span class="text-gray-600 font-medium">Pembiayaan Berjalan</span>
                    <span class="font-bold {{ $pembiayaanAktif->isEmpty() ? 'text-gray-400' : 'text-indigo-600' }}">
                        {{ $pembiayaanAktif->isEmpty() ? 'Tidak Ada' : $rupiah($pembiayaanSummary->total_sisa_pokok ?? 0) }}
                    </span>
                </li>
                @if($notifikasiAngsuran->isNotEmpty())
                    <li class="bg-red-50 border border-red-100 p-2.5 rounded-xl text-xs text-red-800 space-y-1">
                        <p class="font-bold flex items-center gap-1">⚠ Pengingat Penting:</p>
                        @foreach($notifikasiAngsuran as $notif)
                            <p class="text-[11px] opacity-90">• {{ $notif->pesan ?? '' }}</p>
                        @endforeach
                    </li>
                @endif
            </ul>
        </div>

        <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-sm">
            <h5 class="font-bold text-sm text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2.5 mb-3 text-amber-600">Status Permohonan Modal</h5>
            @if(is_null($lapak))
                <div class="flex flex-col items-center justify-center text-center py-6 px-4 bg-amber-50/40 border border-dashed border-amber-200 rounded-xl">
                    <p class="text-xs text-amber-800 font-medium">Fitur pembiayaan modal usaha belum terbuka.</p>
                    <p class="text-[11px] text-gray-400 mt-1">Anda diwajibkan menyelesaikan konfigurasi toko & upload produk ritel pertama.</p>
                </div>
            @else
                <ul class="space-y-3">
                    <li class="flex justify-between items-center bg-gray-50 p-2.5 rounded-xl text-xs">
                        <span class="text-gray-600 font-medium">Nama Toko Mitra</span>
                        <span class="font-bold text-gray-900">{{ $lapak->nama_toko }}</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-2.5 rounded-xl text-xs">
                        <span class="text-gray-600 font-medium">Status Pengajuan Modal</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded text-[10px] uppercase">
                            {{ $pembiayaanUsahaAktif->status ?? 'Belum Ada' }}
                        </span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-2.5 rounded-xl text-xs">
                        <span class="text-gray-600 font-medium">Skor Kredit Internal</span>
                        <span class="text-emerald-600 font-bold">{{ $skorKredit->skor ?? 'Menghitung...' }}</span>
                    </li>
                    <li class="pt-1">
                        <a href="{{ route('anggota.pembiayaan.ajukan', ['tujuan' => 'modal_usaha']) }}"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700">
                            Ajukan Modal Usaha
                        </a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
