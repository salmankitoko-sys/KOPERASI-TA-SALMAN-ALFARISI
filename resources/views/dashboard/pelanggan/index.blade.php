<x-app-layout>
    @php
        $inboxEntries = \App\Models\InboxEntry::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(10)->get();
        $unreadCount = \App\Models\InboxEntry::where('user_id', auth()->id())->where('is_read', false)->count();
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Akun Pelanggan Marketplace</p>
                <h2 class="text-xl font-black leading-tight text-gray-900">Dashboard Belanja</h2>
                <p class="mt-1 text-sm text-gray-500">Belanja produk anggota, pantau pesanan, dan kelola keranjang dari satu tempat.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                {{-- Notifikasi Dropdown --}}
                <div class="relative" x-data="{ open: false, unread: {{ $unreadCount }}, notifs: {{ Js::from($inboxEntries) }} }">
                    <button @click="open = !open" class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:bg-gray-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <template x-if="unread > 0">
                            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white" x-text="unread > 9 ? '9+' : unread"></span>
                        </template>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="text-sm font-bold text-gray-900">Notifikasi</p>
                            <p class="text-xs text-gray-500" x-text="unread + ' belum dibaca'"></p>
                        </div>
                        <div class="max-h-64 divide-y divide-gray-50 overflow-y-auto">
                            <template x-if="notifs.length === 0">
                                <p class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi</p>
                            </template>
                            <template x-for="n in notifs" :key="n.id">
                                <div class="px-4 py-3 transition hover:bg-gray-50" :class="!n.is_read ? 'bg-orange-50/30' : ''">
                                    <p class="text-xs font-bold" :class="n.is_read ? 'text-gray-500' : 'text-gray-900'" x-text="n.title"></p>
                                    <p class="mt-0.5 text-xs text-gray-500" x-text="n.message ? (n.message.length > 60 ? n.message.substring(0, 60) + '...' : n.message) : ''"></p>
                                </div>
                            </template>
                        </div>
                        <a href="{{ route('inbox.index') }}" class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Lihat semua notifikasi →
                        </a>
                    </div>
                </div>

                <a href="{{ route('marketplace') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-orange-700">
                    Mulai Belanja
                </a>
                <a href="{{ route('marketplace.cart') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                    Keranjang ({{ $cartCount ?? 0 }})
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $rupiah = fn ($v) => class_exists(\App\Helpers\RupiahHelper::class)
            ? \App\Helpers\RupiahHelper::format($v ?? 0)
            : 'Rp ' . number_format($v ?? 0, 0, ',', '.');

        $statusClass = [
            'menunggu' => 'bg-amber-100 text-amber-700',
            'dikemas' => 'bg-blue-100 text-blue-700',
            'dikirim' => 'bg-violet-100 text-violet-700',
            'selesai' => 'bg-emerald-100 text-emerald-700',
            'batal' => 'bg-red-100 text-red-700',
        ];

        $statusSteps = [
            ['label' => 'Belum Bayar', 'value' => $stats['belum_bayar'] ?? 0, 'icon' => 'wallet'],
            ['label' => 'Diproses', 'value' => ($stats['menunggu'] ?? 0) + ($stats['dikemas'] ?? 0), 'icon' => 'box'],
            ['label' => 'Dikirim', 'value' => $stats['dikirim'] ?? 0, 'icon' => 'truck'],
            ['label' => 'Selesai', 'value' => $stats['selesai'] ?? 0, 'icon' => 'check'],
        ];
    @endphp

    <style>
        .buyer-page {
            min-height: 100vh;
            background: #f3f4f6;
            padding: 24px 0;
        }
        .buyer-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 16px;
        }
        .buyer-stack > * + * {
            margin-top: 24px;
        }
        .buyer-hero {
            overflow: hidden;
            border-radius: 12px;
            background: linear-gradient(135deg, #c2410c 0%, #ea580c 52%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 18px 45px rgba(124, 45, 18, 0.28);
        }
        .buyer-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 28px;
            align-items: stretch;
            padding: 30px;
        }
        .buyer-welcome {
            color: #ffedd5;
            font-size: 14px;
            font-weight: 800;
        }
        .buyer-title {
            max-width: 680px;
            margin: 10px 0 0;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.12;
            font-weight: 950;
            color: #ffffff;
        }
        .buyer-desc {
            max-width: 700px;
            margin: 14px 0 0;
            color: #fff7ed;
            font-size: 15px;
            line-height: 1.75;
            font-weight: 600;
        }
        .buyer-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }
        .buyer-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .buyer-btn:hover {
            transform: translateY(-2px);
        }
        .buyer-btn:active {
            transform: translateY(0) scale(0.98);
        }
        .buyer-btn-light {
            background: #ffffff;
            color: #c2410c;
            box-shadow: 0 10px 24px rgba(124, 45, 18, 0.2);
        }
        .buyer-btn-dark {
            background: #065f46;
            color: #ffffff;
            border: 1px solid #ffffff;
        }
        .buyer-summary {
            border: 2px solid #fed7aa;
            border-radius: 12px;
            background: #ffffff;
            padding: 18px;
            color: #111827;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.18);
        }
        .buyer-summary-title {
            color: #c2410c;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .buyer-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        .buyer-summary-card {
            border-radius: 12px;
            border: 1px solid #fdba74;
            background: #ffedd5;
            padding: 16px;
        }
        .buyer-summary-card.green {
            border-color: #86efac;
            background: #dcfce7;
        }
        .buyer-summary-card.blue {
            grid-column: 1 / -1;
            border-color: #bfdbfe;
            background: #dbeafe;
        }
        .buyer-label {
            color: #475569;
            font-size: 12px;
            font-weight: 900;
        }
        .buyer-number {
            margin-top: 4px;
            color: #c2410c;
            font-size: 34px;
            line-height: 1;
            font-weight: 950;
        }
        .buyer-number.green {
            color: #047857;
        }
        .buyer-number.blue {
            color: #1d4ed8;
            font-size: 28px;
        }
        .buyer-status-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        .buyer-status-card {
            display: block;
            border: 1px solid #fed7aa;
            border-radius: 12px;
            background: #ffffff;
            padding: 18px;
            color: #111827;
            text-decoration: none;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }
        .buyer-status-card:hover {
            border-color: #ea580c;
            box-shadow: 0 16px 32px rgba(194, 65, 12, 0.18);
            transform: translateY(-4px);
        }
        .buyer-status-card:active,
        .buyer-menu-card:active,
        .buyer-chip:active,
        .buyer-product:active,
        .buyer-profile-card:active {
            transform: scale(0.98);
        }
        .buyer-is-tapped {
            animation: buyerTap 0.28s ease;
        }
        .buyer-status-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .buyer-icon {
            display: flex;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #ffedd5;
            color: #c2410c;
        }
        .buyer-status-value {
            color: #111827;
            font-size: 28px;
            font-weight: 950;
        }
        .buyer-status-label {
            margin-top: 12px;
            color: #334155;
            font-size: 14px;
            font-weight: 900;
        }
        .buyer-content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 20px;
        }
        .buyer-panel {
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.07);
        }
        .buyer-panel-padded {
            padding: 20px;
        }
        .buyer-panel-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px;
        }
        .buyer-panel-title {
            color: #111827;
            font-size: 18px;
            font-weight: 950;
        }
        .buyer-panel-sub {
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }
        .buyer-link {
            color: #c2410c;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
        }
        .buyer-empty {
            padding: 48px 20px;
            text-align: center;
        }
        .buyer-empty-title {
            color: #1f2937;
            font-size: 15px;
            font-weight: 900;
        }
        .buyer-empty-text {
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
        }
        .buyer-order {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s ease;
        }
        .buyer-order:hover {
            background: #fff7ed;
        }
        .buyer-order:last-child {
            border-bottom: 0;
        }
        .buyer-order-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }
        .buyer-order-code {
            color: #111827;
            font-size: 14px;
            font-weight: 950;
        }
        .buyer-order-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
        }
        .buyer-order-items {
            margin-top: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 800;
        }
        .buyer-order-total {
            color: #111827;
            font-weight: 950;
            text-align: right;
        }
        .buyer-badge {
            display: inline-flex;
            margin-top: 8px;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
        }
        .buyer-side {
            display: grid;
            gap: 20px;
        }
        .buyer-menu-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        .buyer-menu-card {
            display: block;
            border-radius: 12px;
            padding: 18px 12px;
            text-align: center;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
            transition: transform 0.2s ease, filter 0.2s ease;
        }
        .buyer-menu-card:hover {
            filter: brightness(0.98);
            transform: translateY(-3px);
        }
        .buyer-menu-orange {
            border: 1px solid #fdba74;
            background: #fed7aa;
            color: #9a3412;
        }
        .buyer-menu-green {
            border: 1px solid #86efac;
            background: #bbf7d0;
            color: #166534;
        }
        .buyer-menu-blue {
            border: 1px solid #93c5fd;
            background: #bfdbfe;
            color: #1e40af;
        }
        .buyer-menu-slate {
            border: 1px solid #cbd5e1;
            background: #e2e8f0;
            color: #334155;
        }
        .buyer-account-card {
            border: 1px solid #fdba74;
            border-radius: 12px;
            background: #fed7aa;
            padding: 20px;
        }
        .buyer-account-eyebrow {
            color: #9a3412;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .buyer-account-title {
            margin-top: 8px;
            color: #111827;
            font-size: 18px;
            font-weight: 950;
        }
        .buyer-account-text {
            margin-top: 8px;
            color: #374151;
            font-size: 14px;
            line-height: 1.65;
            font-weight: 600;
        }
        .buyer-profile-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.07);
        }
        .buyer-profile-head {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
        }
        .buyer-avatar {
            display: flex;
            width: 54px;
            height: 54px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #ea580c;
            color: #ffffff;
            font-size: 20px;
            font-weight: 950;
            text-transform: uppercase;
        }
        .buyer-profile-name {
            color: #111827;
            font-size: 16px;
            line-height: 1.3;
            font-weight: 950;
        }
        .buyer-profile-role {
            margin-top: 4px;
            display: inline-flex;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            padding: 4px 9px;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
        }
        .buyer-profile-list {
            margin-top: 16px;
            display: grid;
            gap: 12px;
        }
        .buyer-profile-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .buyer-profile-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .buyer-profile-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
        }
        .buyer-profile-value {
            max-width: 180px;
            color: #111827;
            text-align: right;
            font-size: 13px;
            font-weight: 900;
            overflow-wrap: anywhere;
        }
        .buyer-profile-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }
        .buyer-profile-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #334155;
            padding: 9px 12px;
            text-align: center;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
        }
        .buyer-profile-action:hover {
            border-color: #fdba74;
            background: #fff7ed;
            color: #9a3412;
            transform: translateY(-2px);
        }
        .buyer-profile-action.primary {
            border-color: #ea580c;
            background: #ea580c;
            color: #ffffff;
        }
        .buyer-profile-action.primary:hover {
            background: #c2410c;
            color: #ffffff;
        }
        .buyer-profile-action.danger {
            border-color: #fecaca;
            background: #fee2e2;
            color: #991b1b;
            cursor: pointer;
        }
        .buyer-profile-action.danger:hover {
            background: #fecaca;
            color: #7f1d1d;
        }
        .buyer-profile-actions form {
            display: contents;
        }
        .buyer-chip-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }
        .buyer-chip {
            border: 1px solid #fdba74;
            border-radius: 999px;
            background: #fed7aa;
            color: #9a3412;
            padding: 9px 16px;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .buyer-chip:hover {
            background: #fdba74;
            transform: translateY(-2px);
        }
        .buyer-product-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }
        .buyer-product {
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            color: #111827;
            text-decoration: none;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }
        .buyer-product:hover {
            border-color: #ea580c;
            box-shadow: 0 16px 32px rgba(194, 65, 12, 0.14);
            transform: translateY(-4px);
        }
        .buyer-product-image {
            aspect-ratio: 1 / 1;
            background-color: #ffedd5;
            background-position: center;
            background-size: cover;
        }
        .buyer-product-body {
            padding: 12px;
        }
        .buyer-product-name {
            min-height: 40px;
            color: #111827;
            font-size: 14px;
            line-height: 1.4;
            font-weight: 950;
        }
        .buyer-product-shop {
            margin-top: 4px;
            overflow: hidden;
            color: #64748b;
            font-size: 12px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .buyer-product-price {
            margin-top: 8px;
            color: #c2410c;
            font-weight: 950;
        }
        .buyer-animate {
            animation: buyerEnter 0.45s ease both;
        }
        .buyer-animate-delay {
            animation: buyerEnter 0.55s ease both;
        }
        @keyframes buyerEnter {
            from { transform: translateY(16px); }
            to { transform: translateY(0); }
        }
        @keyframes buyerTap {
            0% { transform: scale(1); }
            50% { transform: scale(0.96); }
            100% { transform: scale(1); }
        }
        @media (max-width: 900px) {
            .buyer-hero-grid,
            .buyer-content-grid {
                grid-template-columns: 1fr;
            }
            .buyer-product-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .buyer-hero-grid {
                padding: 20px;
            }
            .buyer-actions {
                flex-direction: column;
            }
            .buyer-btn {
                width: 100%;
            }
            .buyer-summary-grid,
            .buyer-status-grid,
            .buyer-product-grid {
                grid-template-columns: 1fr;
            }
            .buyer-order-row {
                flex-direction: column;
            }
            .buyer-order-total {
                text-align: left;
            }
        }
    </style>

    <div class="buyer-page">
        <div class="buyer-container buyer-stack">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ session('error') }}</div>
            @endif

            <section class="buyer-hero buyer-animate">
                <div class="buyer-hero-grid">
                    <div>
                        <p class="buyer-welcome">Selamat datang, {{ auth()->user()->name }}</p>
                        <h1 class="buyer-title">Cari produk anggota koperasi dan checkout seperti marketplace modern.</h1>
                        <p class="buyer-desc">Akun pelanggan hanya dipakai untuk belanja. Fitur simpanan, pembiayaan, buka toko, dan upload produk tetap khusus anggota koperasi.</p>
                        <div class="buyer-actions">
                            <a href="{{ route('marketplace') }}" class="buyer-btn buyer-btn-light">Jelajahi Produk</a>
                            <a href="{{ route('marketplace.orders.index') }}" class="buyer-btn buyer-btn-dark">Lihat Pesanan</a>
                        </div>
                    </div>

                    <div class="buyer-summary">
                        <p class="buyer-summary-title">Ringkasan Belanja</p>
                        <div class="buyer-summary-grid">
                            <div class="buyer-summary-card">
                                <p class="buyer-label">Pesanan Aktif</p>
                                <p class="buyer-number">{{ $stats['aktif'] ?? 0 }}</p>
                            </div>
                            <div class="buyer-summary-card green">
                                <p class="buyer-label">Keranjang</p>
                                <p class="buyer-number green">{{ $cartCount ?? 0 }}</p>
                            </div>
                            <div class="buyer-summary-card blue">
                                <p class="buyer-label">Total Belanja</p>
                                <p class="buyer-number blue">{{ $rupiah($stats['total_belanja'] ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="buyer-status-grid buyer-animate-delay">
                @foreach($statusSteps as $step)
                    <a href="{{ route('marketplace.orders.index') }}" class="buyer-status-card">
                        <div class="buyer-status-top">
                            <div class="buyer-icon">
                                @if($step['icon'] === 'wallet')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4Zm0 0h-5a2 2 0 0 0 0 4h5"/></svg>
                                @elseif($step['icon'] === 'box')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 8-9-5-9 5 9 5 9-5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 8v8l9 5 9-5V8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 13v8"/></svg>
                                @elseif($step['icon'] === 'truck')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM18 21a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                @endif
                            </div>
                            <span class="buyer-status-value">{{ $step['value'] }}</span>
                        </div>
                        <p class="buyer-status-label">{{ $step['label'] }}</p>
                    </a>
                @endforeach
            </section>

            <section class="buyer-content-grid">
                <div class="buyer-panel">
                    <div class="buyer-panel-head">
                        <div>
                            <h3 class="buyer-panel-title">Pesanan Terbaru</h3>
                            <p class="buyer-panel-sub">Pantau status pesanan seperti di platform belanja online.</p>
                        </div>
                        <a href="{{ route('marketplace.orders.index') }}" class="buyer-link">Lihat Semua</a>
                    </div>

                    @if($pesananTerbaru->isEmpty())
                        <div class="buyer-empty">
                            <p class="buyer-empty-title">Belum ada pesanan.</p>
                            <p class="buyer-empty-text">Produk yang Anda checkout akan muncul di sini.</p>
                            <a href="{{ route('marketplace') }}" class="buyer-btn buyer-btn-light" style="margin-top: 16px; background: #ea580c; color: #ffffff;">Mulai Belanja</a>
                        </div>
                    @else
                        <div>
                            @foreach($pesananTerbaru as $pesanan)
                                <div class="buyer-order">
                                    <div class="buyer-order-row">
                                        <div>
                                            <p class="buyer-order-code">{{ $pesanan->nomor_pesanan }}</p>
                                            <p class="buyer-order-meta">{{ $pesanan->toko->nama_toko ?? 'Toko Anggota' }} &middot; {{ $pesanan->created_at->translatedFormat('d M Y H:i') }}</p>
                                            <p class="buyer-order-items">{{ $pesanan->items->pluck('nama_produk_snapshot')->filter()->take(2)->implode(', ') ?: 'Produk pesanan' }}</p>
                                        </div>
                                        <div class="buyer-order-total">
                                            <p>{{ $rupiah($pesanan->total) }}</p>
                                            <span class="buyer-badge {{ $statusClass[$pesanan->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ str_replace('_', ' ', $pesanan->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="buyer-side">
                    <div class="buyer-panel buyer-panel-padded">
                        <h3 class="buyer-panel-title">Menu Belanja</h3>
                        <div class="buyer-menu-grid">
                            <a href="{{ route('marketplace') }}" class="buyer-menu-card buyer-menu-orange">Katalog</a>
                            <a href="{{ route('marketplace.cart') }}" class="buyer-menu-card buyer-menu-green">Keranjang</a>
                            <a href="{{ route('marketplace.orders.index') }}" class="buyer-menu-card buyer-menu-blue">Pesanan</a>
                            <a href="{{ route('inbox.index') }}" class="buyer-menu-card buyer-menu-slate relative">
                                Notifikasi
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold text-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <div class="buyer-profile-card">
                        <div class="buyer-profile-head">
                            <div class="buyer-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                            <div>
                                <h3 class="buyer-profile-name">{{ auth()->user()->name }}</h3>
                                <span class="buyer-profile-role">Pelanggan</span>
                            </div>
                        </div>

                        <div class="buyer-profile-list">
                            <div class="buyer-profile-row">
                                <span class="buyer-profile-label">Email</span>
                                <span class="buyer-profile-value">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="buyer-profile-row">
                                <span class="buyer-profile-label">No. HP</span>
                                <span class="buyer-profile-value">{{ auth()->user()->no_hp ?: '-' }}</span>
                            </div>
                            <div class="buyer-profile-row">
                                <span class="buyer-profile-label">Status Akun</span>
                                <span class="buyer-profile-value">{{ auth()->user()->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                            <div class="buyer-profile-row">
                                <span class="buyer-profile-label">Tipe Akun</span>
                                <span class="buyer-profile-value">Belanja Marketplace</span>
                            </div>
                        </div>

                        <div class="buyer-profile-actions">
                            <a href="{{ route('profile.edit') }}" class="buyer-profile-action primary">Edit Profil</a>
                            <a href="{{ route('marketplace.orders.index') }}" class="buyer-profile-action">Pesanan Saya</a>
                            <a href="{{ route('marketplace.cart') }}" class="buyer-profile-action">Keranjang</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="buyer-profile-action danger">Keluar</button>
                            </form>
                        </div>
                    </div>

                    <div class="buyer-account-card">
                        <p class="buyer-account-eyebrow">Akun Belanja</p>
                        <h3 class="buyer-account-title">Pelanggan Marketplace</h3>
                        <p class="buyer-account-text">Akun ini cocok untuk pembeli umum. Anda bisa belanja tanpa menjadi anggota koperasi.</p>
                    </div>
                </aside>
            </section>

            <section class="buyer-panel buyer-panel-padded">
                <div class="buyer-panel-head" style="padding: 0 0 16px;">
                    <div>
                        <h3 class="buyer-panel-title">Kategori Pilihan</h3>
                        <p class="buyer-panel-sub">Temukan produk berdasarkan kategori yang tersedia.</p>
                    </div>
                    <a href="{{ route('marketplace') }}" class="buyer-link">Lihat Marketplace</a>
                </div>

                @if($kategoriList->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed border-gray-200 bg-gray-50 p-5 text-center text-sm font-semibold text-gray-500">
                        Kategori akan tampil setelah produk aktif tersedia.
                    </div>
                @else
                    <div class="buyer-chip-wrap">
                        @foreach($kategoriList as $kategori)
                            <a href="{{ route('marketplace', ['kategori' => $kategori]) }}" class="buyer-chip">{{ $kategori }}</a>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="buyer-panel buyer-panel-padded">
                <div class="buyer-panel-head" style="padding: 0 0 16px;">
                    <div>
                        <h3 class="buyer-panel-title">Rekomendasi Produk</h3>
                        <p class="buyer-panel-sub">Produk aktif terbaru dari toko anggota koperasi.</p>
                    </div>
                    <a href="{{ route('marketplace') }}" class="buyer-link">Belanja Lagi</a>
                </div>

                @if($recommendedProducts->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm font-semibold text-gray-500">
                        Rekomendasi produk masih kosong.
                    </div>
                @else
                    <div class="buyer-product-grid">
                        @foreach($recommendedProducts as $product)
                            <a href="{{ route('marketplace.show', $product->slug) }}" class="buyer-product">
                                <div class="buyer-product-image" style="background-image: url('{{ $product->foto_url ?: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=500&q=80' }}');"></div>
                                <div class="buyer-product-body">
                                    <p class="buyer-product-name">{{ $product->nama }}</p>
                                    <p class="buyer-product-shop">{{ $product->toko?->nama_toko ?? 'Toko Anggota' }}</p>
                                    <p class="buyer-product-price">{{ $rupiah($product->harga) }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
    <script>
        document.querySelectorAll('.buyer-btn, .buyer-status-card, .buyer-menu-card, .buyer-chip, .buyer-product, .buyer-profile-card, .buyer-profile-action').forEach((element) => {
            element.addEventListener('click', () => {
                element.classList.remove('buyer-is-tapped');
                void element.offsetWidth;
                element.classList.add('buyer-is-tapped');
            });
        });
    </script>
</x-app-layout>
