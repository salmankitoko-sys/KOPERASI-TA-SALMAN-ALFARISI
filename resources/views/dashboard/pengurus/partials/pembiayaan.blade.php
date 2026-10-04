@php
    // Pastikan variabel rupiah ada
    if (!isset($rupiah) || !is_callable($rupiah)) {
        $rupiah = fn ($v) => 'Rp ' . number_format($v ?? 0, 0, ',', '.');
    }
@endphp

{{--
    Panel Pembiayaan (v2)
    x-show="activeTab === 'pembiayaan'"
    Sub-tabs: daftar, detail_approve, angsuran, riwayat
--}}
<section x-show="activeTab === 'pembiayaan'" x-cloak
         x-data="pembiayaanApp()"
         x-init="init()">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Pembiayaan</h3>
            <p class="text-sm text-gray-500">Pengajuan, Persetujuan, Angsuran & Riwayat Pembiayaan</p>
        </div>
        <div class="flex gap-2">
            <button @click="refreshData()" class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- Sub Navigation --}}
    <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200 pb-3">
        <button @click="subTab = 'daftar'; loadData()"
                :class="subTab === 'daftar' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition">
            📋 Daftar Pengajuan
        </button>
        <template x-if="selectedId">
            <button @click="subTab = 'detail'; loadDetail()"
                    :class="subTab === 'detail' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition">
                📄 Detail & Status Akad
            </button>
        </template>
        <template x-if="selectedId">
            <button @click="subTab = 'angsuran'; loadAngsuran()"
                    :class="subTab === 'angsuran' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition">
                📅 Jadwal Angsuran
            </button>
        </template>
        <template x-if="selectedId">
            <button @click="subTab = 'riwayat'; loadRiwayat()"
                    :class="subTab === 'riwayat' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition">
                🔄 Riwayat Pembayaran
            </button>
        </template>
        <template x-if="selectedId">
            <button @click="selectedId = null; subTab = 'daftar'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition bg-gray-100 text-gray-500 hover:bg-gray-200">
                ✕ Tutup Detail
            </button>
        </template>
    </div>

    {{-- ==================== SUB-TAB: DAFTAR PENGAJUAN ==================== --}}
    <div x-show="subTab === 'daftar'" x-cloak class="space-y-6">

        {{-- Ringkasan Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 mb-1">Total Pembiayaan</p>
                <p class="text-lg font-bold text-indigo-600" x-text="rupiah(stats.pembiayaan_aktif)">Rp 0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 mb-1">Pengajuan Baru</p>
                <p class="text-lg font-bold text-amber-600" x-text="stats.pengajuan_baru">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 mb-1">Disetujui / Berjalan</p>
                <p class="text-lg font-bold text-emerald-600">
                    <span x-text="stats.total_disetujui"></span> / <span x-text="stats.total_berjalan"></span>
                </p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 mb-1">NPF</p>
                <p class="text-lg font-bold text-rose-600" x-text="stats.npf + '%'">0%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 mb-1">Angsuran Hari Ini</p>
                <p class="text-lg font-bold text-sky-600" x-text="stats.angsuran_hari_ini">0</p>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="filters.search" placeholder="Cari kode / nama anggota..." @input.debounce="loadData()" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <select x-model="filters.status" @change="loadData()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan">Diajukan</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="berjalan">Berjalan</option>
                    <option value="lunas">Lunas</option>
                </select>
                <select x-model="filters.akad" @change="loadData()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Akad</option>
                    <option value="murabahah">Murabahah</option>
                    <option value="mudharabah">Mudharabah</option>
                    <option value="musyarakah">Musyarakah</option>
                    <option value="ijarah">Ijarah</option>
                </select>
            </div>
        </div>

        {{-- Tabel Daftar Pengajuan --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">#</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Kode</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Anggota</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Akad / Tujuan</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Jumlah</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Tenor</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Tgl Pengajuan</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Validasi DPS</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(p, idx) in pembiayaanList.data" :key="p.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500" x-text="pembiayaanList.from + idx"></td>
                                <td class="px-4 py-3 font-bold text-gray-900" x-text="p.kode"></td>
                                <td class="px-4 py-3 font-medium text-gray-900" x-text="p.user?.name || 'N/A'"></td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                          :class="{'bg-purple-100 text-purple-700': p.akad === 'murabahah', 'bg-cyan-100 text-cyan-700': p.akad === 'mudharabah', 'bg-emerald-100 text-emerald-700': p.akad === 'musyarakah', 'bg-orange-100 text-orange-700': p.akad === 'ijarah', 'bg-teal-100 text-teal-700': p.akad === 'qardh'}">
                                        <span x-text="p.akad.charAt(0).toUpperCase() + p.akad.slice(1)"></span>
                                    </span>
                                    <p class="mt-1 text-xs text-gray-500" x-text="p.tujuan_label || 'Kebutuhan Lainnya'"></p>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900" x-text="rupiah(p.jumlah_pembiayaan)"></td>
                                <td class="px-4 py-3 text-gray-600" x-text="p.tenor + ' bln'"></td>
                                <td class="px-4 py-3 text-gray-600" x-text="formatDate(p.created_at)"></td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="{
                                              'bg-amber-100 text-amber-700': p.status === 'diajukan',
                                              'bg-sky-100 text-sky-700': p.status === 'disurvei',
                                              'bg-emerald-100 text-emerald-700': p.status === 'disetujui',
                                              'bg-indigo-100 text-indigo-700': p.status === 'berjalan',
                                              'bg-green-100 text-green-700': p.status === 'lunas',
                                              'bg-red-100 text-red-700': p.status === 'ditolak'
                                          }">
                                        <span x-text="statusLabel(p.status)"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="validasiDpsClass(p.status_validasi_dps)"
                                          x-text="validasiDpsLabel(p.status_validasi_dps)"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <button @click="selectedId = p.id; subTab = 'detail'; loadDetail()"
                                                class="p-1.5 rounded-lg hover:bg-indigo-50 text-indigo-600" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <div x-show="p.status === 'diajukan'" class="flex gap-1.5">
                                            <button @click="approvePembiayaan(p.id)"
                                                    :disabled="p.status_validasi_dps !== 'sesuai'"
                                                    :title="p.status_validasi_dps === 'sesuai' ? 'Setujui pembiayaan' : 'Menunggu akad dinyatakan sesuai syariah oleh DPS'"
                                                    class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition font-medium disabled:cursor-not-allowed disabled:bg-gray-300">
                                                <span x-text="p.status_validasi_dps === 'sesuai' ? 'Setujui' : 'Tunggu DPS'"></span>
                                            </button>
                                            <button @click="openTolak(p)"
                                                    class="px-3 py-1.5 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition font-medium">
                                                Tolak
                                            </button>
                                        </div>
                                        <template x-if="p.status === 'disetujui' || p.status === 'berjalan'">
                                            <button @click="selectedId = p.id; subTab = 'angsuran'; loadAngsuran()"
                                                    class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition font-medium">
                                                Angsuran
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!pembiayaanList.data || pembiayaanList.data.length === 0">
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 12v-2M3 6h18v12H3z"/></svg>
                                <p class="font-medium">Belum ada data pembiayaan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div x-show="pembiayaanList.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Menampilkan <span x-text="pembiayaanList.from"></span> - <span x-text="pembiayaanList.to"></span> dari <span x-text="pembiayaanList.total"></span>
                </p>
                <div class="flex gap-2">
                    <button @click="loadData(pembiayaanList.current_page - 1)" :disabled="!pembiayaanList.prev_page_url"
                            :class="pembiayaanList.prev_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition">
                        Sebelumnya
                    </button>
                    <button @click="loadData(pembiayaanList.current_page + 1)" :disabled="!pembiayaanList.next_page_url"
                            :class="pembiayaanList.next_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition">
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== SUB-TAB: DETAIL & STATUS AKAD ==================== --}}
    <div x-show="subTab === 'detail'" x-cloak class="space-y-6">
        <div x-show="!loadingDetail" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Info Pembiayaan --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-900">Informasi Pembiayaan</h4>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold"
                              :class="{
                                  'bg-amber-100 text-amber-700': detail.pembiayaan?.status === 'diajukan',
                                  'bg-emerald-100 text-emerald-700': detail.pembiayaan?.status === 'disetujui',
                                  'bg-indigo-100 text-indigo-700': detail.pembiayaan?.status === 'berjalan',
                                  'bg-green-100 text-green-700': detail.pembiayaan?.status === 'lunas',
                                  'bg-red-100 text-red-700': detail.pembiayaan?.status === 'ditolak'
                              }">
                            <span x-text="detail.statusLabel"></span>
                        </span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div><span class="text-gray-500">Kode</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.kode"></p></div>
                        <div><span class="text-gray-500">Anggota</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.user?.name"></p></div>
                        <div><span class="text-gray-500">Jenis Akad</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.akadLabel"></p></div>
                        <div><span class="text-gray-500">Tujuan</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.tujuanLabel || 'Kebutuhan Lainnya'"></p></div>
                        <div><span class="text-gray-500">Objek</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.objek_pembiayaan || '-'"></p></div>
                        <div><span class="text-gray-500">Toko</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.toko?.nama_toko || '-'"></p></div>
                        <div><span class="text-gray-500">Jumlah Pembiayaan</span><p class="font-semibold text-gray-900 mt-0.5" x-text="rupiah(detail.pembiayaan?.jumlah_pembiayaan)"></p></div>
                        <div><span class="text-gray-500">Tenor</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.tenor + ' bulan'"></p></div>
                        <div><span class="text-gray-500">Angsuran/Bulan</span><p class="font-semibold text-gray-900 mt-0.5" x-text="rupiah(detail.pembiayaan?.angsuran_bulanan)"></p></div>
                        <div><span class="text-gray-500">Tgl Pengajuan</span><p class="font-semibold text-gray-900 mt-0.5" x-text="formatDate(detail.pembiayaan?.tanggal_pengajuan)"></p></div>
                        <div><span class="text-gray-500">Tgl Persetujuan</span><p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.tanggal_persetujuan ? formatDate(detail.pembiayaan.tanggal_persetujuan) : '-'"></p></div>
                    </div>
                    <div x-show="detail.pembiayaan?.tujuan_pembiayaan === 'modal_usaha'" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm rounded-lg border border-emerald-100 bg-emerald-50/50 p-4">
                        <div>
                            <span class="text-gray-500">Rencana Penggunaan Dana</span>
                            <p class="font-semibold text-gray-900 mt-0.5" x-text="detail.pembiayaan?.rencana_penggunaan_dana || '-'"></p>
                        </div>
                        <div>
                            <span class="text-gray-500">Estimasi Omzet Usaha / Bulan</span>
                            <p class="font-semibold text-gray-900 mt-0.5" x-text="rupiah(detail.pembiayaan?.estimasi_omzet_usaha)"></p>
                        </div>
                    </div>
                </div>

                {{-- Detail Akad --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Detail Akad <span x-text="'(' + detail.akadLabel + ')'"></span></h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <template x-for="(value, key) in detail.detailData" :key="key">
                            <div>
                                <span class="text-gray-500" x-text="key"></span>
                                <p class="font-semibold text-gray-900 mt-0.5" x-text="isNaN(value) ? value : rupiah(value)"></p>
                            </div>
                        </template>
                    </div>
                    <div x-show="Object.keys(detail.detailData).length === 0" class="text-center py-6 text-gray-500">
                        <p>Tidak ada detail akad tersedia.</p>
                    </div>
                </div>

                {{-- Dokumen Pengajuan --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Dokumen Kelengkapan</h4>
                    <div class="space-y-2" x-show="detail.pembiayaan?.dokumen && detail.pembiayaan.dokumen.length">
                        <template x-for="doc in detail.pembiayaan.dokumen" :key="doc.id">
                            <a :href="doc.url" target="_blank" rel="noopener" class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 hover:bg-indigo-50 hover:border-indigo-100 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="doc.jenis_label"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="doc.nama_asli"></p>
                                </div>
                                <span class="text-xs font-bold text-indigo-700 shrink-0" x-text="doc.ukuran_label"></span>
                            </a>
                        </template>
                    </div>
                    <div x-show="!detail.pembiayaan?.dokumen || !detail.pembiayaan.dokumen.length" class="text-center py-6 text-gray-500">
                        <p>Belum ada dokumen yang diunggah.</p>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Validasi DPS --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Validasi Syariah DPS</h4>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold"
                          :class="validasiDpsClass(detail.pembiayaan?.status_validasi_dps)"
                          x-text="detail.validasiDpsLabel || validasiDpsLabel(detail.pembiayaan?.status_validasi_dps)"></span>
                    <div x-show="detail.pembiayaan?.tanggal_validasi_dps" class="mt-3 text-xs text-gray-600 space-y-1">
                        <p><span class="font-semibold">Validator:</span> <span x-text="detail.pembiayaan?.validator_dps?.name || '-'"></span></p>
                        <p><span class="font-semibold">Tanggal:</span> <span x-text="formatDate(detail.pembiayaan?.tanggal_validasi_dps)"></span></p>
                    </div>
                    <p x-show="detail.pembiayaan?.catatan_validasi_dps"
                       class="mt-3 whitespace-pre-line rounded-lg bg-gray-50 p-3 text-xs text-gray-700"
                       x-text="detail.pembiayaan?.catatan_validasi_dps"></p>
                    <p x-show="detail.pembiayaan?.status_validasi_dps !== 'sesuai'" class="mt-3 text-xs text-amber-700">
                        Persetujuan pembiayaan terkunci sampai DPS menyatakan akad sesuai syariah.
                    </p>
                </div>

                {{-- Progress Angsuran --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Progress Angsuran</h4>
                    <div class="text-center mb-3">
                        <p class="text-3xl font-black text-indigo-600" x-text="detail.progressPct + '%'"></p>
                        <p class="text-xs text-gray-500 mt-1">Angsuran telah dibayar</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-3">
                        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" :style="'width:' + detail.progressPct + '%'"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-gray-50 rounded-lg p-2"><p class="font-bold text-gray-900" x-text="detail.totalAngsuran"></p><p class="text-gray-500">Total</p></div>
                        <div class="bg-emerald-50 rounded-lg p-2"><p class="font-bold text-emerald-700" x-text="detail.sudahDibayar"></p><p class="text-gray-500">Dibayar</p></div>
                        <div class="bg-amber-50 rounded-lg p-2"><p class="font-bold text-amber-700" x-text="detail.sisaAngsuran"></p><p class="text-gray-500">Sisa</p></div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Aksi</h4>
                    <div class="space-y-2">
                        <div x-show="detail.pembiayaan?.status === 'diajukan'" class="space-y-2">
                            <button @click="approvePembiayaan(detail.pembiayaan.id)"
                                    :disabled="detail.pembiayaan?.status_validasi_dps !== 'sesuai'"
                                    class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition font-medium disabled:cursor-not-allowed disabled:bg-gray-300">
                                ✅ Setujui Pembiayaan
                            </button>
                            <button @click="openTolak(detail.pembiayaan)"
                                    class="w-full px-4 py-2.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition font-medium">
                                ❌ Tolak Pembiayaan
                            </button>
                        </div>
                        <button @click="selectedId = detail.pembiayaan?.id; subTab = 'angsuran'; loadAngsuran()"
                                class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium">
                            📅 Lihat Jadwal Angsuran
                        </button>
                        <button @click="selectedId = detail.pembiayaan?.id; subTab = 'riwayat'; loadRiwayat()"
                                class="w-full px-4 py-2.5 bg-sky-600 text-white text-sm rounded-lg hover:bg-sky-700 transition font-medium">
                            🔄 Riwayat Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="loadingDetail" class="text-center py-12">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <p class="text-sm text-gray-500 mt-3">Memuat detail...</p>
        </div>
    </div>

    {{-- ==================== SUB-TAB: JADWAL ANGSURAN ==================== --}}
    <div x-show="subTab === 'angsuran'" x-cloak class="space-y-6">
        <div x-show="!loadingAngsuran" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Stats Angsuran --}}
            <div class="lg:col-span-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-500 mb-1">Total Angsuran</p>
                    <p class="text-xl font-bold text-gray-900" x-text="angsuranData.totalAngsuran"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-500 mb-1">Sudah Dibayar</p>
                    <p class="text-xl font-bold text-emerald-600" x-text="angsuranData.sudahDibayar"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-500 mb-1">Belum Dibayar</p>
                    <p class="text-xl font-bold text-amber-600" x-text="angsuranData.belumDibayar"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-500 mb-1">Terlambat</p>
                    <p class="text-xl font-bold text-red-600" x-text="angsuranData.totalTerlambat"></p>
                </div>
            </div>

            {{-- Tabel Angsuran --}}
            <div class="lg:col-span-4 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">
                        Jadwal Angsuran - <span x-text="angsuranData.pembiayaan?.kode"></span>
                    </h4>
                    <span class="text-xs text-gray-500">
                        Anggota: <strong x-text="angsuranData.pembiayaan?.user?.name"></strong>
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Bulan Ke-</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Jumlah Bayar</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Pokok</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Margin</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Sisa Pokok</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-600">Jatuh Tempo</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-600">Tgl Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="a in angsuranData.angsuranList" :key="a.id">
                                <tr class="hover:bg-gray-50 transition"
                                    :class="{'bg-red-50/50': a.terlambat}">
                                    <td class="px-4 py-3 font-medium text-gray-900" x-text="'Angsuran ke-' + a.bulan_ke"></td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900" x-text="rupiah(a.jumlah_bayar)"></td>
                                    <td class="px-4 py-3 text-right text-gray-700" x-text="rupiah(a.pokok)"></td>
                                    <td class="px-4 py-3 text-right text-gray-700" x-text="rupiah(a.margin)"></td>
                                    <td class="px-4 py-3 text-right text-gray-600" x-text="rupiah(a.sisa_pokok)"></td>
                                    <td class="px-4 py-3 text-center text-gray-600" x-text="formatDate(a.jatuh_tempo)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                                              :class="a.status === 'dibayar' ? 'bg-emerald-100 text-emerald-700' : a.terlambat ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'">
                                            <span x-text="a.status_label"></span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600" x-text="a.tanggal_bayar ? formatDate(a.tanggal_bayar) : '-'"></td>
                                </tr>
                            </template>
                            <tr x-show="!angsuranData.angsuranList || angsuranData.angsuranList.length === 0">
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <p class="font-medium">Belum ada jadwal angsuran.</p>
                                    <p class="text-xs mt-1">Setujui pembiayaan untuk generate angsuran otomatis.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div x-show="loadingAngsuran" class="text-center py-12">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <p class="text-sm text-gray-500 mt-3">Memuat jadwal angsuran...</p>
        </div>
    </div>

    {{-- ==================== SUB-TAB: RIWAYAT PEMBAYARAN ==================== --}}
    <div x-show="subTab === 'riwayat'" x-cloak class="space-y-6">
        <div x-show="!loadingRiwayat" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                    <p class="text-lg font-bold text-gray-900" x-text="riwayatData.totalRiwayat + 'x'"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Total Telah Dibayar</p>
                    <p class="text-lg font-bold text-emerald-600" x-text="rupiah(riwayatData.totalTelahDibayar)"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Kode Pembiayaan</p>
                    <p class="text-lg font-bold text-indigo-600" x-text="riwayatData.pembiayaan?.kode || '-'"></p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-900">Riwayat Pembayaran Angsuran</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">#</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Bulan Ke-</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Jumlah Bayar</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Pokok</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Margin</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600">Sisa Pokok</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-600">Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(r, idx) in riwayatData.riwayat" :key="r.id">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500" x-text="idx + 1"></td>
                                    <td class="px-4 py-3 font-medium text-gray-900" x-text="'Angsuran ke-' + r.bulan_ke"></td>
                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600" x-text="rupiah(r.jumlah_bayar)"></td>
                                    <td class="px-4 py-3 text-right text-gray-700" x-text="rupiah(r.pokok)"></td>
                                    <td class="px-4 py-3 text-right text-gray-700" x-text="rupiah(r.margin)"></td>
                                    <td class="px-4 py-3 text-right text-gray-600" x-text="rupiah(r.sisa_pokok)"></td>
                                    <td class="px-4 py-3 text-center text-gray-600">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="formatDate(r.tanggal_bayar)"></span>
                                        </span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!riwayatData.riwayat || riwayatData.riwayat.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="font-medium">Belum ada pembayaran angsuran</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div x-show="loadingRiwayat" class="text-center py-12">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <p class="text-sm text-gray-500 mt-3">Memuat riwayat pembayaran...</p>
        </div>
    </div>

    {{-- ==================== MODAL TOLAK ==================== --}}
    <div x-show="showTolakModal" x-cloak
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click.self="showTolakModal = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h4 class="font-semibold text-gray-900 text-lg mb-2">Tolak Pembiayaan</h4>
            <p class="text-sm text-gray-500 mb-4">
                Anda akan menolak pembiayaan: <strong x-text="tolakData?.kode"></strong> - <span x-text="tolakData?.user?.name"></span>
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan (opsional)</label>
                <textarea x-model="alasanTolak" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button @click="showTolakModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium">
                    Batal
                </button>
                <button @click="confirmTolak()"
                        class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition font-medium">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MODAL SUCCESS ==================== --}}
    <div x-show="showSuccess" x-cloak x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click.self="showSuccess = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h4 class="font-semibold text-gray-900 text-lg mb-2">Berhasil!</h4>
            <p class="text-sm text-gray-500 mb-6" x-text="successMessage"></p>
            <button @click="showSuccess = false; refreshData()"
                    class="px-6 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium">
                Tutup
            </button>
        </div>
    </div>

    {{-- ==================== MODAL ERROR ==================== --}}
    <div x-show="showError" x-cloak x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click.self="showError = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h4 class="font-semibold text-gray-900 text-lg mb-2">Gagal!</h4>
            <p class="text-sm text-gray-500 mb-6" x-text="errorMessage"></p>
            <button @click="showError = false"
                    class="px-6 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium">
                Tutup
            </button>
        </div>
    </div>

    {{-- ==================== ALPINE JS APP ==================== --}}
    <script>
    function pembiayaanApp() {
        return {
            subTab: 'daftar',
            selectedId: null,

            // Data
            pembiayaanList: { data: [], current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null },
            stats: {
                pembiayaan_aktif: 0, pengajuan_baru: 0, npf: 0,
                angsuran_hari_ini: 0, total_disetujui: 0, total_ditolak: 0,
                total_berjalan: 0, total_lunas: 0
            },
            filters: { search: '', status: '', akad: '' },

            // Detail
            loadingDetail: false,
            detail: { pembiayaan: null, statusLabel: '', akadLabel: '', validasiDpsLabel: '', detailData: {}, totalAngsuran: 0, sudahDibayar: 0, sisaAngsuran: 0, progressPct: 0 },

            // Angsuran
            loadingAngsuran: false,
            angsuranData: { pembiayaan: null, angsuranList: [], totalAngsuran: 0, sudahDibayar: 0, belumDibayar: 0, totalTerlambat: 0 },

            // Riwayat
            loadingRiwayat: false,
            riwayatData: { pembiayaan: null, riwayat: [], totalRiwayat: 0, totalTelahDibayar: 0 },

            // Modal
            showTolakModal: false,
            showSuccess: false,
            showError: false,
            successMessage: '',
            errorMessage: '',
            tolakData: null,
            alasanTolak: '',

            // Helpers
            rupiah(v) {
                if (!v || isNaN(v)) return 'Rp 0';
                return 'Rp ' + Number(v).toLocaleString('id-ID');
            },
            formatDate(d) {
                if (!d) return '-';
                const date = new Date(d);
                if (isNaN(date.getTime())) {
                    // Try parsing YYYY-MM-DD
                    const parts = d.split('-');
                    if (parts.length === 3) {
                        return parts[2] + '/' + parts[1] + '/' + parts[0];
                    }
                    return d;
                }
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            },
            statusLabel(s) {
                const labels = {
                    'diajukan': 'Diajukan',
                    'disetujui': 'Disetujui',
                    'ditolak': 'Ditolak',
                    'berjalan': 'Berjalan',
                    'lunas': 'Lunas'
                };
                return labels[s] || s;
            },
            validasiDpsLabel(s) {
                const labels = {
                    menunggu: 'Menunggu Validasi DPS',
                    sesuai: 'Sesuai Syariah',
                    perlu_perbaikan: 'Perlu Perbaikan',
                    tidak_sesuai: 'Tidak Sesuai Syariah'
                };
                return labels[s || 'menunggu'] || labels.menunggu;
            },
            validasiDpsClass(s) {
                const classes = {
                    menunggu: 'bg-gray-100 text-gray-600',
                    sesuai: 'bg-emerald-100 text-emerald-700',
                    perlu_perbaikan: 'bg-amber-100 text-amber-700',
                    tidak_sesuai: 'bg-red-100 text-red-700'
                };
                return classes[s || 'menunggu'] || classes.menunggu;
            },

            // Methods
            init() {
                this.loadData();
            },

            async loadData(page = null) {
                const params = new URLSearchParams();
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.akad) params.append('akad', this.filters.akad);
                if (page) params.append('page', page);

                try {
                    const res = await fetch(`{{ route('pengurus.pembiayaan.index') }}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.pembiayaanList = data.pembiayaanList;
                    this.stats = data.stats;
                } catch (e) {
                    this.errorMessage = 'Gagal memuat data: ' + e.message;
                    this.showError = true;
                }
            },

            refreshData() {
                this.loadData();
            },

            async loadDetail() {
                if (!this.selectedId) return;
                this.loadingDetail = true;
                try {
                    const res = await fetch(`{{ url('pengurus/pembiayaan') }}/${this.selectedId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.detail = await res.json();
                } catch (e) {
                    this.errorMessage = 'Gagal memuat detail: ' + e.message;
                    this.showError = true;
                } finally {
                    this.loadingDetail = false;
                }
            },

            async loadAngsuran() {
                if (!this.selectedId) return;
                this.loadingAngsuran = true;
                try {
                    const res = await fetch(`{{ url('pengurus/pembiayaan') }}/${this.selectedId}/angsuran`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.angsuranData = await res.json();
                } catch (e) {
                    this.errorMessage = 'Gagal memuat angsuran: ' + e.message;
                    this.showError = true;
                } finally {
                    this.loadingAngsuran = false;
                }
            },

            async loadRiwayat() {
                if (!this.selectedId) return;
                this.loadingRiwayat = true;
                try {
                    const res = await fetch(`{{ url('pengurus/pembiayaan') }}/${this.selectedId}/riwayat-bayar`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.riwayatData = await res.json();
                } catch (e) {
                    this.errorMessage = 'Gagal memuat riwayat: ' + e.message;
                    this.showError = true;
                } finally {
                    this.loadingRiwayat = false;
                }
            },

            async approvePembiayaan(id) {
                if (!confirm('Setujui pembiayaan ini? Jadwal angsuran akan dibuat otomatis.')) return;
                try {
                    const res = await fetch(`{{ url('pengurus/pembiayaan') }}/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.successMessage = data.message;
                        this.showSuccess = true;
                        this.loadData();
                        if (this.selectedId) this.loadDetail();
                    } else {
                        this.errorMessage = data.message;
                        this.showError = true;
                    }
                } catch (e) {
                    this.errorMessage = 'Gagal approve: ' + e.message;
                    this.showError = true;
                }
            },

            openTolak(p) {
                this.tolakData = p;
                this.alasanTolak = '';
                this.showTolakModal = true;
            },

            async confirmTolak() {
                if (!this.tolakData) return;
                try {
                    const formData = new FormData();
                    if (this.alasanTolak) formData.append('alasan_tolak', this.alasanTolak);

                    const res = await fetch(`{{ url('pengurus/pembiayaan') }}/${this.tolakData.id}/tolak`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    const data = await res.json();
                    this.showTolakModal = false;
                    if (data.success) {
                        this.successMessage = data.message;
                        this.showSuccess = true;
                        this.loadData();
                        if (this.selectedId) this.loadDetail();
                    } else {
                        this.errorMessage = data.message;
                        this.showError = true;
                    }
                } catch (e) {
                    this.showTolakModal = false;
                    this.errorMessage = 'Gagal tolak: ' + e.message;
                    this.showError = true;
                }
            }
        };
    }
    </script>
</section>

