<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Metode Pembayaran</h2>
                <p class="mt-0.5 text-sm text-gray-500">Pilih metode pembayaran untuk angsuran Anda.</p>
            </div>
            <a href="{{ route('anggota.angsuran.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            {{-- Angsuran Summary --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-400">Tagihan Angsuran</h3>
                <dl class="space-y-3">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Pembiayaan</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $angsuran->pembiayaan->kode }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Angsuran Ke-</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $angsuran->bulan_ke }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Jatuh Tempo</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($angsuran->jatuh_tempo)->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-bold text-gray-700">Total Tagihan</dt>
                            <dd class="text-lg font-bold text-indigo-600">Rp {{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- Payment Methods --}}
            <div class="mt-5 space-y-4">
                {{-- QRIS Option --}}
                <div class="rounded-2xl border-2 border-indigo-200 bg-indigo-50 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-bold text-indigo-900">Bayar via QRIS</h4>
                            <p class="mt-1 text-sm text-indigo-700">Scan QR Code dengan e-wallet atau mobile banking Anda. Pembayaran otomatis terverifikasi.</p>
                            <ul class="mt-2 space-y-1 text-xs text-indigo-600">
                                <li>✓ Pembayaran otomatis terkonfirmasi</li>
                                <li>✓ Tidak perlu upload bukti transfer</li>
                                <li>✓ QR Code berlaku 30 menit</li>
                            </ul>
                            @if ($existingPayment)
                                <a href="{{ route('anggota.payments.show', $existingPayment->payment_code) }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Lihat QR Code (masih aktif)
                                </a>
                            @else
                                <form method="POST" action="{{ route('anggota.payments.qris.angsuran') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="pembiayaan_id" value="{{ $angsuran->pembiayaan_id }}">
                                    <input type="hidden" name="angsuran_id" value="{{ $angsuran->id }}">
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                        Buat QR Code QRIS
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Manual Transfer Option --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-bold text-gray-900">Transfer Manual</h4>
                            <p class="mt-1 text-sm text-gray-600">Transfer ke rekening koperasi, lalu unggah bukti transfer untuk verifikasi manual oleh pengurus.</p>
                            <ul class="mt-2 space-y-1 text-xs text-gray-500">
                                <li>• Perlu upload bukti transfer</li>
                                <li>• Diverifikasi oleh pengurus</li>
                                <li>• Proses 1-2 hari kerja</li>
                            </ul>
                            <a href="{{ route('anggota.transaksi.angsuran.create', ['pembiayaan_id' => $angsuran->pembiayaan_id, 'angsuran_id' => $angsuran->id]) }}" class="mt-3 inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Upload Bukti Transfer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('click', (event) => {
            const manualLink = event.target.closest('a[href*=/anggota/transaksi/angsuran]');
            if (!manualLink) return;

            const url = new URL(manualLink.href, window.location.origin);
            url.searchParams.set('manual', '1');
            manualLink.href = url.toString();
        });
    </script>
</x-app-layout>
