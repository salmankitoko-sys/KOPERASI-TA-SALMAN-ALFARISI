@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-3">Broadcast Pesan</h3>
                <form method="POST" action="{{ route('pengurus.notifikasi.broadcast') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-xs text-gray-600 mb-1">Penerima</label>
                        <select name="recipient" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="semua_anggota">Semua Anggota</option>
                            <option value="semua_pengurus">Semua Pengurus</option>
                            <option value="semua_admin">Semua Admin</option>
                            <option value="semua_user">Semua Pengguna</option>
                            <option value="anggota">Role: anggota (pilih role langsung)</option>
                            <option value="pengurus">Role: pengurus</option>
                            <option value="admin">Role: admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs text-gray-600 mb-1">Judul Pesan</label>
                        <input name="title" class="w-full border rounded px-3 py-2 text-sm" placeholder="Contoh: Pengumuman RAT" required />
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs text-gray-600 mb-1">Isi Pesan</label>
                        <textarea name="message" rows="6" class="w-full border rounded px-3 py-2 text-sm" placeholder="Tulis pesan..." required></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Kirim Broadcast</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-span-2">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Riwayat Notifikasi</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-lg border bg-white flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-indigo-50 flex items-center justify-center text-indigo-600">👥</div>
                        <div>
                            <div class="font-semibold">Anggota Baru Mendaftar</div>
                            <div class="text-sm text-gray-500">{{ $anggotaPending }} anggota baru menunggu verifikasi</div>
                        </div>
                        <div class="ml-auto text-xs text-gray-400">{{ now()->subHours(2)->diffForHumans() }}</div>
                    </div>

                    <div class="p-4 rounded-lg border bg-white flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-amber-50 flex items-center justify-center text-amber-600">💼</div>
                        <div>
                            <div class="font-semibold">Pengajuan Pembiayaan Baru</div>
                            <div class="text-sm text-gray-500">{{ $pengajuanPembiayaan }} pengajuan pembiayaan perlu direview</div>
                        </div>
                        <div class="ml-auto text-xs text-gray-400">{{ now()->subHours(5)->diffForHumans() }}</div>
                    </div>

                    <div class="p-4 rounded-lg border bg-white flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-red-50 flex items-center justify-center text-red-600">⚠️</div>
                        <div>
                            <div class="font-semibold">Angsuran Jatuh Tempo</div>
                            <div class="text-sm text-gray-500">{{ $angsuranJatuhTempo }} anggota memiliki angsuran yang jatuh tempo hari ini</div>
                        </div>
                        <div class="ml-auto text-xs text-gray-400">{{ now()->subDay()->diffForHumans() }}</div>
                    </div>

                    <div class="p-4 rounded-lg border bg-white flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-cyan-50 flex items-center justify-center text-cyan-600">📦</div>
                        <div>
                            <div class="font-semibold">Produk Baru Perlu Moderasi</div>
                            <div class="text-sm text-gray-500">{{ $produkModerasi }} produk baru menunggu persetujuan</div>
                        </div>
                        <div class="ml-auto text-xs text-gray-400">{{ now()->subDay()->diffForHumans() }}</div>
                    </div>
                </div>

                <div>
                    @forelse($recent as $r)
                        <div class="flex items-center justify-between p-3 border-b">
                            <div>
                                <div class="font-medium">{{ $r->title }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($r->message, 120) }}</div>
                            </div>
                            <div class="text-xs text-gray-400">{{ $r->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">Belum ada notifikasi</div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
