<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $produk->nama }} - Marketplace SIPDKS</title>
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
            background: linear-gradient(135deg, rgba(232,245,239,0.72), rgba(250,250,248,0.92)), var(--putih);
            color: var(--teks);
        }
        a { text-decoration: none; }
        .container { max-width: 1180px; margin: 0 auto; padding: 32px 20px 64px; }
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
        .product-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.82fr);
            gap: 30px;
            align-items: start;
        }
        .product-photo {
            min-height: 480px;
            border-radius: 30px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 22px 60px rgba(29,106,74,0.18);
        }
        .detail-card {
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(29,106,74,0.1);
            border-radius: 26px;
            padding: 26px;
            box-shadow: 0 18px 48px rgba(29,106,74,0.1);
        }
        .eyebrow {
            color: var(--hijau);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        h1 { margin: 0; font-size: clamp(2rem, 4vw, 3.1rem); line-height: 1.12; }
        .desc { color: var(--muted); line-height: 1.8; margin: 16px 0 0; }
        .price { color: var(--hijau); font-size: 1.7rem; font-weight: 900; margin-top: 22px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 22px 0; }
        .meta-box {
            border-radius: 16px;
            background: var(--hijau-pale);
            padding: 14px;
        }
        .meta-label { color: var(--muted); font-size: 0.75rem; font-weight: 700; }
        .meta-value { color: var(--teks); font-size: 0.95rem; font-weight: 900; margin-top: 4px; }
        .form-row { display: grid; grid-template-columns: 110px 1fr; gap: 12px; margin-top: 20px; }
        input[type="number"] {
            width: 100%;
            border: 1px solid rgba(29,106,74,0.2);
            border-radius: 12px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 800;
        }
        .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 14px; }
        .related { margin-top: 42px; }
        .related h2 { margin: 0 0 16px; font-size: 1.45rem; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .related-card {
            background: white;
            border: 1px solid rgba(29,106,74,0.08);
            border-radius: 18px;
            padding: 16px;
        }
        .related-name { color: var(--teks); font-weight: 900; }
        .related-shop { color: var(--muted); font-size: 0.82rem; margin-top: 4px; }
        .related-price { color: var(--hijau); font-weight: 900; margin-top: 12px; }
        .alert {
            margin-bottom: 18px;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @media (max-width: 860px) {
            .product-layout { grid-template-columns: 1fr; }
            .product-photo { min-height: 340px; }
        }
        @media (max-width: 640px) {
            .container { padding: 24px 14px 48px; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .nav { width: 100%; }
            .btn { flex: 1; padding: 10px 12px; }
            .actions, .form-row, .meta-grid { grid-template-columns: 1fr; }
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

        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="product-layout">
            <div class="product-photo" style="background-image: linear-gradient(135deg, rgba(232,245,239,0.22), rgba(29,106,74,0.08)), url('{{ $produk->foto_url ?: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80' }}');"></div>

            <section class="detail-card">
                <div class="eyebrow">{{ $produk->kategori ?? 'Produk Anggota' }}</div>
                <h1>{{ $produk->nama }}</h1>
                <p class="desc">{{ $produk->deskripsi ?: 'Produk dari anggota koperasi yang siap dibeli oleh masyarakat umum.' }}</p>
                <div class="price">Rp {{ number_format((float) $produk->harga, 0, ',', '.') }}</div>

                <div class="meta-grid">
                    <div class="meta-box">
                        <div class="meta-label">Toko</div>
                        <div class="meta-value">{{ $produk->toko?->nama_toko ?? 'Toko Anggota' }}</div>
                    </div>
                    <div class="meta-box">
                        <div class="meta-label">Stok</div>
                        <div class="meta-value">{{ $produk->stok }} tersedia</div>
                    </div>
                    <div class="meta-box">
                        <div class="meta-label">Akad</div>
                        <div class="meta-value">{{ ucfirst($produk->akad ?? 'murabahah') }}</div>
                    </div>
                    <div class="meta-box">
                        <div class="meta-label">Status</div>
                        <div class="meta-value">Ready</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('marketplace.cart.add', $produk->id) }}">
                    @csrf
                    <div class="form-row">
                        <input type="number" name="qty" min="1" max="{{ max(1, $produk->stok) }}" value="1">
                        <button type="submit" class="btn btn-solid">Tambah ke Keranjang</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('marketplace.cart.add', $produk->id) }}" class="actions">
                    @csrf
                    <input type="hidden" name="qty" value="1">
                    <input type="hidden" name="redirect_to" value="checkout">
                    <a href="{{ route('marketplace') }}" class="btn btn-outline">Kembali</a>
                    <button type="submit" class="btn btn-solid">Beli Sekarang</button>
                </form>
            </section>
        </div>

        @if($relatedProducts->count())
            <section class="related">
                <h2>Produk terkait</h2>
                <div class="related-grid">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('marketplace.show', $related->slug) }}" class="related-card">
                            <div class="related-name">{{ $related->nama }}</div>
                            <div class="related-shop">{{ $related->toko?->nama_toko ?? 'Toko Anggota' }}</div>
                            <div class="related-price">Rp {{ number_format((float) $related->harga, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</body>
</html>
