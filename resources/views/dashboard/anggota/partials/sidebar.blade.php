<aside
    x-show="sidebarOpen || window.innerWidth >= 1024"
    x-transition
    x-cloak
    @resize.window="if(window.innerWidth >= 1024) sidebarOpen = false"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 shadow-lg
           transform lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>

    <div class="flex flex-col h-full">

        {{-- Logo --}}
        <div class="px-6 py-6 border-b">
            <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">
                Aplikasi
            </p>

            <h2 class="text-xl font-bold text-indigo-700">
                SIPDKS
            </h2>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto py-4">

            <nav class="space-y-2 px-3">

                @foreach($menuGroups as $group)

                    @if($group['label'])
                        <p class="px-3 pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-gray-400">
                            {{ $group['label'] }}
                        </p>
                    @endif

                    @foreach($group['items'] as $item)

                        <button
                            type="button"
                            @click="setActiveTab('{{ $item['key'] }}')"
                            class="flex items-center w-full gap-3 rounded-xl px-4 py-3 transition font-medium text-left"
                            :class="activeTab=='{{ $item['key'] }}'
                                ? 'bg-indigo-600 text-white shadow'
                                : 'text-gray-600 hover:bg-gray-100'">

                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24">

                                @switch($item['icon'])

                                    @case('home')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    @break

                                    @case('wave')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2"/>
                                    @break

                                    @case('doc')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                    @break

                                    @case('chart')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 19V6l12-3v13"/>
                                    @break

                                    @case('profit-share')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v9h9A9 9 0 0012 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 4.06A9 9 0 104 13h7V4.06z" />
                                    @break

                                    @case('wallet')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.5 6.75A2.25 2.25 0 016.75 4.5h10.5A2.25 2.25 0 0119.5 6.75v.75H6.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 13.5h.008v.008H16.5V13.5z" />
                                    @break

                                    @case('bag')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 11H4L5 9z"/>
                                    @break

                                    @case('box')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                    @break

                                    @case('store')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7l1.5-4h15L21 7M3 7v12a1 1 0 001 1h16a1 1 0 001-1V7"/>
                                    @break

                                    @case('clip')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5"/>
                                    @break

                                    @case('timer')
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 8v4l2 2"/>
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 2h6"/>
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 22a8 8 0 100-16 8 8 0 000 16z"/>
                                    @break

                                    @case('money')
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.25 7.5A2.25 2.25 0 014.5 5.25h15A2.25 2.25 0 0121.75 7.5v9A2.25 2.25 0 0119.5 18.75h-15A2.25 2.25 0 012.25 16.5v-9z" />
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9.75v4.5m-1.5-3a1.5 1.5 0 013 0c0 .828-.672 1.5-1.5 1.5S10.5 13.422 10.5 14.25a1.5 1.5 0 003 0" />
                                    @break

                                    @case('inbox')
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                                    @break
                                @endswitch

                            </svg>

                            {{ $item['title'] }}

                            {{-- Badge notifikasi belum dibaca --}}
                            @if($item['key'] === 'notifikasi' && ($jumlahNotif ?? 0) > 0)
                                <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">
                                    {{ ($jumlahNotif ?? 0) > 99 ? '99+' : ($jumlahNotif ?? 0) }}
                                </span>
                            @endif

                            {{-- Badge pesanan menunggu untuk penjual --}}
                            @if($item['key'] === 'pesanan-masuk' && ($statistikPesananMasuk->menunggu ?? 0) > 0)
                                <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[10px] font-bold text-white">
                                    {{ $statistikPesananMasuk->menunggu ?? 0 }}
                                </span>
                            @endif

                        </button>

                    @endforeach

                @endforeach

            </nav>

        </div>

        {{-- Footer --}}
        <div class="border-t p-4">

            <button
                @click="setActiveTab('profil')"
                class="flex items-center gap-3 w-full rounded-xl px-4 py-3 text-gray-600 hover:bg-gray-100">

                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>

                </svg>

                Pengaturan Profil

            </button>

        </div>

    </div>

</aside>
