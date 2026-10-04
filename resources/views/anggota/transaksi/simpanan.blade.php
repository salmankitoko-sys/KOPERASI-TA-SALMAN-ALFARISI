<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">Setoran Simpanan</h2>
                <p class="text-sm text-gray-500 mt-0.5">Upload bukti transfer untuk setoran simpanan Anda.</p>
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('anggota.transaksi.simpanan.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis simpanan</label>
                        <select name="jenis_simpanan" class="mt-1 w-full rounded-lg border-gray-300" required>
                            <option value="pokok">Pokok</option>
                            <option value="wajib">Wajib</option>
                            <option value="sukarela">Sukarela</option>
                        </select>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nominal</label>
                            <input type="number" name="nominal" class="mt-1 w-full rounded-lg border-gray-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal setor</label>
                            <input type="date" name="tanggal_setor" class="mt-1 w-full rounded-lg border-gray-300" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bukti transfer</label>
                        <input type="file" name="bukti_transfer" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('anggota.transaksi.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700">Kembali</a>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
