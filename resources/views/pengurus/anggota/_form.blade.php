@php
    $isEdit = isset($anggota);
    $statusOptions = ['Aktif', 'Calon', 'Non-Aktif'];
@endphp

@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-semibold">Periksa kembali data anggota.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $isEdit ? route('pengurus.anggota.update', $anggota->id) : route('pengurus.anggota.store') }}" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h3 class="text-base font-bold text-gray-900">Identitas Anggota</h3>
            <p class="mt-1 text-sm text-gray-500">Lengkapi data dasar anggota koperasi.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-700">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $anggota->name ?? '') }}" required
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $anggota->email ?? '') }}" required
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_hp" class="mb-1.5 block text-sm font-semibold text-gray-700">No. HP</label>
                <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $anggota->no_hp ?? '') }}"
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('no_hp') border-red-400 @enderror">
                @error('no_hp')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tgl_gabung" class="mb-1.5 block text-sm font-semibold text-gray-700">Tanggal gabung</label>
                <input id="tgl_gabung" name="tgl_gabung" type="date" value="{{ old('tgl_gabung', isset($anggota) ? optional($anggota->tgl_gabung)->format('Y-m-d') : now()->toDateString()) }}"
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('tgl_gabung') border-red-400 @enderror">
                @error('tgl_gabung')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h3 class="text-base font-bold text-gray-900">Status & Finansial</h3>
            <p class="mt-1 text-sm text-gray-500">Atur status anggota dan simpanan pokok awal.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-gray-700">Status</label>
                <select id="status" name="status" required
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('status') border-red-400 @enderror">
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" @selected(old('status', $anggota->status ?? 'Aktif') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pekerjaan" class="mb-1.5 block text-sm font-semibold text-gray-700">Pekerjaan</label>
                <input id="pekerjaan" name="pekerjaan" type="text" value="{{ old('pekerjaan', $anggota->pekerjaan ?? '') }}"
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('pekerjaan') border-red-400 @enderror">
                @error('pekerjaan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="penghasilan" class="mb-1.5 block text-sm font-semibold text-gray-700">Penghasilan / bulan</label>
                <input id="penghasilan" name="penghasilan" type="number" min="0" value="{{ old('penghasilan', $anggota->penghasilan ?? '') }}"
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('penghasilan') border-red-400 @enderror">
                @error('penghasilan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pokok" class="mb-1.5 block text-sm font-semibold text-gray-700">Simpanan pokok</label>
                <input id="pokok" name="pokok" type="number" min="0" value="{{ old('pokok', $anggota->simpanan_pokok ?? '') }}"
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('pokok') border-red-400 @enderror">
                @error('pokok')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700">{{ $isEdit ? 'Password baru' : 'Password awal' }}</label>
                <input id="password" name="password" type="password" autocomplete="new-password" {{ $isEdit ? '' : 'required' }}
                    placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                    class="min-h-[44px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('pengurus.dashboard', ['tab' => 'anggota']) }}"
            class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            Kembali
        </a>
        <button type="submit"
            class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-indigo-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Anggota' }}
        </button>
    </div>
</form>
