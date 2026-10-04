<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Pesanan - SIPDKS</title>
    <style>
        :root {
            --hijau: #1D6A4A;
            --hijau-pale: #E8F5EF;
            --putih: #FAFAF8;
            --teks: #1A1A18;
            --muted: #5C5C58;
            --amber: #D97706;
            --amber-bg: #FEF3C7;
            --amber-border: #FDE68A;
            --blue: #2563EB;
            --blue-bg: #DBEAFE;
            --blue-border: #BFDBFE;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, rgba(232,245,239,0.72), rgba(250,250,248,0.92)), var(--putih);
            color: var(--teks);
        }
        a { text-decoration: none; }
        .container { max-width: 720px; margin: 0 auto; padding: 32px 20px 64px; }
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
            font-size: 0.88rem;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-outline { border: 1.5px solid rgba(29,106,74,0.25); color: var(--hijau); background: transparent; }
        .btn-solid { background: var(--hijau); color: white; box-shadow: 0 10px 24px rgba(29,106,74,0.15); }
        .btn-amber { background: var(--amber); color: white; }
        h1 { margin: 0; font-size: 1.8rem; line-height: 1.2; }
        .subtext { color: var(--muted); margin-top: 8px; line-height: 1.7; font-size: 0.92rem; }
        .panel {
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(29,106,74,0.1);
            border-radius: 24px;
            box-shadow: 0 18px 48px rgba(29,106,74,0.08);
            padding: 24px;
            margin-bottom: 20px;
        }
        .section-title { margin: 0 0 16px; font-size: 1.1rem; font-weight: 900; }
        .alert {
            border-radius: 14px;
            padding: 14px 18px;
            font-weight: 700;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }
        .alert.success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert.warning { background: var(--amber-bg); color: #92400E; border: 1px solid var(--amber-border); }
        .alert.info { background: var(--blue-bg); color: #1E40AF; border: 1px solid var(--blue-border); }
        .order-card {
            border: 2px solid rgba(29,106,74,0.15);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }
        .order-number { font-weight: 900; font-size: 1.05rem; }
        .order-date { color: var(--muted); font-size: 0.8rem; margin-top: 3px; }
        .badge {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .badge-amber { background: var(--amber-bg); color: var(--amber); border: 1px solid var(--amber-border); }
        .badge-green { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-blue { background: var(--blue-bg); color: #1E40AF; border: 1px solid var(--blue-border); }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(29,106,74,0.06);
            font-size: 0.88rem;
        }
        .item-row:last-child { border-bottom: none; }
        .item-name { font-weight: 700; }
        .item-qty { color: var(--muted); font-size: 0.8rem; }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding-top: 14px;
            margin-top: 10px;
            border-top: 2px solid rgba(29,106,74,0.12);
            font-weight: 900;
            font-size: 1.05rem;
        }
        .total-row span:last-child { color: var(--hijau); }
        .payment-box {
            background: var(--hijau-pale);
            border: 2px solid rgba(29,106,74,0.2);
            border-radius: 16px;
            padding: 20px;
            margin-top: 16px;
        }
        .payment-box h3 {
            margin: 0 0 12px;
            font-size: 1rem;
            font-weight: 900;
            color: var(--hijau);
        }
        .bank-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 6px 16px;
            font-size: 0.9rem;
        }
        .bank-label { color: var(--muted); font-weight: 600; }
        .bank-value { font-weight: 800; }
        .bank-value.highlight {
            font-size: 1.2rem;
            color: var(--hijau);
        }
        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 10px;
            padding: 6px 14px;
            border-radius: 8px;
            border: 1.5px solid rgba(29,106,74,0.3);
            background: white;
            color: var(--hijau);
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.15s;
        }
        .copy-btn:hover { background: var(--hijau); color: white; }
        .steps {
            margin-top: 14px;
            padding-left: 20px;
            font-size: 0.88rem;
            line-height: 1.8;
        }
        .steps li { margin-bottom: 4px; }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .actions .btn { flex: 1; min-width: 160px; }
        .status-pending {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            padding: 12px 16px;
            background: var(--amber-bg);
            border: 1px solid var(--amber-border);
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #92400E;
        }
        .spinner {
            width: 16px; height: 16px;
            border: 2px solid var(--amber-border);
            border-top-color: var(--amber);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 640px) {
            .container { padding: 24px 14px 48px; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .nav { width: 100%; }
            .btn { flex: 1; padding: 10px 12px; }
            .actions { flex-direction: column; }
            .actions .btn { min-width: auto; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <a href="{{ route('home') }}" class="brand">SIPDKS</a>
            <div class="nav">
                <a href="{{ route('marketplace.orders.index') }}" class="btn btn-outline">Pesanan Saya</a>
                <a href="{{ route('marketplace') }}" class="btn btn-solid">Marketplace</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <h1>Pembayaran Pesanan</h1>
        <p class="subtext">Pesanan Anda telah berhasil dibuat. Silakan lakukan pembayaran sesuai instruksi di bawah ini.</p>

        @foreach($orders as $pesanan)
            <div class="order-card" id="order-{{ $pesanan->id }}">
                <div class="order-header">
                    <div>
                        <div class="order-number">#{{ $pesanan->nomor_pesanan }}</div>
                        <div class="order-date">{{ $pesanan->created_at->translatedFormat('d M Y H:i') }}</div>
                    </div>
                    <div style="text-align:right;">
                        @if($pesanan->status_pembayaran === 'terverifikasi' || $pesanan->status_pembayaran === 'lunas')
                            <span class="badge badge-green">Lunas</span>
                        @else
                            <span class="badge badge-amber">Menunggu Pembayaran</span>
                        @endif
                    </div>
                </div>

                {{-- Items --}}
                @foreach($pesanan->items as $item)
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $item->nama_produk_snapshot }}</div>
                            <div class="item-qty">{{ $item->qty }} x Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</div>
                        </div>
                        <strong>Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</strong>
                    </div>
                @endforeach

                <div class="total-row">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format((float) $pesanan->total, 0, ',', '.') }}</span>
                </div>

                {{-- Payment Instructions --}}
                @if($pesanan->metode_pembayaran === 'transfer_manual')
                    <div class="payment-box">
                        <h3>🏦 Transfer Manual</h3>
                        <div class="bank-info">
                            <span class="bank-label">Bank</span>
                            <span class="bank-value">Bank Syariah Indonesia (BSI)</span>
                            <span class="bank-label">No. Rekening</span>
                            <span class="bank-value highlight">7128 9012 3456 7890</span>
                            <span class="bank-label">Atas Nama</span>
                            <span class="bank-value">Koperasi Syariah Bersama</span>
                            <span class="bank-label">Jumlah Transfer</span>
                            <span class="bank-value highlight">Rp {{ number_format((float) $pesanan->total, 0, ',', '.') }}</span>
                        </div>
                        <button class="copy-btn" onclick="copyRekening()">📋 Salin No. Rekening</button>
                        <ol class="steps">
                            <li>Transfer sesuai jumlah <strong>paling lambat 1×24 jam</strong></li>
                            <li>Masukkan nominal <strong>persis</strong> sesuai total (tanpa pembulatan)</li>
                            <li>Simpan bukti transfer</li>
                            <li>Upload bukti transfer di bawah ini</li>
                            <li>Penjual akan memverifikasi dan segera memproses pesanan</li>
                        </ol>

                        {{-- Upload Bukti Transfer --}}
                        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(29,106,74,0.15);">
                            @if($pesanan->bukti_transfer)
                                <div style="background: #D1FAE5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 0.85rem; font-weight: 700; color: #065F46;">
                                    ✅ Bukti transfer sudah diupload. Menunggu verifikasi penjual.
                                </div>
                                <div style="margin-top: 10px;">
                                    <img src="{{ Storage::url($pesanan->bukti_transfer) }}" alt="Bukti Transfer" style="max-width: 100%; border-radius: 10px; border: 1px solid #ddd;">
                                </div>
                            @else
                                <form action="{{ route('marketplace.orders.uploadBukti', $pesanan->id) }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
                                    @csrf
                                    <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; color: var(--teks);">Upload Bukti Transfer</label>
                                    <input type="file" name="bukti_transfer" accept="image/*" required
                                        style="width: 100%; padding: 10px; border: 1px solid rgba(29,106,74,0.2); border-radius: 10px; font-size: 0.85rem;">
                                    <p style="margin: 6px 0 0; font-size: 0.75rem; color: var(--muted);">Format: JPG, PNG, atau JPEG. Maks 5MB.</p>
                                    @error('bukti_transfer')
                                        <p style="color: #991b1b; font-size: 0.8rem; font-weight: 700; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                    <button type="submit" style="margin-top: 10px; padding: 8px 16px; background: var(--hijau); color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                                        📤 Kirim Bukti Transfer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                @elseif($pesanan->metode_pembayaran === 'bayar_di_tempat')
                    <div class="payment-box" style="border-color: rgba(37,99,235,0.2); background: var(--blue-bg);">
                        <h3 style="color: #1E40AF;">💳 Bayar di Tempat</h3>
                        <p style="margin: 0; font-size: 0.9rem; line-height: 1.7;">
                            Pembayaran dilakukan saat barang diterima. Penjual akan segera memproses pesanan Anda.
                        </p>
                        <ol class="steps">
                            <li>Penjual akan memproses dan mengirim pesanan</li>
                            <li>Siapkan uang sesuai total <strong>Rp {{ number_format((float) $pesanan->total, 0, ',', '.') }}</strong></li>
                            <li>Bayar saat kurir/pengirim datang</li>
                        </ol>
                    </div>

                @elseif($pesanan->metode_pembayaran === 'qris')
                    <div class="payment-box" style="border-color: rgba(37,99,235,0.2); background: var(--blue-bg);">
                        <h3 style="color: #1E40AF;">📱 QRIS</h3>
                        <p style="margin: 0; font-size: 0.9rem; line-height: 1.7;">
                            Pembayaran QRIS sedang diproses. Pesanan akan diproses setelah pembayaran berhasil.
                        </p>
                        @if($qrisPayment)
                            <div style="text-align:center; margin-top: 14px;">
                                <a href="{{ route('anggota.payments.show', $qrisPayment->payment_code) }}" class="btn btn-solid">
                                    Lihat QR Code & Bayar
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        <div class="actions">
            <a href="{{ route('marketplace.orders.index') }}" class="btn btn-outline">Lihat Pesanan Saya</a>
            <a href="{{ route('marketplace') }}" class="btn btn-solid">Belanja Lagi</a>
        </div>
    </div>

    <script>
        @if($qrisPayment && $qrisPayment->qr_string)
        const marketplaceSnapToken = {{ Js::from($qrisPayment->qr_string) }};
        const marketplacePayLink = [...document.querySelectorAll('a')].find((link) => link.textContent.includes('Lihat QR Code'));
        marketplacePayLink?.addEventListener('click', (event) => {
            event.preventDefault();
            window.snap.pay(marketplaceSnapToken, { onSuccess: () => window.location.reload(), onPending: () => {}, onError: () => {}, onClose: () => {} });
        });
        @endif

        function copyRekening() {
            navigator.clipboard.writeText('7128901234567890').then(() => {
                const btn = document.querySelector('.copy-btn');
                const original = btn.innerHTML;
                btn.innerHTML = '✅ Tersalin!';
                setTimeout(() => btn.innerHTML = original, 2000);
            });
        }

        // Auto-check payment status for QRIS payments
        document.querySelectorAll('[id^="order-"]').forEach(card => {
            const qrisBox = card.querySelector('[style*="QRIS"]');
            if (!qrisBox) return;

            const orderId = card.id.replace('order-', '');

            // Check status every 10 seconds for QRIS
            setInterval(async () => {
                try {
                    const resp = await fetch(`/pesanan-saya/${orderId}/payment-status`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (resp.ok) {
                        const data = await resp.json();
                        if (data.status_pembayaran === 'lunas') {
                            window.location.reload();
                        }
                    }
                } catch (e) { /* ignore */ }
            }, 10000);
        });
    </script>
    @if($qrisPayment && $qrisPayment->qr_string)
        <script src='https://app.sandbox.midtrans.com/snap/snap.js' data-client-key='{{ config('payment.midtrans.client_key') }}'></script>
    @endif
</body>
</html>
