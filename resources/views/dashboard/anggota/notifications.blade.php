@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-3">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="p-3 rounded-lg border flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Pesanan Aktif</div>
                            <div class="text-xl font-bold">{{ $pesananAktif }}</div>
                        </div>
                        <div class="text-xs text-gray-400">🔔</div>
                    </div>

                    <div class="p-3 rounded-lg border flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Wishlist</div>
                            <div class="text-xl font-bold">{{ $wishlistCount }}</div>
                        </div>
                        <div class="text-xs text-gray-400">❤️</div>
                    </div>

                    <div class="p-3 rounded-lg border flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Angsuran Jatuh Tempo</div>
                            <div class="text-xl font-bold">{{ $angsuranJatuhTempo }}</div>
                        </div>
                        <div class="text-xs text-gray-400">⚠️</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-2">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Notifikasi Masuk</h3>

                <div class="divide-y">
                    @forelse($entries as $entry)
                        <div class="py-4 flex items-start justify-between">
                            <div>
                                <div class="font-semibold {{ $entry->is_read ? 'text-gray-500' : 'text-gray-900' }}">{{ $entry->title }}</div>
                                <div class="text-sm text-gray-600 mt-1">{{ $entry->message }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $entry->created_at->format('d/m/Y H:i') }}</div>
                                @if(!empty($entry->data))
                                    <pre class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded">{{ json_encode($entry->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </div>
                            <div class="ml-4 text-right">
                                @if(!$entry->is_read)
                                    <form method="POST" action="{{ route('inbox.read', $entry->id) }}">@csrf<button class="px-3 py-2 bg-emerald-600 text-white rounded">Tandai terbaca</button></form>
                                @else
                                    <span class="text-xs text-gray-400">Terbaca</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-500">Belum ada notifikasi</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
