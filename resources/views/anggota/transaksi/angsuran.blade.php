<x-app-layout>
    @php
        $initialPembiayaanId = old('pembiayaan_id', $selectedPembiayaanId);
        $initialAngsuranId = old('angsuran_id', $selectedAngsuranId);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Pembayaran Angsuran</h2>
                <p class="mt-0.5 text-sm text-gray-500">Pilih tagihan dari jadwal pembiayaan. Nominal pembayaran mengikuti jadwal yang dibuat koperasi.</p>
            </div>
            <a href="{{ route('anggota.transaksi.dashboard') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Transaksi
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                @if (session('info'))
                    <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
                @endif

                @if ($pembiayaanList->isEmpty())
                    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Belum ada angsuran yang dapat dibayar. Pembayaran tersedia setelah pencairan pembiayaan selesai diverifikasi.
                    </div>
                @endif

                <form method="POST" action="{{ route('anggota.transaksi.angsuran.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="pembiayaanSelect" class="block text-sm font-medium text-gray-700">Pembiayaan</label>
                        <select id="pembiayaanSelect" name="pembiayaan_id" required class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">Pilih pembiayaan</option>
                            @foreach ($pembiayaanList as $pembiayaan)
                                <option value="{{ $pembiayaan->id }}" @selected((string) $initialPembiayaanId === (string) $pembiayaan->id)>
                                    {{ $pembiayaan->kode }} - {{ $pembiayaan->objek_pembiayaan ?? 'Pembiayaan' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="angsuranSelect" class="block text-sm font-medium text-gray-700">Tagihan angsuran</label>
                        <select id="angsuranSelect" name="angsuran_id" required class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">Pilih tagihan angsuran</option>
                        </select>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="nominalJadwal" class="block text-sm font-medium text-gray-700">Nominal sesuai jadwal</label>
                            <input id="nominalJadwal" type="text" readonly placeholder="Pilih tagihan terlebih dahulu" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-gray-700">
                            <input id="jumlahDibayar" type="hidden" name="jumlah_dibayar">
                            <p class="mt-1 text-xs text-gray-500">Nominal dikunci sesuai jadwal angsuran dan divalidasi kembali oleh sistem.</p>
                        </div>
                        <div>
                            <label for="jatuhTempo" class="block text-sm font-medium text-gray-700">Jatuh tempo tagihan</label>
                            <input id="jatuhTempo" type="text" readonly placeholder="Pilih tagihan terlebih dahulu" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-gray-700">
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="tanggalBayar" class="block text-sm font-medium text-gray-700">Tanggal transfer</label>
                            <input id="tanggalBayar" type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', today()->toDateString()) }}" required class="mt-1 w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label for="rekeningKoperasi" class="block text-sm font-medium text-gray-700">Rekening koperasi tujuan</label>
                            <select id="rekeningKoperasi" name="rekening_koperasi" class="mt-1 w-full rounded-lg border-gray-300">
                                <option value="">Pilih rekening koperasi</option>
                                @foreach ($rekeningKoperasi as $rekening)
                                    <option value="{{ $rekening->id }}" @selected(old('rekening_koperasi') == $rekening->id)>
                                        {{ $rekening->nama_bank }} - {{ $rekening->nomor_rekening }} ({{ $rekening->atas_nama }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="buktiTransfer" class="block text-sm font-medium text-gray-700">Bukti transfer</label>
                        <input id="buktiTransfer" type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" required class="mt-1 w-full rounded-lg border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Format JPG, PNG, atau PDF dengan ukuran maksimal 2 MB.</p>
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                        <a href="{{ route('anggota.transaksi.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Kembali</a>
                        <button id="submitButton" type="submit" disabled class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">Kirim Bukti Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const angsuranByPembiayaan = {{ Js::from($angsuranByPembiayaan) }};
        const initialPembiayaanId = {{ Js::from($initialPembiayaanId) }};
        const initialAngsuranId = {{ Js::from($initialAngsuranId) }};
        const pembiayaanSelect = document.getElementById('pembiayaanSelect');
        const angsuranSelect = document.getElementById('angsuranSelect');
        const nominalJadwal = document.getElementById('nominalJadwal');
        const jumlahDibayar = document.getElementById('jumlahDibayar');
        const jatuhTempo = document.getElementById('jatuhTempo');
        const submitButton = document.getElementById('submitButton');

        function formatRupiah(nominal) {
            return 'Rp ' + Number(nominal || 0).toLocaleString('id-ID');
        }

        function formatTanggal(tanggal) {
            if (!tanggal) return '';

            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
            }).format(new Date(tanggal + 'T00:00:00'));
        }

        function setDetailAngsuran() {
            const daftarAngsuran = angsuranByPembiayaan[pembiayaanSelect.value] || [];
            const angsuran = daftarAngsuran.find((item) => String(item.id) === angsuranSelect.value);

            nominalJadwal.value = angsuran ? formatRupiah(angsuran.nominal) : '';
            jumlahDibayar.value = angsuran ? angsuran.nominal : '';
            jatuhTempo.value = angsuran ? formatTanggal(angsuran.jatuh_tempo) : '';
            submitButton.disabled = !angsuran;        }

        function isiPilihanAngsuran(selectedId = null) {
            const daftarAngsuran = angsuranByPembiayaan[pembiayaanSelect.value] || [];
            angsuranSelect.innerHTML = '<option value="">Pilih tagihan angsuran</option>';

            daftarAngsuran.forEach((angsuran) => {
                const option = document.createElement('option');
                option.value = angsuran.id;
                option.textContent = angsuran.label + ' - ' + formatRupiah(angsuran.nominal);
                option.selected = String(angsuran.id) === String(selectedId ?? '');
                angsuranSelect.appendChild(option);
            });

            setDetailAngsuran();
        }

        pembiayaanSelect.addEventListener('change', () => isiPilihanAngsuran());
        angsuranSelect.addEventListener('change', setDetailAngsuran);

        if (initialPembiayaanId) {
            pembiayaanSelect.value = String(initialPembiayaanId);
        }
        isiPilihanAngsuran(initialAngsuranId);
    </script>
</x-app-layout>
