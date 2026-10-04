<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Ajukan Pencairan Dana</h2>
                <p class="mt-0.5 text-sm text-gray-500">Kirim rekening tujuan. Bukti transfer akan dicatat oleh pengurus setelah dana dikirim.</p>
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
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('anggota.transaksi.pencairan.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="pembiayaan_id" class="block text-sm font-medium text-gray-700">Pembiayaan</label>
                        <select id="pembiayaan_id" name="pembiayaan_id" required class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">Pilih pembiayaan yang disetujui</option>
                            @foreach ($pembiayaan as $item)
                                <option value="{{ $item->id }}" @selected(old('pembiayaan_id') == $item->id)>{{ $item->kode }} - {{ $item->objek_pembiayaan }} (Plafon Rp {{ number_format($item->jumlah_pembiayaan, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="nominal_pencairan" class="block text-sm font-medium text-gray-700">Nominal pencairan</label>
                            <input id="nominal_pencairan" type="number" name="nominal_pencairan" value="{{ old('nominal_pencairan') }}" min="1" required class="mt-1 w-full rounded-lg border-gray-300">
                            <p class="mt-1 text-xs text-gray-500">Nominal tidak dapat melebihi sisa plafon pembiayaan.</p>
                        </div>
                        <div>
                            <label for="tanggal_pencairan" class="block text-sm font-medium text-gray-700">Tanggal yang diharapkan</label>
                            <input id="tanggal_pencairan" type="date" name="tanggal_pencairan" value="{{ old('tanggal_pencairan', today()->toDateString()) }}" required class="mt-1 w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="bank_tujuan" class="block text-sm font-medium text-gray-700">Bank tujuan</label>
                            <input id="bank_tujuan" type="text" name="bank_tujuan" value="{{ old('bank_tujuan') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label for="no_rekening_tujuan" class="block text-sm font-medium text-gray-700">Nomor rekening tujuan</label>
                            <input id="no_rekening_tujuan" type="text" name="no_rekening_tujuan" value="{{ old('no_rekening_tujuan') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label for="nama_pemilik_rekening" class="block text-sm font-medium text-gray-700">Nama pemilik rekening</label>
                        <input id="nama_pemilik_rekening" type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan untuk pengurus</label>
                        <textarea id="catatan" name="catatan" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                        <a href="{{ route('anggota.transaksi.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Kembali</a>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Kirim Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
