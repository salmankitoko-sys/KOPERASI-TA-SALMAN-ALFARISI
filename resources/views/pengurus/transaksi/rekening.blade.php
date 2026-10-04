<x-bendahara-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Rekening Koperasi</h2>
                <p class="mt-0.5 text-sm text-gray-500">Daftar rekening resmi koperasi untuk transaksi manual.</p>
            </div>
            <a href="{{ route('bendahara.transaksi.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:border-indigo-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Pencairan
            </a>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50/80 to-white px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Tambah Rekening Baru</h3>
                            <p class="mt-0.5 text-sm text-gray-500">Masukkan data rekening koperasi yang akan digunakan untuk transaksi.</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('bendahara.transaksi.rekening.store') }}" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Nama Bank</label>
                            <input type="text" name="nama_bank" placeholder="Contoh: Bank Mandiri" required class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" placeholder="Contoh: 1234567890" required class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Atas Nama</label>
                            <input type="text" name="atas_nama" placeholder="Contoh: Koperasi Syariah" required class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Kode Rekening <span class="text-gray-400 normal-case">(opsional)</span></label>
                            <input type="text" name="kode_rekening" placeholder="Contoh: MKS-001" class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 hover:shadow-md">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Simpan Rekening
                        </button>
                    </div>
                </form>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Daftar Rekening</h3>
                            <p class="mt-0.5 text-sm text-gray-500">Rekening koperasi yang tersedia untuk pencairan dana.</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3.5 text-left font-bold">Bank</th>
                                <th class="px-6 py-3.5 text-left font-bold">Nomor Rekening</th>
                                <th class="px-6 py-3.5 text-left font-bold">Atas Nama</th>
                                <th class="px-6 py-3.5 text-left font-bold">Status</th>
                                <th class="px-6 py-3.5 text-left font-bold">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($rekening as $item)
                                <tr class="transition hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-xs font-bold text-indigo-700">
                                                {{ strtoupper(substr($item->nama_bank, 0, 2)) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $item->nama_bank }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm font-semibold text-gray-800">{{ $item->nomor_rekening }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $item->atas_nama }}</td>
                                    <td class="px-6 py-4">
                                        @if($item->is_aktif)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('bendahara.transaksi.rekening.status', $item->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            @if($item->is_aktif)
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50 hover:border-red-300">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-bold text-emerald-600 transition hover:bg-emerald-50 hover:border-emerald-300">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <p class="mt-4 text-sm font-medium text-gray-500">Belum ada rekening koperasi</p>
                                            <p class="mt-1 text-xs text-gray-400">Tambahkan rekening baru menggunakan form di atas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-bendahara-layout>
