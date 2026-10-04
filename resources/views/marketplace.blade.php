<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marketplace Koperasi</title>
    <style>
        :root {
            --hijau: #1D6A4A;
            --hijau-muda: #2D9B6A;
            --hijau-pale: #E8F5EF;
            --emas: #C49A2A;
            --putih: #FAFAF8;
            --teks: #1A1A18;
            --muted: #5C5C58;
            --abu: #F2F0EB;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                linear-gradient(135deg, rgba(232,245,239,0.72), rgba(250,250,248,0.88) 42%, rgba(240,200,74,0.12)),
                var(--putih);
            color: var(--teks);
        }
        a { text-decoration: none; }
        .container { max-width: 1180px; margin: 0 auto; padding: 32px 20px 64px; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center; gap: 16px;
            padding: 18px 0 30px;
            border-bottom: 1px solid rgba(29, 106, 74, 0.14);
            margin-bottom: 32px;
        }
        .brand {
            font-size: 1.6rem; font-weight: 800; color: var(--hijau);
        }
        .nav {
            display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; padding: 10px 18px; font-weight: 700;
            transition: 0.2s ease;
        }
        .btn-outline {
            border: 1.5px solid rgba(29,106,74,0.25); color: var(--hijau); background: transparent;
        }
        .btn-solid {
            background: var(--hijau); color: white; box-shadow: 0 10px 24px rgba(29, 106, 74, 0.15);
        }
        .btn:hover { opacity: 0.95; transform: translateY(-1px); }

        .hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(280px, 0.95fr);
            gap: 32px;
            align-items: stretch;
            margin-bottom: 34px;
        }
        .eyebrow {
            color: var(--hijau); font-weight: 700; font-size: 0.8rem; letter-spacing: 0.12em; text-transform: uppercase;
            margin-bottom: 12px;
        }
        h1 {
            margin: 0; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.15;
        }
        .subtext {
            color: var(--muted); margin-top: 10px; max-width: 650px;
            line-height: 1.7;
        }
        .hero-card {
            min-height: 250px;
            border-radius: 28px;
            padding: 22px;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.78), rgba(255,255,255,0.48)),
                url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1100&q=80');
            background-size: cover;
            background-position: center;
            box-shadow: 0 24px 65px rgba(29,106,74,0.16);
            display: flex;
            align-items: flex-end;
        }
        .hero-card-panel {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }
        .hero-mini {
            border-radius: 14px;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.62);
            padding: 13px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .hero-mini strong {
            display: block;
            color: var(--hijau);
            font-size: 1rem;
            line-height: 1.25;
        }
        .hero-mini span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 0.72rem;
            line-height: 1.45;
        }

        .catalog-section {
            border-radius: 28px;
            border: 1px solid rgba(29,106,74,0.1);
            background: rgba(255,255,255,0.72);
            box-shadow: 0 18px 55px rgba(29,106,74,0.08);
            padding: 24px;
        }
        .catalog-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .catalog-title {
            margin: 0;
            font-size: clamp(1.45rem, 3vw, 2rem);
            line-height: 1.2;
        }
        .catalog-sub {
            color: var(--muted);
            margin: 8px 0 0;
            line-height: 1.65;
            max-width: 620px;
        }
        .category-filter {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .category-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 13px;
            border-radius: 999px;
            border: 1px solid rgba(29,106,74,0.16);
            color: var(--hijau);
            background: rgba(255,255,255,0.82);
            font-size: 0.82rem;
            font-weight: 800;
            transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
        }
        .category-chip:hover { transform: translateY(-2px); }
        .category-chip.active {
            background: var(--hijau);
            color: white;
            border-color: var(--hijau);
        }

        .products-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px; margin-top: 26px;
        }
        .product-card {
            background: white; border-radius: 22px; border: 1px solid rgba(29,106,74,0.08);
            overflow: hidden; box-shadow: 0 12px 28px rgba(29,106,74,0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: rgba(45,155,106,0.32);
            box-shadow: 0 20px 42px rgba(29,106,74,0.14);
        }
        .product-image {
            position: relative;
            height: 190px; background-size: cover; background-position: center;
            background-color: var(--hijau-pale);
        }
        .product-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.38));
        }
        .product-body { padding: 18px; }
        .meta {
            display: flex; justify-content: space-between; align-items: center; gap: 8px; font-size: 0.72rem;
            color: var(--muted); margin-bottom: 10px;
        }
        .badge {
            padding: 5px 8px; border-radius: 999px; background: var(--hijau-pale); color: var(--hijau);
            font-weight: 700;
        }
        .product-name {
            margin: 0 0 8px; font-size: 1.15rem; font-weight: 800;
        }
        .product-desc {
            margin: 0; color: var(--muted); font-size: 0.9rem; line-height: 1.7;
        }
        .product-footer {
            display: flex; justify-content: space-between; align-items: center; gap: 10px;
            margin-top: 18px;
            padding-top: 14px; border-top: 1px solid rgba(0,0,0,0.06);
        }
        .product-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 16px;
        }
        .card-btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 800;
            font-size: 0.82rem;
            cursor: pointer;
            text-align: center;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .card-btn:hover { transform: translateY(-1px); }
        .product-actions form { display: flex; }
        .card-btn.outline {
            background: var(--hijau-pale);
            color: var(--hijau);
        }
        .card-btn.solid {
            background: var(--hijau);
            color: white;
        }
        .price {
            font-weight: 800; color: var(--hijau);
            font-size: 1.05rem;
        }
        .shop-name {
            font-size: 0.8rem; color: var(--muted); text-align: right;
        }
        .empty-state {
            margin-top: 22px;
            background: rgba(232,245,239,0.62); border: 1px dashed rgba(29,106,74,0.25); border-radius: 18px;
            padding: 24px 20px; text-align: center; color: var(--muted);
            font-weight: 600;
        }
        .empty-catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-top: 22px;
        }
        .catalog-slot {
            min-height: 300px;
            border-radius: 22px;
            border: 1.5px dashed rgba(29,106,74,0.2);
            background: linear-gradient(180deg, rgba(255,255,255,0.86), rgba(250,250,248,0.72));
            padding: 14px;
        }
        .slot-image {
            height: 160px;
            border-radius: 16px;
            background:
                linear-gradient(135deg, rgba(232,245,239,0.9), rgba(240,200,74,0.18)),
                repeating-linear-gradient(45deg, rgba(29,106,74,0.08) 0 1px, transparent 1px 14px);
            border: 1px solid rgba(29,106,74,0.08);
        }
        .slot-line {
            height: 12px;
            border-radius: 999px;
            background: rgba(29,106,74,0.1);
            margin-top: 16px;
        }
        .slot-line.short {
            width: 58%;
        }
        .slot-line.medium {
            width: 76%;
        }
        .slot-footer {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 22px;
            padding-top: 14px;
            border-top: 1px solid rgba(29,106,74,0.08);
        }
        .slot-pill {
            width: 84px;
            height: 28px;
            border-radius: 999px;
            background: rgba(29,106,74,0.1);
        }
        .slot-pill.small {
            width: 56px;
        }
        .pagination {
            display: flex; justify-content: center; margin-top: 28px; flex-wrap: wrap; gap: 8px;
        }
        .pagination a, .pagination span {
            display: inline-flex; align-items:center; justify-content:center; min-width: 38px; min-height: 38px;
            padding: 8px 12px; border-radius: 10px; border: 1px solid rgba(29,106,74,0.18); background: white;
            color: var(--hijau); font-weight: 700;
        }
        .pagination .current { background: var(--hijau); color: white; border-color: var(--hijau); }
        .alert {
            margin-bottom: 18px;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .container { padding: 24px 14px 48px; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .nav { width: 100%; }
            .btn { flex: 1; padding: 10px 12px; }
            .hero-card { min-height: 360px; padding: 14px; }
            .hero-card-panel { grid-template-columns: 1fr; }
            .catalog-section { padding: 18px; border-radius: 22px; }
            .category-filter { width: 100%; }
            .category-chip { flex: 1; }
            .product-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div class="brand">SIPDKS</div>
            <div class="nav">
                <a href="{{ route('home') }}" class="btn btn-outline">Beranda</a>
                <a href="{{ route('marketplace') }}" class="btn btn-solid">Marketplace</a>
                <a href="{{ route('marketplace.cart') }}" class="btn btn-outline">Keranjang</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="btn btn-outline">
                            {{ auth()->user()->isPelanggan() ? 'Pesanan Saya' : 'Dashboard Saya' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                        <a href="{{ route('register', ['as' => 'pelanggan']) }}" class="btn btn-outline">Daftar Belanja</a>
                    @endauth
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="hero">
            <div>
                <div class="eyebrow">Marketplace Anggota</div>
                <h1>Belanja kebutuhan sehari-hari dari produk anggota koperasi.</h1>
                <p class="subtext">Temukan produk lokal, ramah keluarga, dan rutin dipasarkan oleh sesama anggota koperasi syariah.</p>
            </div>
            <div class="hero-card" aria-hidden="true">
                <div class="hero-card-panel">
                    <div class="hero-mini">
                        <strong>Toko</strong>
                        <span>Etalase usaha anggota.</span>
                    </div>
                    <div class="hero-mini">
                        <strong>Katalog</strong>
                        <span>Produk siap ditampilkan.</span>
                    </div>
                    <div class="hero-mini">
                        <strong>Transaksi</strong>
                        <span>Belanja lebih tertata.</span>
                    </div>
                </div>
            </div>
        </div>

        <section class="catalog-section">
            <div class="catalog-head">
                <div>
                    <div class="eyebrow">Katalog Produk</div>
                    <h2 class="catalog-title">Etalase produk anggota</h2>
                    <p class="catalog-sub">Area ini disiapkan sebagai tempat katalog produk. Saat anggota mengunggah produk aktif, katalog akan otomatis terisi di sini.</p>
                </div>

                @if(isset($kategoriList) && $kategoriList->count())
                    <div class="category-filter">
                        <a href="{{ route('marketplace') }}" class="category-chip {{ empty($kategori) ? 'active' : '' }}">Semua</a>
                        @foreach($kategoriList as $k)
                            <a href="{{ route('marketplace', ['kategori' => $k]) }}" class="category-chip {{ (isset($kategori) && $kategori == $k) ? 'active' : '' }}">{{ $k }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($products->count())
                <div class="products-grid">
                    @foreach($products as $product)
                        <article class="product-card">
                            <div class="product-image" style="background-image: linear-gradient(135deg, rgba(232,245,239,0.35), rgba(29,106,74,0.08)), url('{{ $product->foto_url ?: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80' }}');"></div>
                            <div class="product-body">
                                <div class="meta">
                                    <span class="badge">{{ $product->kategori ?? 'Umum' }}</span>
                                    <span>{{ $product->stok }} stok</span>
                                </div>
                                <h2 class="product-name">{{ $product->nama }}</h2>
                                <p class="product-desc">{{ $product->deskripsi ?: 'Produk dari anggota koperasi yang siap dibeli oleh masyarakat umum.' }}</p>
                                <div class="product-footer">
                                    <div class="price">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</div>
                                    <div class="shop-name">{{ $product->toko?->nama_toko ?? 'Toko Anggota' }}</div>
                                </div>
                                <div class="product-actions">
                                    <a href="{{ route('marketplace.show', $product->slug) }}" class="card-btn outline">Detail</a>
                                    <form method="POST" action="{{ route('marketplace.cart.add', $product->id) }}">
                                        @csrf
                                        <input type="hidden" name="qty" value="1">
                                        <button class="card-btn solid" type="submit">+ Keranjang</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="pagination">
                    {{ $products->links() }}
                </div>
            @else
                <div class="empty-catalog-grid">
                    @for($i = 0; $i < 8; $i++)
                        <article class="catalog-slot" aria-label="Slot katalog produk kosong">
                            <div class="slot-image"></div>
                            <div class="slot-line medium"></div>
                            <div class="slot-line short"></div>
                            <div class="slot-footer">
                                <div class="slot-pill"></div>
                                <div class="slot-pill small"></div>
                            </div>
                        </article>
                    @endfor
                </div>
                <div class="empty-state">
                    Katalog produk masih kosong. Produk akan tampil otomatis setelah anggota mengunggah dan produk disetujui.
                </div>
            @endif
        </section>
    </div>
</body>
</html>
