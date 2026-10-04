<x-admin-layout title="Edit User">

    <div class="py-8">
        <div class="mx-auto max-w-2xl">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Edit User</h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui data akun {{ $user->name }}.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="shrink-0 text-sm font-medium text-gray-600 transition hover:text-gray-900">Kembali</a>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Nama lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="role" class="mb-1 block text-sm font-medium text-gray-700">Peran</label>
                        <select id="role" name="role" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="is_active" class="mb-1 block text-sm font-medium text-gray-700">Status akses</label>
                        <select id="is_active" name="is_active" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled($user->is(auth()->user()))>
                            <option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Aktif</option>
                            <option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Nonaktif</option>
                        </select>
                        @if($user->is(auth()->user()))<input type="hidden" name="is_active" value="1">@endif
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password baru</label>
                        <input id="password" name="password" type="password" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak diubah.</p>
                        @error('password')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Konfirmasi password baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
