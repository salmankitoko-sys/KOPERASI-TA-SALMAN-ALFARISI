{{--
    Navbar Pengurus
    $notifCount / $chatCount dikirim dari controller (index()), fallback 0 kalau belum ada.
--}}
<header class="sticky top-0 z-20 border-b border-indigo-100 bg-white/85 backdrop-blur-md h-16 flex items-center justify-between px-4 lg:px-6">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden dashboard-navbar-btn p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="hidden sm:block">
            <h1 class="text-base font-semibold text-gray-900" x-text="tabs[activeTab] || tabs.dashboard">Dashboard Pengurus</h1>
            <p class="text-xs text-gray-500">
                Hari ini: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" @click="setActiveTab('notifikasi')" title="Buka notifikasi" aria-label="Buka notifikasi" class="dashboard-navbar-btn relative p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if(($notifCount ?? 0) > 0)
                <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] leading-none rounded-full w-4 h-4 flex items-center justify-center shadow-sm">
                    {{ $notifCount }}
                </span>
            @endif
        </button>

        <a href="{{ route('inbox.index') }}" title="Buka inbox" aria-label="Buka inbox" class="dashboard-navbar-btn relative p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z" />
            </svg>
            @if(($chatCount ?? 0) > 0)
                <span class="absolute -top-0.5 -right-0.5 bg-indigo-500 text-white text-[10px] leading-none rounded-full w-4 h-4 flex items-center justify-center shadow-sm">
                    {{ $chatCount }}
                </span>
            @endif
        </a>

        <div class="w-px h-6 bg-gray-200 mx-1"></div>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg hover:bg-gray-100">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-gray-700">
                        {{ auth()->user()->name ?? 'Pengurus' }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Pengurus' }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
