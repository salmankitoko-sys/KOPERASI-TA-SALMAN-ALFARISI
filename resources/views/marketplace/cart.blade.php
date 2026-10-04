<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - SIPDKS</title>
    <style>
        :root {
            --hijau: #1D6A4A;
            --hijau-pale: #E8F5EF;
            --putih: #FAFAF8;
            --teks: #1A1A18;
            --muted: #5C5C58;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, rgba(232,245,239,0.72), rgba(250,250,248,0.92)), var(--putih);
            color: var(--teks);
        }
        a { text-decoration: none; }
        .container { max-width: 1080px; margin: 0 auto; padding: 32px 20px 64px; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center; gap: 16px;
            padding: 18px 0 30px;
            border-bottom: 1px solid rgba(29,106,74,0.14);
            margin-bottom: 32px;
        }
        .brand { font-size: 1.6rem; font-weight: 800; color: var(--hijau); }
        .nav { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; padding: 10px 18px; font-weight: 800;
            border: 0; cursor: pointer; transition: 0.2s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-outline { border: 1.5px solid rgba(29,106,74,0.25); color: var(--hijau); background: transparent; }
        .btn-solid { background: var(--hijau); color: white; box-shadow: 0 10px 24px rgba(29,106,74,0.15); }
        .btn-danger { background: #fee2e2; color: #991b1b; }
        h1 { margin: 0; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.15; }
        .subtext { color: var(--muted); margin-top: 10px; line-height: 1.7; }
        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 22px;
            margin-top: 28px;
            align-items: start;
        }
        .panel {
            background: rgba(255,255,255,0.86);
            border: 1px solid rgba(29,106,74,0.1);
            border-radius: 24px;
            box-shadow: 0 18px 48px rgba(29,106,74,0.08);
            overflow: hidden;
        }
        .cart-item {
            display: grid;
            grid-template-columns: 100px minmax(0, 1fr) auto;
            gap: 16px;
            padding: 18px;
            border-bottom: 1px solid rgba(29,106,74,0.08);
        }
        .cart-item:last-child { border-bottom: 0; }
        .thumb {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            background-size: cover;
            background-position: center;
            background-color: var(--hijau-pale);
        }
        .name { color: var(--teks); font-weight: 900; font-size: 1.05rem; }
        .shop { color: var(--muted); font-size: 0.82rem; margin-top: 4px; }
        .price { color: var(--hijau); font-weight: 900; margin-top: 12px; }
        .qty-form { display: flex; align-items: center; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
        input[type="number"] {
            width: 76px;
            border: 1px solid rgba(29,106,74,0.2);
            border-radius: 10px;
            padding: 9px;
            font-weight: 800;
        }
        .item-total { text-align: right; min-width: 140px; }
        .item-total strong { display: block; color: var(--hijau); font-size: 1.05rem; }
        .summary { padding: 22px; position: sticky; top: 20px; }
        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            margin-bottom: 12px;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-top: 1px solid rgba(29,106,74,0.12);
            padding-top: 16px;
            margin-top: 16px;
            font-size: 1.15rem;
            font-weight: 900;
        }
        .summary-total span:last-child { color: var(--hijau); }
        .summary .btn { width: 100%; margin-top: 16px; }
        .empty {
            margin-top: 28px;
            padding: 48px 22px;
            border: 1px dashed rgba(29,106,74,0.25);
            border-radius: 22px;
            background: rgba(255,255,255,0.74);
            text-align: center;
            color: var(--muted);
            font-weight: 700;
        }
        .alert {
            margin-bottom: 18px;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @media (max-width: 860px) {
            .layout { grid-template-columns: 1fr; }
            .summary { position: static; }
        }
        @media (max-width: 640px) {
            .container { padding: 24px 14px 48px; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .nav { width: 100%; }
            .btn { flex: 1; padding: 10px 12px; }
            .cart-item { grid-template-columns: 80px 1fr; }
            .thumb { width: 80px; height: 80px; }
            .item-total { grid-column: 1 / -1; text-align: left; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <a href="{{ route('home') }}" class="brand">SIPDKS</a>
            <div class="nav">
                <a href="{{ route('marketplace') }}" class="btn btn-outline">Marketplace</a>
                <a href="{{ route('marketplace.cart') }}" class="btn btn-solid">Keranjang</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <h1>Keranjang Belanja</h1>
        <p class="subtext">Periksa produk, jumlah, dan subtotal sebelum melanjutkan ke checkout.</p>

        @if($cartItems->isEmpty())
            <div class="empty">
                Keranjang masih kosong.
                <div style="margin-top: 18px;">
                    <a href="{{ route('marketplace') }}" class="btn btn-solid">Mulai Belanja</a>
                </div>
            </div>
        @else
            <div class="layout">
                <section class="panel">
                    @foreach($cartItems as $item)
                        @php($produk = $item['produk'])
                        <article class="cart-item">
                            <div class="thumb" style="background-image: url('{{ $produk->foto_url ?: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80' }}');"></div>
                            <div>
                                <a href="{{ route('marketplace.show', $produk->slug) }}" class="name">{{ $produk->nama }}</a>
                                <div class="shop">{{ $produk->toko?->nama_toko ?? 'Toko Anggota' }} &middot; Stok {{ $produk->stok }}</div>
                                <div class="price">Rp {{ number_format((float) $produk->harga, 0, ',', '.') }}</div>
                                <form method="POST" action="{{ route('marketplace.cart.update', $produk->id) }}" class="qty-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="qty" min="1" max="{{ max(1, $produk->stok) }}" value="{{ $item['qty'] }}">
                                    <button class="btn btn-outline" type="submit">Update</button>
                                </form>
                            </div>
                            <div class="item-total">
                                <strong>Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</strong>
                                <form method="POST" action="{{ route('marketplace.cart.remove', $produk->id) }}" style="margin-top: 12px;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </section>

                <aside class="panel summary">
                    <div class="summary-row">
                        <span>Total item</span>
                        <strong>{{ $cartItems->sum('qty') }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong>Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('marketplace.checkout') }}" class="btn btn-solid">Lanjut Checkout</a>
                    <a href="{{ route('marketplace') }}" class="btn btn-outline">Tambah Produk</a>
                </aside>
            </div>
        @endif
    </div>
</body>
</html>
