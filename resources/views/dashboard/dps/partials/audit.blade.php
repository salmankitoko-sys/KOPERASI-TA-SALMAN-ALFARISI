{{-- ========== PANEL: AUDIT & VALIDASI AKAD ========== --}}
<div x-show="activeTab === 'audit'" x-cloak
     x-data="auditDps()"
     x-init="loadAudit()"
     class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h4 class="font-bold text-gray-900">Audit & Validasi Akad</h4>
                <p class="text-sm text-gray-500 mt-0.5">Periksa kepatuhan syariah akad sebelum keputusan operasional pengurus.</p>
            </div>
            <button @click="loadAudit()"
                    class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium whitespace-nowrap shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap gap-3 mb-4">
            <input type="text" x-model="filter.search" @keyup.enter="loadAudit()" placeholder="Cari kode / anggota..."
                   class="flex-1 min-w-[180px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <select x-model="filter.status" @change="loadAudit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Semua Status</option>
                <option value="diajukan">Diajukan</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
                <option value="berjalan">Berjalan</option>
                <option value="lunas">Lunas</option>
            </select>
            <select x-model="filter.akad" @change="loadAudit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Semua Akad</option>
                <option value="murabahah">Murabahah</option>
                <option value="mudharabah">Mudharabah</option>
                <option value="musyarakah">Musyarakah</option>
                <option value="ijarah">Ijarah</option>
            </select>
            <select x-model="filter.validasi_dps" @change="loadAudit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Semua Validasi</option>
                <option value="menunggu">Menunggu</option>
                <option value="sesuai">Sesuai Syariah</option>
                <option value="perlu_perbaikan">Perlu Perbaikan</option>
                <option value="tidak_sesuai">Tidak Sesuai</option>
            </select>
            <button @click="loadAudit()" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium">
                Cari
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-3">Memuat daftar akad...</p>
        </div>

        {{-- Tabel --}}
        <div x-show="!loading" class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full min-w-max text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left whitespace-nowrap">Kode</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Anggota</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Akad</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Pembiayaan</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Angsuran/Bulan</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Tenor</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Validasi DPS</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="p in list" :key="p.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap" x-text="p.kode"></td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap" x-text="p.user?.name || '-'"></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold bg-indigo-100 text-indigo-700"
                                      x-text="akadLabel(p.akad)"></span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 whitespace-nowrap" x-text="rupiah(p.jumlah_pembiayaan)"></td>
                            <td class="px-4 py-3 text-right text-gray-600 whitespace-nowrap" x-text="rupiah(p.angsuran_bulanan)"></td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="p.tenor + ' bln'"></td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="statusClass(p.status)"
                                      x-text="statusLabel(p.status)"></span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="validasiClass(p.status_validasi_dps)"
                                      x-text="validasiLabel(p.status_validasi_dps)"></span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button @click="openDetail(p.id)"
                                        :class="['ditolak', 'lunas'].includes(p.status)
                                            ? 'text-indigo-700 bg-indigo-50 border-indigo-200 hover:bg-indigo-100'
                                            : 'text-white bg-emerald-600 border-emerald-600 hover:bg-emerald-700'"
                                        class="px-3 py-1.5 text-[10px] font-bold border rounded-lg transition">
                                    <span x-text="['ditolak', 'lunas'].includes(p.status)
                                        ? 'Lihat Hasil'
                                        : (p.status_validasi_dps === 'menunggu' ? 'Validasi' : 'Validasi Ulang')"></span>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!list || list.length === 0">
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <p class="font-medium">Belum ada data akad.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="!loading && pagination.last_page > 1"
             class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-100 pt-4 mt-4">
            <p class="text-sm text-gray-600" x-text="'Menampilkan ' + pagination.from + ' - ' + pagination.to + ' dari ' + pagination.total"></p>
            <div class="flex gap-2">
                <button @click="loadAudit(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                        :class="pagination.prev_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Sebelumnya
                </button>
                <button @click="loadAudit(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                        :class="pagination.next_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div x-show="detail.id"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="overscroll-behavior: contain;">
        <div class="fixed inset-0 bg-black/40" @click="detail.id = null"></div>

        <div class="relative z-10 w-full max-w-4xl mx-auto my-4 sm:my-6 bg-white rounded-2xl shadow-xl"
             style="max-height: calc(100vh - 2rem); overflow-y: auto; overscroll-behavior: contain; scrollbar-gutter: stable;">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <div>
                    <h4 class="font-bold text-gray-900" x-text="detail.pembiayaan?.kode || 'Detail Akad'"></h4>
                    <p class="text-xs text-gray-500" x-text="'Status: ' + (detail.pembiayaan ? statusLabel(detail.pembiayaan.status) : '')"></p>
                </div>
                <div class="flex items-center gap-2">
                    <span x-show="detail.validasiMasihBerlaku === false && detail.validasiTerbaru"
                          class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                        ⚠ Data berubah sejak validasi terakhir
                    </span>
                    <button @click="detail.id = null" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500" aria-label="Tutup detail akad">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <template x-if="detail.loading">
                    <div class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </template>

                <template x-if="!detail.loading && detail.pembiayaan">

                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <label class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-500">Navigasi konten</label>
                            <select x-model="auditSection" class="w-full sm:w-56 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <option value="ringkasan">Ringkasan</option>
                                <option value="review">Review Akad</option>
                                <option value="dokumen">Dokumen</option>
                                <option value="pembayaran">Pembayaran</option>
                                <option value="pemeriksaan">Pemeriksaan</option>
                                <option value="riwayat">Riwayat Validasi</option>
                            </select>
                        </div>

                        {{-- Ringkasan review DPS --}}
                        <div x-show="auditSection === 'ringkasan'">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ringkasan Review DPS</p>
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold"
                                      :class="validasiClass(detail.pembiayaan.status_validasi_dps)"
                                      x-text="validasiLabel(detail.pembiayaan.status_validasi_dps)"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">
                                <template x-for="item in detail.ringkasanReview" :key="item.label">
                                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                        <p class="text-[10px] text-gray-500" x-text="item.label"></p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900" x-text="formatReviewValue(item)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Info umum --}}
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Umum</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Anggota</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="detail.pembiayaan.user?.name || '-'"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Jenis Akad</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="akadLabel(detail.pembiayaan.akad)"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Jumlah Pembiayaan</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="rupiah(detail.pembiayaan.jumlah_pembiayaan)"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Angsuran / Bulan</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="rupiah(detail.pembiayaan.angsuran_bulanan)"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Tenor</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="detail.pembiayaan.tenor + ' bulan'"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Objek Pembiayaan</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="detail.pembiayaan.objek_pembiayaan || '-'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Riwayat margin/nisbah --}}
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Riwayat Margin / Nisbah</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="(value, key) in detail.riwayatAkad" :key="key">
                                    <div class="rounded-lg bg-gray-50 p-3 flex items-center justify-between">
                                        <p class="text-xs text-gray-500" x-text="key"></p>
                                        <p class="text-sm font-semibold text-gray-900" x-text="formatAkadValue(key, value)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Profil anggota dan tujuan pembiayaan --}}
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Profil Anggota & Tujuan</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Kontak Anggota</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="detail.pembiayaan.user?.email || '-'"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="detail.pembiayaan.user?.no_hp || '-'"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Pekerjaan / Penghasilan</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="detail.pembiayaan.user?.pekerjaan || '-'"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="rupiah(detail.pembiayaan.user?.penghasilan)"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Tujuan Pembiayaan</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="label(detail.pembiayaan.tujuan_pembiayaan)"></p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Estimasi Omzet Usaha</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="rupiah(detail.pembiayaan.estimasi_omzet_usaha)"></p>
                                </div>
                                <div class="sm:col-span-2 rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] text-gray-500">Rencana Penggunaan Dana</p>
                                    <p class="text-sm text-gray-800 whitespace-pre-line" x-text="detail.pembiayaan.rencana_penggunaan_dana || '-'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Detail review sesuai pengajuan anggota --}}
                        <div x-show="auditSection === 'review' && detail.reviewAkad.length">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Detail Review Akad</p>
                            <div class="space-y-3">
                                <template x-for="section in detail.reviewAkad" :key="section.title">
                                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                                        <div class="mb-3">
                                            <p class="text-sm font-bold text-gray-900" x-text="section.title"></p>
                                            <p class="text-xs text-gray-500 mt-0.5" x-text="section.description"></p>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <template x-for="item in section.items" :key="section.title + '-' + item.label">
                                                <div class="rounded-lg bg-gray-50 p-3" :class="item.type === 'longtext' ? 'sm:col-span-2' : ''">
                                                    <p class="text-[10px] text-gray-500" x-text="item.label"></p>
                                                    <p class="mt-1 text-sm font-semibold text-gray-900 whitespace-pre-line" x-text="formatReviewValue(item)"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Dokumen --}}
                        <div x-show="auditSection === 'dokumen'">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen Kelengkapan</p>
                            <div class="space-y-2" x-show="detail.pembiayaan?.dokumen && detail.pembiayaan.dokumen.length">
                                <template x-for="doc in detail.pembiayaan.dokumen" :key="doc.id">
                                    <a :href="doc.url" target="_blank" rel="noopener" class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 p-3 hover:bg-indigo-50 transition">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="doc.jenis_label"></p>
                                            <p class="text-xs text-gray-500 truncate" x-text="doc.nama_asli"></p>
                                        </div>
                                        <span class="text-xs font-bold text-indigo-700 shrink-0" x-text="doc.ukuran_label"></span>
                                    </a>
                                </template>
                            </div>
                            <p x-show="!detail.pembiayaan?.dokumen || !detail.pembiayaan.dokumen.length" class="text-sm text-gray-500">Belum ada dokumen yang diunggah.</p>
                        </div>

                        {{-- Jadwal Angsuran --}}
                        <div x-show="auditSection === 'pembayaran' && detail.pembiayaan?.angsuran && detail.pembiayaan.angsuran.length">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Pembayaran</p>
                                <span class="text-[10px] text-gray-500" x-text="detail.pembiayaan.angsuran.length + ' baris'"></span>
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-gray-100">
                                <table class="w-full min-w-max text-xs">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                                            <th class="px-3 py-2 text-center">Bulan</th>
                                            <th class="px-3 py-2 text-left">Jatuh Tempo</th>
                                            <th class="px-3 py-2 text-right">Pokok</th>
                                            <th class="px-3 py-2 text-right">Margin</th>
                                            <th class="px-3 py-2 text-right">Jumlah Bayar</th>
                                            <th class="px-3 py-2 text-right">Sisa Pokok</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="row in detail.pembiayaan.angsuran" :key="row.id || row.bulan_ke">
                                            <tr>
                                                <td class="px-3 py-2 text-center font-semibold text-gray-900" x-text="row.bulan_ke"></td>
                                                <td class="px-3 py-2 text-gray-600" x-text="tanggalPendek(row.jatuh_tempo)"></td>
                                                <td class="px-3 py-2 text-right text-gray-700" x-text="rupiah(row.pokok)"></td>
                                                <td class="px-3 py-2 text-right text-gray-700" x-text="rupiah(row.margin)"></td>
                                                <td class="px-3 py-2 text-right font-semibold text-gray-900" x-text="rupiah(row.jumlah_bayar)"></td>
                                                <td class="px-3 py-2 text-right text-gray-600" x-text="rupiah(row.sisa_pokok)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Pemeriksaan otomatis --}}
                        <div x-show="auditSection === 'pemeriksaan'">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pemeriksaan Data Sistem</p>
                                <span class="text-[10px] text-gray-500">Bukan pengganti penilaian DPS</span>
                            </div>
                            <div class="space-y-2">
                                <template x-for="check in detail.pemeriksaanSistem" :key="check.kode">
                                    <div class="rounded-lg border p-3"
                                         :class="check.status === 'lulus' ? 'border-emerald-100 bg-emerald-50' : 'border-red-200 bg-red-50'">
                                        <div class="flex items-start gap-2">
                                            <span class="mt-0.5 text-xs font-bold"
                                                  :class="check.status === 'lulus' ? 'text-emerald-700' : 'text-red-700'"
                                                  x-text="check.status === 'lulus' ? '✓' : '!'"></span>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-900" x-text="check.label"></p>
                                                <p class="text-[11px] mt-0.5"
                                                   :class="check.status === 'lulus' ? 'text-emerald-700' : 'text-red-700'"
                                                   x-text="check.pesan"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Referensi fatwa --}}
                        <div x-show="auditSection === 'pemeriksaan'" class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                            <p class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Rujukan Pemeriksaan</p>
                            <ul class="mt-2 space-y-1 text-xs text-indigo-800 list-disc pl-4">
                                <template x-for="reference in detail.referensiFatwa" :key="reference">
                                    <li x-text="reference"></li>
                                </template>
                            </ul>
                            <a href="https://dsnmui.or.id/kategori/fatwa/" target="_blank" rel="noopener"
                               class="inline-flex mt-3 text-xs font-bold text-indigo-700 hover:underline">
                                Buka katalog Fatwa DSN-MUI
                            </a>
                        </div>

                        {{-- Perubahan Data (Snapshot Comparison) --}}
                        <div x-show="detail.validasiTerbaru && !detail.validasiMasihBerlaku"
                             class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-amber-600 font-bold">⚠</span>
                                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Perubahan Data Sejak Validasi Terakhir</p>
                            </div>
                            <p class="text-xs text-amber-800 mb-3">
                                Data pembiayaan telah berubah sejak validasi terakhir (v<span x-text="detail.validasiTerbaru?.versi"></span>).
                                Pemeriksaan ulang diperlukan sebelum validasi dapat diperbarui.
                            </p>
                            <button @click="loadSnapshotComparison()"
                                    class="px-3 py-1.5 text-xs font-bold text-amber-700 bg-white border border-amber-300 rounded-lg hover:bg-amber-100 transition">
                                Lihat Perubahan
                            </button>

                            <div x-show="snapshotComparison.loading" class="mt-3 text-xs text-amber-700">Memuat perbandingan...</div>
                            <div x-show="snapshotComparison.diff && snapshotComparison.diff.length > 0" class="mt-3 space-y-2">
                                <template x-for="d in snapshotComparison.diff" :key="d.section + (d.field || '')">
                                    <div class="rounded-lg bg-white p-3 border border-amber-200">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                                  :class="d.type === 'added' ? 'bg-emerald-100 text-emerald-700' : d.type === 'removed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'"
                                                  x-text="d.type === 'added' ? 'Tambah' : d.type === 'removed' ? 'Hapus' : 'Ubah'"></span>
                                            <span class="text-xs font-semibold text-gray-900" x-text="d.section + (d.field ? '.' + d.field : '')"></span>
                                        </div>
                                        <template x-if="d.type === 'changed' && d.old !== undefined">
                                            <div class="mt-1 text-[11px] text-gray-600">
                                                <span class="text-red-600 line-through" x-text="formatSnapshotValue(d.old)"></span>
                                                <span class="text-gray-400 mx-1">→</span>
                                                <span class="text-emerald-600 font-semibold" x-text="formatSnapshotValue(d.new)"></span>
                                            </div>
                                        </template>
                                        <template x-if="d.message">
                                            <p class="mt-1 text-[11px] text-gray-600" x-text="d.message"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Riwayat Validasi DPS --}}
                        <div x-show="auditSection === 'riwayat' && detail.riwayatValidasi && detail.riwayatValidasi.length > 0">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Riwayat Validasi DPS</p>
                            <div class="space-y-3">
                                <template x-for="v in detail.riwayatValidasi" :key="v.id">
                                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                                        <div class="flex items-center justify-between gap-3 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900" x-text="'v' + v.versi"></span>
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                                      :class="validasiClass(v.hasil)"
                                                      x-text="validasiLabel(v.hasil)"></span>
                                            </div>
                                            <span class="text-[10px] text-gray-500" x-text="formatDateTime(v.divalidasi_pada)"></span>
                                        </div>
                                        <p class="text-xs text-gray-700 mb-2" x-text="v.kesimpulan"></p>
                                        <div class="flex items-center gap-4 text-[10px] text-gray-500">
                                            <span x-text="(v.jumlah_sesuai || 0) + '/' + (v.jumlah_kriteria || 0) + ' kriteria sesuai'"></span>
                                            <span x-text="'Oleh: ' + (v.validator?.name || '-')"></span>
                                        </div>
                                        <div x-show="v.catatan_perbaikan" class="mt-2 rounded-lg bg-amber-50 p-2 border border-amber-100">
                                            <p class="text-[10px] font-bold text-amber-700">Catatan Perbaikan:</p>
                                            <p class="text-[11px] text-amber-800" x-text="v.catatan_perbaikan"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Formulir Validasi DPS --}}
                        <div x-show="canValidate()" class="border-t border-gray-200 pt-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Formulir Validasi DPS</p>
                                    <p class="text-[11px] text-gray-500">Periksa setiap kriteria syariah dan tentukan hasil validasi.</p>
                                </div>
                            </div>

                            {{-- Blocking warnings --}}
                            <div x-show="detail.pemeriksaanSistem && detail.pemeriksaanSistem.some(c => c.status === 'gagal')"
                                 class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="text-xs font-bold text-red-700 mb-2">⚠ Pemeriksaan Data Sistem Gagal</p>
                                <p class="text-[11px] text-red-600 mb-2">Beberapa pemeriksaan otomatis tidak terpenuhi. Validasi "Sesuai Syariah" tidak dapat diberikan sampai data diperbaiki.</p>
                                <template x-for="check in detail.pemeriksaanSistem.filter(c => c.status === 'gagal')" :key="check.kode">
                                    <div class="text-[11px] text-red-700 ml-2">• <span x-text="check.label"></span>: <span x-text="check.pesan"></span></div>
                                </template>
                            </div>

                            {{-- Checklist items --}}
                            <div class="space-y-3 mb-6">
                                <template x-for="(item, idx) in detail.checklist" :key="item.kode">
                                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded" x-text="item.kelompok"></span>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-900" x-text="item.label"></p>
                                                <p class="text-[11px] text-gray-500 mt-0.5" x-text="item.panduan"></p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <button @click="setChecklistStatus(idx, 'ya')"
                                                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg border transition"
                                                    :class="checklistForm[idx]?.status === 'ya' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                                                ✓ Sesuai
                                            </button>
                                            <button @click="setChecklistStatus(idx, 'tidak')"
                                                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg border transition"
                                                    :class="checklistForm[idx]?.status === 'tidak' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                                                ✗ Tidak Sesuai
                                            </button>
                                            <button @click="setChecklistStatus(idx, 'catatan')"
                                                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg border transition"
                                                    :class="checklistForm[idx]?.status === 'catatan' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                                                ~ Catatan
                                            </button>
                                        </div>
                                        <div x-show="checklistForm[idx]?.status === 'catatan' || checklistForm[idx]?.status === 'tidak'" class="mt-2">
                                            <input type="text" x-model="checklistForm[idx].catatan"
                                                   placeholder="Catatan untuk kriteria ini..."
                                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Kesimpulan & Hasil --}}
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-5 space-y-4">
                                <p class="text-sm font-bold text-gray-900">Kesimpulan Validasi</p>

                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Hasil Validasi *</label>
                                    <select x-model="validationForm.hasil" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <option value="sesuai">✓ Sesuai Syariah</option>
                                        <option value="perlu_perbaikan">~ Perlu Perbaikan</option>
                                        <option value="tidak_sesuai">✗ Tidak Sesuai Syariah</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Kesimpulan / Catatan Utama *</label>
                                    <textarea x-model="validationForm.kesimpulan" rows="3"
                                              placeholder="Tuliskan kesimpulan pemeriksaan kepatuhan syariah..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                </div>

                                <div x-show="validationForm.hasil === 'perlu_perbaikan' || validationForm.hasil === 'tidak_sesuai'">
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Catatan Perbaikan</label>
                                    <textarea x-model="validationForm.catatan_perbaikan" rows="2"
                                              placeholder="Jelaskan apa yang perlu diperbaiki..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Referensi Tambahan (opsional)</label>
                                    <input type="text" x-model="validationForm.referensi_tambahan"
                                           placeholder="Fatwa atau referensi tambahan..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>

                                {{-- Checklist summary --}}
                                <div class="flex items-center gap-4 text-xs text-gray-600">
                                    <span class="font-semibold" x-text="checklistSummary().sesuai + ' sesuai'"></span>
                                    <span class="font-semibold" x-text="checklistSummary().tidak + ' tidak sesuai'"></span>
                                    <span class="font-semibold" x-text="checklistSummary().catatan + ' catatan'"></span>
                                    <span class="text-gray-400" x-text="'dari ' + detail.checklist.length + ' kriteria'"></span>
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button @click="submitValidation()"
                                            :disabled="validationForm.saving || !validationForm.kesimpulan"
                                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition disabled:opacity-60">
                                        <span x-show="!validationForm.saving">Simpan Validasi</span>
                                        <span x-show="validationForm.saving">Menyimpan...</span>
                                    </button>
                                    <button @click="validationForm.saving = false"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>
    </div>

    <style>
        /* Scroll vertikal untuk modal detail Audit & Validasi Akad */
        [x-show="detail.id"] > .relative.z-10 {
            scrollbar-width: thin;
            scrollbar-color: #9ca3af #f3f4f6;
        }

        [x-show="detail.id"] > .relative.z-10::-webkit-scrollbar {
            width: 10px;
        }

        [x-show="detail.id"] > .relative.z-10::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 9999px;
        }

        [x-show="detail.id"] > .relative.z-10::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 9999px;
            border: 2px solid #f3f4f6;
        }

        [x-show="detail.id"] > .relative.z-10::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>

    <script>
        function auditDps() {
            return {
                loading: false,
                list: [],
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null },
                filter: { search: '', status: '', akad: '', validasi_dps: '' },
                auditSection: 'ringkasan',
                detail: {
                    id: null,
                    loading: false,
                    pembiayaan: null,
                    riwayatAkad: {},
                    reviewAkad: [],
                    ringkasanReview: [],
                    checklist: [],
                    referensiFatwa: [],
                    pemeriksaanSistem: [],
                    validasiTerbaru: null,
                    validasiMasihBerlaku: false,
                    riwayatValidasi: [],
                },
                checklistForm: [],
                validationForm: { hasil: 'sesuai', kesimpulan: '', catatan_perbaikan: '', referensi_tambahan: '', saving: false },
                snapshotComparison: { loading: false, diff: null, has_changes: false },

                rupiah(v) {
                    if (!v && v !== 0) return 'Rp 0';
                    if (isNaN(v)) return 'Rp 0';
                    return 'Rp ' + Number(v).toLocaleString('id-ID');
                },
                statusLabel(s) {
                    const map = { diajukan: 'Diajukan', disetujui: 'Disetujui', ditolak: 'Ditolak', berjalan: 'Berjalan', lunas: 'Lunas' };
                    return map[s] || s;
                },
                statusClass(s) {
                    const map = {
                        diajukan: 'bg-yellow-100 text-yellow-700',
                        disetujui: 'bg-blue-100 text-blue-700',
                        ditolak: 'bg-red-100 text-red-700',
                        berjalan: 'bg-emerald-100 text-emerald-700',
                        lunas: 'bg-gray-100 text-gray-600',
                    };
                    return map[s] || 'bg-gray-100 text-gray-600';
                },
                akadLabel(a) {
                    const map = { murabahah: 'Murabahah', mudharabah: 'Mudharabah', musyarakah: 'Musyarakah', ijarah: 'Ijarah', qardh: 'Qardh' };
                    return map[a] || a;
                },
                label(value) {
                    if (!value) return '-';
                    return String(value).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
                },
                validasiLabel(s) {
                    const map = { menunggu: 'Menunggu', sesuai: 'Sesuai Syariah', perlu_perbaikan: 'Perlu Perbaikan', tidak_sesuai: 'Tidak Sesuai' };
                    return map[s || 'menunggu'] || s;
                },
                validasiClass(s) {
                    const map = {
                        menunggu: 'bg-gray-100 text-gray-600',
                        sesuai: 'bg-emerald-100 text-emerald-700',
                        perlu_perbaikan: 'bg-amber-100 text-amber-700',
                        tidak_sesuai: 'bg-red-100 text-red-700',
                    };
                    return map[s || 'menunggu'] || map.menunggu;
                },
                formatDateTime(value) {
                    if (!value) return '-';
                    return new Date(value).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                },
                formatAkadValue(key, value) {
                    if (value === null || value === undefined || value === '') return '-';
                    if (String(key).includes('(%)') || String(key).startsWith('Porsi Modal')) return Number(value).toLocaleString('id-ID') + '%';
                    return isNaN(value) ? value : this.rupiah(value);
                },
                formatReviewValue(item) {
                    if (!item || item.value === null || item.value === undefined || item.value === '') return '-';
                    if (item.type === 'money') return this.rupiah(item.value);
                    if (item.type === 'percent') return Number(item.value || 0).toLocaleString('id-ID') + '%';
                    if (item.type === 'date') return this.tanggalPendek(item.value);
                    if (item.type === 'status') return this.statusLabel(item.value);
                    if (item.type === 'validasi') return this.validasiLabel(item.value);
                    if (item.type === 'label') return this.label(item.value);
                    if (item.type === 'number') return Number(item.value || 0).toLocaleString('id-ID');
                    return String(item.value);
                },
                formatSnapshotValue(v) {
                    if (v === null || v === undefined) return '-';
                    if (typeof v === 'object') return JSON.stringify(v);
                    return String(v);
                },
                tanggalPendek(value) {
                    if (!value) return '-';
                    return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                },

                canValidate() {
                    const p = this.detail.pembiayaan;
                    return p && !['ditolak', 'lunas'].includes(p.status);
                },

                setChecklistStatus(idx, status) {
                    if (!this.checklistForm[idx]) {
                        this.checklistForm[idx] = { kode: this.detail.checklist[idx]?.kode, status: status, catatan: '' };
                    } else {
                        this.checklistForm[idx].status = status;
                    }
                },

                checklistSummary() {
                    const form = this.checklistForm.filter(c => c);
                    return {
                        sesuai: form.filter(c => c.status === 'ya').length,
                        tidak: form.filter(c => c.status === 'tidak').length,
                        catatan: form.filter(c => c.status === 'catatan').length,
                    };
                },

                async loadAudit(page = null) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (page) params.append('page', page);
                        if (this.filter.search) params.append('search', this.filter.search);
                        if (this.filter.status) params.append('status', this.filter.status);
                        if (this.filter.akad) params.append('akad', this.filter.akad);
                        if (this.filter.validasi_dps) params.append('validasi_dps', this.filter.validasi_dps);
                        const res = await fetch('{{ route("dps.audit.index") }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.list = data.data || [];
                        this.pagination = data.pagination || this.pagination;
                    } catch (e) {
                        console.error('Gagal memuat daftar akad:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async openDetail(id) {
                    this.detail = {
                        id,
                        loading: true,
                        pembiayaan: null,
                        riwayatAkad: {},
                        reviewAkad: [],
                        ringkasanReview: [],
                        checklist: [],
                        referensiFatwa: [],
                        pemeriksaanSistem: [],
                        validasiTerbaru: null,
                        validasiMasihBerlaku: false,
                        riwayatValidasi: [],
                    };
                    this.checklistForm = [];
                    this.validationForm = { hasil: 'sesuai', kesimpulan: '', catatan_perbaikan: '', referensi_tambahan: '', saving: false };
                    this.snapshotComparison = { loading: false, diff: null, has_changes: false };
                    this.auditSection = 'ringkasan';
                    try {
                        const res = await fetch('{{ route("dps.audit.show", ":id") }}'.replace(':id', id), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.detail.pembiayaan = data.pembiayaan || null;
                        this.detail.riwayatAkad = data.riwayatAkad || {};
                        this.detail.reviewAkad = data.reviewAkad || [];
                        this.detail.ringkasanReview = data.ringkasanReview || [];
                        this.detail.checklist = data.checklist || [];
                        this.detail.referensiFatwa = data.referensiFatwa || [];
                        this.detail.pemeriksaanSistem = data.pemeriksaanSistem || [];
                        this.detail.validasiTerbaru = data.validasiTerbaru || null;
                        this.detail.validasiMasihBerlaku = Boolean(data.validasiMasihBerlaku);
                        this.detail.riwayatValidasi = data.riwayatValidasi || [];

                        // Initialize checklist form
                        this.checklistForm = this.detail.checklist.map(item => ({
                            kode: item.kode,
                            status: 'ya',
                            catatan: '',
                        }));

                        // Pre-fill from last validation if exists
                        if (this.detail.validasiTerbaru && this.detail.validasiTerbaru.checklist) {
                            const lastChecklist = this.detail.validasiTerbaru.checklist;
                            this.checklistForm = this.detail.checklist.map(item => {
                                const prev = lastChecklist.find(c => c.kode === item.kode);
                                return {
                                    kode: item.kode,
                                    status: prev?.status || 'ya',
                                    catatan: prev?.catatan || '',
                                };
                            });
                        }
                    } catch (e) {
                        console.error('Gagal memuat detail akad:', e);
                    } finally {
                        this.detail.loading = false;
                    }
                },

                async loadSnapshotComparison() {
                    this.snapshotComparison = { loading: true, diff: null, has_changes: false };
                    try {
                        const res = await fetch('{{ route("dps.audit.compare", ":id") }}'.replace(':id', this.detail.id), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.snapshotComparison = {
                            loading: false,
                            has_changes: data.has_changes || false,
                            diff: data.diff || [],
                        };
                    } catch (e) {
                        console.error('Gagal memuat perbandingan snapshot:', e);
                        this.snapshotComparison.loading = false;
                    }
                },

                async submitValidation() {
                    if (!this.validationForm.kesimpulan) {
                        alert('Kesimpulan wajib diisi.');
                        return;
                    }

                    const filledChecklist = this.checklistForm.filter(c => c && c.status);
                    if (filledChecklist.length === 0) {
                        alert('Harap isi minimal satu kriteria checklist.');
                        return;
                    }

                    this.validationForm.saving = true;
                    try {
                        const res = await fetch('{{ route("dps.audit.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') ?? '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                pembiayaan_id: this.detail.id,
                                hasil: this.validationForm.hasil,
                                checklist: filledChecklist,
                                kesimpulan: this.validationForm.kesimpulan,
                                catatan_perbaikan: this.validationForm.catatan_perbaikan || null,
                                referensi_tambahan: this.validationForm.referensi_tambahan || null,
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert(data.message);
                            this.detail.id = null;
                            this.loadAudit();
                        } else {
                            alert(data.message || 'Gagal menyimpan validasi.');
                            if (data.errors) {
                                console.error('Validation errors:', data.errors);
                            }
                        }
                    } catch (e) {
                        alert('Gagal terhubung ke server.');
                        console.error(e);
                    } finally {
                        this.validationForm.saving = false;
                    }
                },
            }
        }
    </script>
</div>