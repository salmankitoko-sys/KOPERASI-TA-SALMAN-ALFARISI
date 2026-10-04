{{-- ========== PANEL: JADWAL ANGSURAN PEMBIAYAAN ========== --}}
<div x-show="activeTab === 'angsuran'" x-cloak
     x-data="angsuranAnggota()"
     x-init="loadAngsuran()"
     class="space-y-6">

    {{-- Card Utama: Header -> Stats -> Loading -> Tabel -> Pagination --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-3">
                <div>
                    <h4 class="font-bold text-gray-900">Jadwal Angsuran Pembiayaan</h4>
                    <p class="text-sm text-gray-500 mt-0.5">Daftar angsuran pembiayaan yang harus dibayar.</p>
                </div>

            </div>
            <button @click="loadAngsuran()"
                    class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium whitespace-nowrap shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>

        {{-- Grid Statistik (4 kartu, di luar tabel) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Total Angsuran</p>
                <p class="text-xl font-bold text-gray-900" x-text="stats.total">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Sudah Dibayar</p>
                <p class="text-xl font-bold text-emerald-600" x-text="stats.lunas">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Belum Dibayar</p>
                <p class="text-xl font-bold text-amber-600" x-text="stats.belum">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Terlewat</p>
                <p class="text-xl font-bold text-red-600" x-text="stats.terlewat">0</p>
            </div>
        </div>
        {{-- /Grid Statistik --}}

        {{-- Loading Indicator (di luar grid) --}}
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-3">Memuat jadwal angsuran...</p>
        </div>

        {{-- Jadwal dikelompokkan per pengajuan agar beberapa pembiayaan tidak tercampur. --}}
        <div x-show="!loading" class="space-y-4">
            <template x-for="p in pembiayaanList" :key="p.id">
                <section class="overflow-hidden rounded-xl border border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 bg-gray-50 px-4 py-3">
                        <div>
                            <h5 class="font-bold text-gray-900" x-text="p.akad"></h5>
                            <p class="text-xs text-gray-500" x-text="'Kode pembiayaan: ' + (p.kode || '-')"></p>
                        </div>
                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold capitalize text-indigo-700" x-text="p.status"></span>
                    </div>
                    <div class="overflow-x-auto max-h-[420px]">
                        <table class="w-full min-w-max text-xs">
                            <thead>
                                <tr class="bg-white text-gray-500 font-bold uppercase tracking-wider">
                                    <th class="sticky top-0 z-10 bg-white text-left px-4 py-3 whitespace-nowrap">Angsuran Ke-</th>
                                    <th class="sticky top-0 z-10 bg-white text-right px-4 py-3 whitespace-nowrap">Jumlah Bayar</th>
                                    <th class="sticky top-0 z-10 bg-white text-right px-4 py-3 whitespace-nowrap">Pokok</th>
                                    <th class="sticky top-0 z-10 bg-white text-right px-4 py-3 whitespace-nowrap">Margin</th>
                                    <th class="sticky top-0 z-10 bg-white text-right px-4 py-3 whitespace-nowrap">Sisa Pokok</th>
                                    <th class="sticky top-0 z-10 bg-white text-center px-4 py-3 whitespace-nowrap">Jatuh Tempo</th>
                                    <th class="sticky top-0 z-10 bg-white text-center px-4 py-3 whitespace-nowrap">Status</th>
                                    <th class="sticky top-0 z-10 bg-white text-center px-4 py-3 whitespace-nowrap">Tgl Bayar</th>
                                    <th class="sticky top-0 z-10 bg-white text-center px-4 py-3 whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="a in p.angsuran" :key="a.id">
                                    <tr class="hover:bg-gray-50 transition" :class="{'bg-red-50/50': terlambat(a)}">
                                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap" x-text="'Angsuran ke-' + a.bulan_ke"></td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900 whitespace-nowrap" x-text="rupiah(a.jumlah_bayar)"></td>
                                        <td class="px-4 py-3 text-right text-gray-700 whitespace-nowrap" x-text="rupiah(a.pokok)"></td>
                                        <td class="px-4 py-3 text-right text-gray-700 whitespace-nowrap" x-text="rupiah(a.margin)"></td>
                                        <td class="px-4 py-3 text-right text-gray-600 whitespace-nowrap" x-text="rupiah(a.sisa_pokok)"></td>
                                        <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="formatDate(a.jatuh_tempo)"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold"
                                                  :class="isLunas(a) ? 'bg-emerald-100 text-emerald-700' : (terlambat(a) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')">
                                                <span x-text="isLunas(a) ? 'Lunas' : (terlambat(a) ? 'Terlewat' : 'Belum')"></span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="a.tanggal_bayar ? formatDate(a.tanggal_bayar) : '-'"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <template x-if="p.dapat_dibayar && !isLunas(a)">
                                                <a :href="'{{ route('anggota.transaksi.angsuran.create') }}?pembiayaan_id=' + encodeURIComponent(a.pembiayaan_id) + '&angsuran_id=' + encodeURIComponent(a.id)" class="inline-flex rounded-lg bg-indigo-600 px-3 py-1.5 text-[10px] font-bold text-white transition hover:bg-indigo-700">
                                                    Bayar
                                                </a>
                                            </template>
                                            <template x-if="isLunas(a)">
                                                <span class="text-[10px] text-emerald-600 font-bold whitespace-nowrap">✓ Lunas</span>
                                            </template>
                                            <template x-if="!p.dapat_dibayar && !isLunas(a)">
                                                <span class="text-[10px] font-semibold text-gray-500 whitespace-nowrap" x-text="p.status === 'disetujui' ? 'Menunggu pencairan' : 'Tidak tersedia'"></span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>
            </template>
            <div x-show="!pembiayaanList || pembiayaanList.length === 0" class="rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 12v-2M3 6h18v12H3z"/>
                </svg>
                <p class="font-medium">Belum ada jadwal angsuran.</p>
                <p class="text-xs mt-1">Angsuran akan muncul setelah pembiayaan berjalan.</p>
            </div>
        </div>
        {{-- /Jadwal per pembiayaan --}}

        {{-- Pagination (di luar tabel) --}}
        <div x-show="!loading && pagination.last_page > 1"
             class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-100 pt-4 mt-4">
            <p class="text-sm text-gray-600" x-text="'Menampilkan pembiayaan ' + pagination.from + ' - ' + pagination.to + ' dari ' + pagination.total"></p>
            <div class="flex gap-2">
                <button @click="loadAngsuran(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                        :class="pagination.prev_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Sebelumnya
                </button>
                <button @click="loadAngsuran(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                        :class="pagination.next_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Berikutnya
                </button>
            </div>
        </div>
        {{-- /Pagination --}}

    </div>
    {{-- /Card Utama --}}

    {{-- Alpine.js Component (diletakkan setelah seluruh layout selesai) --}}
    <script>
        document.addEventListener('click', (event) => {
            const paymentLink = event.target.closest('a[href*=/anggota/transaksi/angsuran]');
            if (!paymentLink) return;

            const url = new URL(paymentLink.href, window.location.origin);
            const angsuranId = url.searchParams.get('angsuran_id');
            if (!angsuranId) return;

            event.preventDefault();
            window.location.assign(`{{ url('/anggota/angsuran') }}/${angsuranId}/bayar`);
        });

        function angsuranAnggota() {
            return {
                loading: false,
                pembiayaanList: [],
                stats: { total: 0, lunas: 0, belum: 0, terlewat: 0 },
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null },

                rupiah(v) {
                    if (!v && v !== 0) return 'Rp 0';
                    if (isNaN(v)) return 'Rp 0';
                    return 'Rp ' + Number(v).toLocaleString('id-ID');
                },
                formatDate(d) {
                    if (!d) return '-';
                    const date = new Date(d);
                    if (isNaN(date.getTime())) {
                        const parts = d.split('-');
                        if (parts.length === 3) return parts[2] + '/' + parts[1] + '/' + parts[0];
                        return d;
                    }
                    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                },

                // Flag terlambat tidak dikirim controller, hitung di sisi klien.
                terlambat(a) {
                    if (!a || !a.jatuh_tempo) return false;
                    if (a.status === 'dibayar') return false;
                    const now = new Date();
                    const today = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0');
                    return a.jatuh_tempo < today;
                },
                isLunas(a) {
                    if (!a) return false;
                    return a.status === 'dibayar';
                },

                async loadAngsuran(page = null) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (page) params.append('page', page);

                        const res = await fetch('{{ route("anggota.angsuran.index") }}?ajax=1&' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();

                        this.pembiayaanList = data.pembiayaanList || [];
                        this.stats = data.stats || { total: 0, lunas: 0, belum: 0, terlewat: 0 };
                        this.pagination = data.pagination || { current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null };
                    } catch (e) {
                        console.error('Gagal memuat angsuran:', e);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</div>
