<header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-stone-200 bg-white/90 px-4 backdrop-blur-md lg:px-6">
    <div class="flex items-center gap-3">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-xl p-2 text-stone-600 hover:bg-stone-100 lg:hidden" aria-label="Buka menu">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
        <div><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-red-800">Pimpinan Pengurus</p><h1 class="text-lg font-bold text-stone-900" x-text="tabs[activeTab] || tabs.ringkasan">Dashboard</h1><p class="hidden text-xs text-stone-500 sm:block">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p></div>
    </div>
    <div class="flex items-center gap-2">
        <span class="hidden rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800 md:inline-flex">Mode Pengawasan</span>
        <button type="button" @click="setActiveTab('notifikasi')" class="relative rounded-xl p-2 text-stone-600 transition hover:bg-stone-100" title="Notifikasi" aria-label="Buka notifikasi"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>@if(($notifCount ?? 0) > 0)<span class="absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-700 px-1 text-[10px] text-white">{{ $notifCount }}</span>@endif</button>
        <div class="mx-1 h-7 w-px bg-stone-200"></div>
        <x-dropdown align="right" width="48">
            <x-slot name="trigger"><button class="flex items-center gap-2 rounded-xl p-1.5 hover:bg-stone-100"><div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-900 text-sm font-bold text-amber-200 ring-2 ring-amber-300/60">{{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}</div><div class="hidden text-left sm:block"><p class="max-w-32 truncate text-sm font-semibold text-stone-800">{{ auth()->user()->name }}</p><p class="text-[11px] text-stone-500">Ketua Koperasi</p></div><svg class="h-4 w-4 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" /></svg></button></x-slot>
            <x-slot name="content">
                <div class="border-b px-4 py-3"><p class="text-sm font-semibold text-stone-900">{{ auth()->user()->name }}</p><p class="truncate text-xs text-stone-500">{{ auth()->user()->email }}</p></div>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">Profil Saya</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-700 hover:bg-red-50">Keluar</button></form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
