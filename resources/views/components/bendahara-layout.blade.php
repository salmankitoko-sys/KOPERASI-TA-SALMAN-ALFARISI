<x-app-layout>
@php
    $unread = auth()->user()->unreadInboxCount();
    $pendingMutasi = \App\Models\TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count();
    $pendingCair = \App\Models\PencairanDana::where('status', 'menunggu')->count();
    $navGroups = [
        'UTAMA' => [
            ['Dashboard', 'bendahara.dashboard', 'M3 12l9-9 9 9M5 10v10h14V10', null],
        ],
        'TRANSAKSI' => [
            ['Pencairan Dana', 'bendahara.pencairan.index', 'M12 3v18m9-9H3m15.4-5.4L21 9.2M5.6 17.4L3 14.8', $pendingCair],
            ['Angsuran', 'bendahara.angsuran.index', 'M7 7h10M7 12h10M7 17h6M5 3h14v18H5z', null],
            ['Mutasi Kas', 'bendahara.transaksi.mutasi.index', 'M7 7h11l-3-3m3 3-3 3M17 17H6l3 3m-3-3 3-3', $pendingMutasi],
        ],
        'MANAJEMEN' => [
            ['Rekening Koperasi', 'bendahara.transaksi.rekening.index', 'M3 10h18M5 10V8l7-4 7 4v2M7 14h2m3 0h2m3 0h2M5 18h14', null],
            ['Laporan', 'bendahara.laporan.index', 'M6 2h9l5 5v15H6zM14 2v6h6M9 13h6m-6 4h6', null],
            ['Notifikasi', 'bendahara.notifikasi', 'M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4', $unread],
        ],
    ];
@endphp
<div x-data="{ sidebarOpen:false, profileOpen:false }" class="min-h-screen bg-slate-50">
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"></div>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-950 text-white transition-transform duration-300 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500 shadow-lg shadow-emerald-950/40">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 10V8l7-4 7 4v2M7 14h2m3 0h2m3 0h2M5 18h14"/></svg>
                </span>
                <span><span class="block text-[10px] font-bold uppercase tracking-[.24em] text-emerald-400">Zona Keuangan</span><span class="mt-1 block text-base font-bold">Bendahara</span></span>
            </a>
            <button @click="sidebarOpen=false" class="rounded-lg p-2 text-slate-400 hover:bg-white/10 lg:hidden"><span class="sr-only">Tutup menu</span>&times;</button>
        </div>
        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6">
            @foreach($navGroups as $group => $items)
                <section>
                    <p class="mb-2 px-3 text-[10px] font-bold tracking-[.2em] text-slate-500">{{ $group }}</p>
                    <div class="space-y-1">
                        @foreach($items as [$label,$routeName,$icon,$badge])
                            @php
                                $active = request()->routeIs($routeName)
                                    || ($routeName === 'bendahara.pencairan.index' && request()->routeIs('bendahara.transaksi.index'));
                            @endphp
                            <a href="{{ route($routeName) }}" @click="sidebarOpen=false"
                               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-white' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                                <span class="min-w-0 flex-1 truncate">{{ $label }}</span>
                                @if($badge)
                                    <span class="rounded-full {{ $active ? 'bg-white/20' : 'bg-rose-500/20 text-rose-300' }} px-2 py-0.5 text-[10px] font-bold">{{ $badge }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-sm font-bold">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                <div class="min-w-0"><p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p><p class="text-xs text-slate-400">Bendahara Koperasi</p></div>
            </div>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="flex h-20 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button @click="sidebarOpen=true" class="rounded-xl border border-slate-200 p-2.5 text-slate-600 lg:hidden"><span class="sr-only">Buka menu</span><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <div class="hidden min-w-0 sm:block">
                    <p class="text-xs font-semibold text-slate-400">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <p class="truncate text-sm font-bold text-slate-800">Pusat Operasional Keuangan</p>
                </div>
                <form action="{{ route('bendahara.transaksi.mutasi.index') }}" method="GET" class="mx-auto hidden w-full max-w-md md:block">
                    <label class="relative block"><span class="sr-only">Cari transaksi</span><svg class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m21 21-4.3-4.3M19 11a8 8 0 11-16 0 8 8 0 0116 0z"/></svg><input name="q" value="{{ request('q') }}" placeholder="Cari transaksi atau referensi..." class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-emerald-500 focus:ring-emerald-500"></label>
                </form>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('bendahara.pencairan.index') }}" class="hidden rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 sm:inline-flex">+ Proses transaksi</a>
                    <a href="{{ route('bendahara.notifikasi') }}" class="relative rounded-xl border border-slate-200 p-2.5 text-slate-600 hover:bg-slate-50"><span class="sr-only">Notifikasi</span><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@if($unread)<span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $unread }}</span>@endif</a>
                    <div class="relative" @click.outside="profileOpen=false">
                        <button @click="profileOpen=!profileOpen" class="flex items-center gap-2 rounded-xl border border-slate-200 p-1.5 pr-2 hover:bg-slate-50"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></button>
                        <div x-show="profileOpen" x-cloak x-transition class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                            <div class="border-b px-3 py-2"><p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p><p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p></div>
                            <a href="{{ route('profile.edit') }}" class="mt-1 block rounded-lg px-3 py-2 text-sm hover:bg-slate-50">Profil saya</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">Keluar</button></form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if(isset($header))
            <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-6 lg:px-8">{{ $header }}</div>
        @endif
        <main>{{ $slot }}</main>
    </div>
</div>
</x-app-layout>
