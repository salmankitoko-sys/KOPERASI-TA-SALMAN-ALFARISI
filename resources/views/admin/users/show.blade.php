<x-admin-layout title="Detail User">
    @php
        $roleClasses = [
            \App\Models\User::ROLE_ADMIN => 'bg-slate-900 text-white',
            \App\Models\User::ROLE_PENGURUS => 'bg-indigo-100 text-indigo-800',
            \App\Models\User::ROLE_DPS => 'bg-emerald-100 text-emerald-800',
            \App\Models\User::ROLE_ANGGOTA => 'bg-sky-100 text-sky-800',
            \App\Models\User::ROLE_PELANGGAN => 'bg-amber-100 text-amber-800',
        ];
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-900">Kembali ke manajemen user</a>
                    <h2 class="mt-2 text-2xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $roleClasses[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $roles[$user->role] ?? ucfirst($user->role) }}
                    </span>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        Akses {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            @if (session('error'))
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <section class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">Profil Akun</h3>
                    <dl class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor HP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->no_hp ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->tanggal_lahir?->locale('id')->translatedFormat('d M Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->pekerjaan ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Penghasilan</dt>
                            <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format((float) ($user->penghasilan ?? 0), 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->status ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Bergabung</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email Terverifikasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at?->locale('id')->translatedFormat('d M Y H:i') ?? 'Belum' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Login Terakhir</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->last_login_at?->locale('id')->translatedFormat('d M Y H:i') ?? 'Belum pernah' }}</dd>
                        </div>
                    </dl>
                </div>

                <aside class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">Kontrol Data</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-500">Admin dapat memperbarui profil, role, status akses, dan password pengguna.</p>
                    <a href="{{ route('admin.users.edit', $user) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">Edit Akun</a>

                    @if ($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="mt-5" onsubmit="return confirm('Hapus akun {{ $user->name }}? Data terkait user ini juga dapat ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">Hapus Akun</button>
                        </form>
                    @else
                        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Akun admin yang sedang dipakai tidak dapat dihapus.</div>
                    @endif
                </aside>
            </section>

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Simpanan</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $activity['simpanan_count'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Rp {{ number_format($activity['simpanan_total'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pembiayaan</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $activity['pembiayaan_count'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Rp {{ number_format($activity['pembiayaan_total'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pesanan</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $activity['pesanan_count'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Rp {{ number_format($activity['pesanan_total'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Toko & Produk</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $activity['toko_count'] }} / {{ $activity['produk_count'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Toko / produk terdaftar</p>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Pesanan Terbaru</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[520px] text-left text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-5 py-3">Nomor</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $order->nomor_pesanan }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ ucfirst($order->status) }} / {{ ucfirst(str_replace('_', ' ', $order->status_pembayaran)) }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-10 text-center text-gray-500">Belum ada pesanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Pembiayaan Terbaru</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[520px] text-left text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-5 py-3">Kode</th>
                                    <th class="px-5 py-3">Akad</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($recentPembiayaan as $item)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $item->kode }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ ucfirst($item->akad) }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ ucfirst($item->status) }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700">Rp {{ number_format((float) $item->jumlah_pembiayaan, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada pembiayaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-admin-layout>
