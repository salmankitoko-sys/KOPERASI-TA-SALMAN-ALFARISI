<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - SIPDKS</title>
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
        h1 { margin: 0; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.15; }
        .subtext { color: var(--muted); margin-top: 10px; line-height: 1.7; }
        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 22px;
            margin-top: 28px;
            align-items: start;
        }
        .panel {
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(29,106,74,0.1);
            border-radius: 24px;
            box-shadow: 0 18px 48px rgba(29,106,74,0.08);
            padding: 22px;
        }
        .section-title { margin: 0 0 16px; font-size: 1.15rem; font-weight: 900; }
        label { display: block; color: var(--teks); font-size: 0.86rem; font-weight: 800; margin-bottom: 8px; }
        textarea, select {
            width: 100%;
            border: 1px solid rgba(29,106,74,0.2);
            border-radius: 14px;
            padding: 12px 14px;
            background: white;
            color: var(--teks);
            font: inherit;
        }
        textarea { min-height: 120px; resize: vertical; }
        .field { margin-bottom: 18px; }
        .help { color: var(--muted); font-size: 0.8rem; margin-top: 7px; line-height: 1.5; }
        .error { color: #991b1b; font-size: 0.8rem; font-weight: 700; margin-top: 7px; }
        .summary { position: sticky; top: 20px; }
        .summary-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px solid rgba(29,106,74,0.08);
        }
        .item-name { font-weight: 900; }
        .item-meta { color: var(--muted); font-size: 0.8rem; margin-top: 3px; }
        .summary-row, .summary-total {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 14px;
        }
        .summary-row { color: var(--muted); }
        .summary-total {
            border-top: 1px solid rgba(29,106,74,0.12);
            padding-top: 16px;
            font-size: 1.15rem;
            font-weight: 900;
        }
        .summary-total span:last-child { color: var(--hijau); }
        .summary .btn { width: 100%; margin-top: 18px; }
        .alert {
            margin-bottom: 18px;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 700;
            font-size: 0.9rem;
        }
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
        }
    </style>
</head>
<body>
    @php
        $selectedShipping = old('metode_pengiriman', 'ambil_di_toko');
        $shippingFee = $shippingOptions[$selectedShipping]['fee'] ?? 0;
        $grandTotal = $subtotal + $shippingFee;
    @endphp

    <div class="container">
        <div class="topbar">
            <a href="{{ route('home') }}" class="brand">SIPDKS</a>
            <div class="nav">
                <a href="{{ route('marketplace.cart') }}" class="btn btn-outline">Keranjang</a>
                <a href="{{ route('marketplace') }}" class="btn btn-solid">Marketplace</a>
            </div>
        </div>

        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <h1>Checkout</h1>
        <p class="subtext">Lengkapi alamat, pengiriman, dan metode pembayaran untuk membuat pesanan.</p>

        <form method="POST" action="{{ route('marketplace.checkout.store') }}" class="layout">
            @csrf
            <section class="panel">
                <h2 class="section-title">Informasi Pengiriman</h2>

                <div class="field">
                    <label for="alamat_kirim">Alamat pengiriman</label>
                    <textarea id="alamat_kirim" name="alamat_kirim" placeholder="Tulis alamat lengkap, nomor HP, dan patokan lokasi.">{{ old('alamat_kirim') }}</textarea>
                    @error('alamat_kirim')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="metode_pengiriman">Metode pengiriman</label>
                    <select id="metode_pengiriman" name="metode_pengiriman">
                        @foreach($shippingOptions as $value => $option)
                            <option value="{{ $value }}" data-fee="{{ $option['fee'] }}" @selected(old('metode_pengiriman', 'ambil_di_toko') === $value)>
                                {{ $option['label'] }} - Rp {{ number_format($option['fee'], 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="help">Total pembayaran akan mengikuti metode pengiriman yang dipilih.</div>
                    @error('metode_pengiriman')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="metode_pembayaran">Metode pembayaran</label>
                    <select id="metode_pembayaran" name="metode_pembayaran">
                        @foreach($paymentOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('metode_pembayaran', 'transfer_manual') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="help">Transfer manual: bayar ke rekening toko, penjual akan segera proses. Bayar di tempat: bayar saat barang diterima. QRIS: bayar langsung dengan scan QR.</div>
                    @error('metode_pembayaran')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="catatan_pembeli">Catatan untuk penjual</label>
                    <textarea id="catatan_pembeli" name="catatan_pembeli" placeholder="Opsional, misalnya warna/varian atau waktu pengantaran.">{{ old('catatan_pembeli') }}</textarea>
                    @error('catatan_pembeli')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </section>

            <aside class="panel summary">
                <h2 class="section-title">Ringkasan Belanja</h2>

                @foreach($cartItems as $item)
                    @php($produk = $item['produk'])
                    <div class="summary-item">
                        <div>
                            <div class="item-name">{{ $produk->nama }}</div>
                            <div class="item-meta">{{ $item['qty'] }} x Rp {{ number_format((float) $produk->harga, 0, ',', '.') }}</div>
                        </div>
                        <strong>Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</strong>
                    </div>
                @endforeach

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong>Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row">
                    <span>Estimasi pengiriman</span>
                    <strong id="shippingFeeText">Rp {{ number_format((float) $shippingFee, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span id="grandTotalText">Rp {{ number_format((float) $grandTotal, 0, ',', '.') }}</span>
                </div>

                <button type="submit" class="btn btn-solid">Buat Pesanan</button>
                <a href="{{ route('marketplace.cart') }}" class="btn btn-outline">Kembali ke Keranjang</a>
            </aside>
        </form>
    </div>
    <script>
        const subtotal = {{ (float) $subtotal }};
        const shippingSelect = document.getElementById('metode_pengiriman');
        const shippingFeeText = document.getElementById('shippingFeeText');
        const grandTotalText = document.getElementById('grandTotalText');
        const rupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

        shippingSelect?.addEventListener('change', () => {
            const fee = Number(shippingSelect.selectedOptions[0]?.dataset.fee || 0);
            shippingFeeText.textContent = `Rp ${rupiah(fee)}`;
            grandTotalText.textContent = `Rp ${rupiah(subtotal + fee)}`;
        });
    </script>
</body>
</html>
