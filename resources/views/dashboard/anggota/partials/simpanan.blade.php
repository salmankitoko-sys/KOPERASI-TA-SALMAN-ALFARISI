<div x-show="activeTab === 'simpanan'" x-cloak class="space-y-6">
    @if(request('tab') === 'simpanan' && session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(request('tab') === 'simpanan' && session('info'))
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800">{{ session('info') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h4 class="font-bold text-gray-900">Rincian Simpanan</h4>
            <p class="mb-5 text-xs text-gray-500">Ringkasan simpanan Anda di koperasi.</p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach (['pokok' => 'Pokok', 'wajib' => 'Wajib', 'sukarela' => 'Sukarela'] as $key => $label)
                    <div class="rounded-xl border bg-gray-50 p-4"><p class="text-xs text-gray-500">Simpanan {{ $label }}</p><p class="mt-1 font-black">{{ $rupiah($simpanan->{$key}) }}</p></div>
                @endforeach
            </div>
            <div class="mt-5 grid gap-3 md:grid-cols-2">
                <div class="flex items-center justify-between rounded-xl bg-indigo-600 p-4 text-white"><span>Total Simpanan</span><strong>{{ $rupiah($simpanan->total) }}</strong></div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
                    <div class="flex items-center justify-between"><span>Menunggu Pembayaran</span><strong>{{ $rupiah($simpanan->pending) }}</strong></div>
                    @if($pembayaranSimpananPending)
                        <a href="{{ route('anggota.payments.show', $pembayaranSimpananPending->payment_code) }}" class="mt-3 block rounded-lg bg-amber-600 px-3 py-2 text-center text-xs font-bold text-white hover:bg-amber-700">Lanjutkan Pembayaran</a>
                    @endif
                </div>
            </div>
            <p class="mt-4 rounded-xl bg-gray-50 p-3 text-xs text-gray-500">Saldo bertambah otomatis setelah QRIS berhasil dibayar.</p>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h4 class="font-bold">Setor Simpanan</h4><p class="mb-5 text-xs text-gray-500">Pembayaran dilakukan melalui QRIS.</p>
            <a href="{{ route('anggota.payments.simpanan.qris') }}" class="block rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-indigo-700">Bayar Simpanan via QRIS</a>
        </section>
    </div>

    <section class="overflow-hidden rounded-2xl border bg-white shadow-sm">
        <div class="border-b px-6 py-4"><h4 class="font-bold">Riwayat Simpanan</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-gray-50"><th class="px-6 py-3 text-left">Tanggal</th><th class="px-6 py-3 text-left">Jenis</th><th class="px-6 py-3 text-right">Jumlah</th><th class="px-6 py-3 text-left">Status</th></tr></thead><tbody>
        @forelse($riwayatSimpanan as $item)
            <tr class="border-t"><td class="px-6 py-3">{{ optional($item->tanggal)->translatedFormat('d M Y') ?? '-' }}</td><td class="px-6 py-3">{{ ucfirst($item->jenis) }}</td><td class="px-6 py-3 text-right font-bold">{{ $rupiah($item->jumlah) }}</td><td class="px-6 py-3">{{ $item->status === 'masuk' ? 'Berhasil' : ($item->status === 'ditolak' ? 'Dibatalkan' : 'Menunggu Pembayaran') }}</td></tr>
        @empty
            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi simpanan.</td></tr>
        @endforelse
        </tbody></table></div>
    </section>
</div>