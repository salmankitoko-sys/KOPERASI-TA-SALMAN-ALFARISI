<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Riwayat Pembayaran</h2>
                <p class="mt-0.5 text-sm text-gray-500">Daftar seluruh pembayaran yang pernah Anda lakukan.</p>
            </div>
            <a href="{{ route('anggota.transaksi.dashboard') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Tanggal</th>
                                <th class="px-5 py-3 text-left font-semibold">Kode</th>
                                <th class="px-5 py-3 text-left font-semibold">Jenis</th>
                                <th class="px-5 py-3 text-right font-semibold">Nominal</th>
                                <th class="px-5 py-3 text-center font-semibold">Metode</th>
                                <th class="px-5 py-3 text-center font-semibold">Status</th>
                                <th class="px-5 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-gray-600">{{ $payment->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="px-5 py-3 font-mono text-xs font-semibold text-gray-900">{{ $payment->payment_code }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ $payment->type_label }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                            @if ($payment->payment_method === 'qris')
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                            @endif
                                            {{ ucfirst($payment->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if ($payment->status === 'paid')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Lunas</span>
                                        @elseif ($payment->status === 'pending')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                                        @elseif ($payment->status === 'expired')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Kedaluwarsa</span>
                                        @elseif ($payment->status === 'failed')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Gagal</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <a href="{{ route('anggota.payments.show', $payment->payment_code) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">
                                        Belum ada riwayat pembayaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payments->hasPages())
                    <div class="border-t border-gray-100 px-5 py-3">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
