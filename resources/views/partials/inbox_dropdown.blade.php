@php
$unread = \App\Models\InboxEntry::where('user_id', auth()->id())->where('is_read', 0)->count();
$items = \App\Models\InboxEntry::where('user_id', auth()->id())->orderBy('created_at','desc')->limit(6)->get();

// Tentukan route tujuan saat ikon ditekan: prioritaskan role-specific page jika tersedia
$listRoute = route('inbox.index');
if (auth()->check()) {
    $role = auth()->user()->role ?? null;

    // Pengurus: cek beberapa kemungkinan nama rute
    if ($role === 'pengurus') {
        if (\Illuminate\Support\Facades\Route::has('pengurus.marketplace.notifikasi.index')) {
            $listRoute = route('pengurus.marketplace.notifikasi.index');
        } elseif (\Illuminate\Support\Facades\Route::has('pengurus.notifikasi.index')) {
            $listRoute = route('pengurus.notifikasi.index');
        }
    }

    // Anggota: cek kemungkinan rute (dapat diletakkan di bawah marketplace)
    if ($role === 'anggota') {
        if (\Illuminate\Support\Facades\Route::has('anggota.marketplace.notifikasi.index')) {
            $listRoute = route('anggota.marketplace.notifikasi.index');
        } elseif (\Illuminate\Support\Facades\Route::has('anggota.notifikasi.index')) {
            $listRoute = route('anggota.notifikasi.index');
        }
    }
}
@endphp

<div class="relative">
    <a href="{{ $listRoute }}" class="inline-flex items-center gap-2">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        @if($unread)
            <span class="text-sm font-bold text-red-600">{{ $unread }}</span>
        @endif
    </a>
    <div class="absolute right-0 mt-2 w-80 bg-white border rounded-lg shadow-lg z-50">
        <div class="p-3 border-b">
            <div class="flex items-center justify-between">
                <strong>Notifikasi</strong>
                <form method="POST" action="{{ route('inbox.read-all') }}">@csrf<button class="text-xs text-gray-500">Tandai semua</button></form>
            </div>
        </div>
        <div class="max-h-64 overflow-auto">
            @forelse($items as $it)
                <a href="{{ $listRoute }}" class="block px-3 py-2 border-b hover:bg-gray-50">
                    <div class="text-sm font-medium {{ $it->is_read ? 'text-gray-600' : 'text-gray-900' }}">{{ $it->title }}</div>
                    <div class="text-xs text-gray-500">{{ Str::limit($it->message, 80) }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $it->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <div class="p-4 text-center text-gray-500">Tidak ada notifikasi</div>
            @endforelse
        </div>
        <div class="p-2 text-center">
        <a href="{{ $listRoute }}" class="text-sm text-indigo-600">Lihat semua</a>
        </div>
    </div>
</div>