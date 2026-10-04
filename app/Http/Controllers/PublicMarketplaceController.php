<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\Produk;
use App\Services\PaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Support\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PublicMarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = $request->query('kategori');

        // Daftar kategori unik untuk filter
        $kategoriList = Produk::where('status', 'aktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->pluck('kategori');

        $query = Produk::with('toko.user')
            ->where('status', 'aktif');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $featuredProducts = $query->latest()->take(4)->get();

        return view('welcome', compact('featuredProducts', 'kategoriList', 'kategori'));
    }

    public function marketplace(Request $request): View
    {
        $kategori = $request->query('kategori');

        $productsQuery = Produk::with('toko.user')
            ->where('status', 'aktif');

        if ($kategori) {
            $productsQuery->where('kategori', $kategori);
        }

        $products = $productsQuery->latest()->paginate(12)->withQueryString();

        // juga kirim daftar kategori agar halaman marketplace bisa menampilkan filter
        $kategoriList = Produk::where('status', 'aktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('marketplace', compact('products', 'kategoriList', 'kategori'));
    }

    public function show(Produk $produk): View
    {
        abort_unless($produk->status === 'aktif', 404);

        $produk->load('toko.user');

        $relatedProducts = Produk::with('toko')
            ->where('status', 'aktif')
            ->where('id', '!=', $produk->id)
            ->when($produk->kategori, fn ($query) => $query->where('kategori', $produk->kategori))
            ->latest()
            ->take(4)
            ->get();

        return view('marketplace.show', compact('produk', 'relatedProducts'));
    }

    public function cart(Request $request): View
    {
        $cart = $this->cartItems($request);

        return view('marketplace.cart', [
            'cartItems' => $cart['items'],
            'subtotal' => $cart['subtotal'],
        ]);
    }

    public function addToCart(Request $request, Produk $produk): RedirectResponse
    {
        abort_unless($produk->status === 'aktif', 404);

        $validated = $request->validate([
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
            'redirect_to' => ['nullable', 'in:cart,checkout'],
        ]);

        if ($produk->stok < 1) {
            return back()->with('error', 'Stok produk sedang kosong.');
        }

        $qty = min((int) ($validated['qty'] ?? 1), $produk->stok);
        $cart = $request->session()->get('cart', []);
        $currentQty = (int) ($cart[$produk->id] ?? 0);
        $cart[$produk->id] = min($currentQty + $qty, $produk->stok);

        $request->session()->put('cart', $cart);

        if (($validated['redirect_to'] ?? null) === 'checkout') {
            return redirect()->route('marketplace.checkout');
        }

        return redirect()->route('marketplace.cart')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, Produk $produk): RedirectResponse
    {
        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (!array_key_exists($produk->id, $cart)) {
            return redirect()->route('marketplace.cart')->with('error', 'Produk tidak ada di keranjang.');
        }

        $cart[$produk->id] = min((int) $validated['qty'], max(1, (int) $produk->stok));
        $request->session()->put('cart', $cart);

        return redirect()->route('marketplace.cart')->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    public function removeFromCart(Request $request, Produk $produk): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$produk->id]);

        $request->session()->put('cart', $cart);

        return redirect()->route('marketplace.cart')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $cart = $this->cartItems($request);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('marketplace.cart')->with('error', 'Keranjang masih kosong.');
        }

        return view('marketplace.checkout', [
            'cartItems' => $cart['items'],
            'subtotal' => $cart['subtotal'],
            'shippingOptions' => $this->shippingOptions(),
            'paymentOptions' => $this->paymentOptions(),
        ]);
    }

    public function storeCheckout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'alamat_kirim' => ['required', 'string', 'max:1000'],
            'metode_pengiriman' => ['required', 'in:ambil_di_toko,diantar_penjual,kurir_lokal'],
            'metode_pembayaran' => ['required', 'in:transfer_manual,bayar_di_tempat,qris'],
            'catatan_pembeli' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = $this->cartItems($request);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('marketplace.cart')->with('error', 'Keranjang masih kosong.');
        }

        try {
            $orders = DB::transaction(function () use ($cart, $request, $validated) {
                $createdOrders = collect();
                $groupedItems = $cart['items']->groupBy('produk.toko_id');
                $shippingFeePerOrder = $this->shippingFee($validated['metode_pengiriman']);

                foreach ($groupedItems as $tokoId => $items) {
                    $freshProducts = Produk::whereIn('id', $items->pluck('produk.id'))
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    foreach ($items as $item) {
                        $produk = $freshProducts[$item['produk']->id] ?? null;

                        if (!$produk || $produk->status !== 'aktif' || $produk->stok < $item['qty']) {
                            throw new \RuntimeException('Stok produk "' . ($item['produk']->nama ?? 'produk') . '" tidak mencukupi.');
                        }
                    }

                    $itemsSubtotal = $items->sum('subtotal');
                    $pesanan = Pesanan::create([
                        'pembeli_id' => $request->user()->id,
                        'toko_id' => $tokoId,
                        'nomor_pesanan' => $this->generateOrderNumber(),
                        'status' => 'menunggu',
                        'status_pembayaran' => 'menunggu',
                        'akad' => 'murabahah',
                        'total' => $itemsSubtotal + $shippingFeePerOrder,
                        'biaya_pengiriman' => $shippingFeePerOrder,
                        'metode_pengiriman' => $validated['metode_pengiriman'],
                        'metode_pembayaran' => $validated['metode_pembayaran'],
                        'alamat_kirim' => $validated['alamat_kirim'],
                        'catatan_pembeli' => $validated['catatan_pembeli'] ?? null,
                    ]);

                    foreach ($items as $item) {
                        $produk = $freshProducts[$item['produk']->id];

                        PesananItem::create([
                            'pesanan_id' => $pesanan->id,
                            'produk_id' => $produk->id,
                            'nama_produk_snapshot' => $produk->nama,
                            'harga_satuan' => $produk->harga,
                            'qty' => $item['qty'],
                            'subtotal' => $item['subtotal'],
                        ]);

                        $produk->decrement('stok', $item['qty']);

                        if ($produk->fresh()->stok <= 0) {
                            $produk->update(['status' => 'habis']);
                        }
                    }

                    $createdOrders->push($pesanan);
                }

                // Notify toko owners + pengurus about new orders
                foreach ($createdOrders as $order) {
                    NotificationService::pesananBaru($order);
                }

                return $createdOrders;
            });
        } catch (\RuntimeException $exception) {
            return redirect()->route('marketplace.cart')->with('error', $exception->getMessage());
        }

        $request->session()->forget('cart');

        // Buat tagihan QRIS terpisah untuk setiap pesanan toko.
        if ($validated['metode_pembayaran'] === 'qris' && $orders->isNotEmpty()) {
            try {
                $paymentGateway = app(PaymentGatewayService::class);
                foreach ($orders as $order) {
                    $paymentGateway->createQrisPayment([
                        'user_id' => auth()->id(),
                        'type' => Payment::TYPE_MARKETPLACE,
                        'payable_id' => $order->id,
                        'amount' => $order->total,
                        'notes' => 'Pembayaran pesanan ' . $order->nomor_pesanan,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Marketplace: Failed to create QRIS payment', [
                    'order_ids' => $orders->pluck('id')->all(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('marketplace.payment.hub')
            ->with('success', $orders->count() . ' pesanan berhasil dibuat. Lanjutkan pembayaran dari halaman ini.');
    }

    /**
     * Show payment instruction page after checkout.
     *
     * GET /marketplace/pembayaran?orders=1,2,3
     */
    public function payment(Request $request)
    {
        $orderIds = $request->query('orders');

        if (!$orderIds) {
            return redirect()->route('marketplace.orders.index');
        }

        $ids = is_array($orderIds) ? $orderIds : explode(',', $orderIds);

        $orders = Pesanan::whereIn('id', $ids)
            ->where('pembeli_id', auth()->id())
            ->with(['items', 'toko'])
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('marketplace.orders.index')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $qrisPayment = session('qrisPayment');

        return view('marketplace.payment', compact('orders', 'qrisPayment'))
            ->with('success', session('success'));
    }

    public function paymentHub(): View
    {
        $orders = Pesanan::where('pembeli_id', auth()->id())
            ->whereIn('status_pembayaran', ['menunggu', 'menunggu_verifikasi'])
            ->with(['items', 'toko'])
            ->latest()
            ->get();

        $payments = Payment::where('user_id', auth()->id())
            ->where('type', Payment::TYPE_MARKETPLACE)
            ->whereIn('payable_id', $orders->pluck('id'))
            ->latest()
            ->get()
            ->unique('payable_id')
            ->keyBy('payable_id');

        return view('marketplace.payment-hub', compact('orders', 'payments'));
    }

    private function cartItems(Request $request): array
    {
        $cart = collect($request->session()->get('cart', []))
            ->mapWithKeys(fn ($qty, $id) => [(int) $id => max(1, (int) $qty)])
            ->filter();

        if ($cart->isEmpty()) {
            return ['items' => collect(), 'subtotal' => 0];
        }

        $products = Produk::with('toko')
            ->whereIn('id', $cart->keys())
            ->where('status', 'aktif')
            ->get();

        $items = $products->map(function (Produk $produk) use ($cart) {
            $qty = min((int) $cart[$produk->id], max(1, (int) $produk->stok));

            return [
                'produk' => $produk,
                'qty' => $qty,
                'subtotal' => (float) $produk->harga * $qty,
            ];
        })->values();

        return [
            'items' => $items,
            'subtotal' => $items->sum('subtotal'),
        ];
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'PD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Pesanan::where('nomor_pesanan', $number)->exists());

        return $number;
    }

    private function shippingOptions(): array
    {
        return [
            'ambil_di_toko' => ['label' => 'Ambil di toko', 'fee' => 0],
            'diantar_penjual' => ['label' => 'Diantar penjual', 'fee' => 5000],
            'kurir_lokal' => ['label' => 'Kurir lokal', 'fee' => 10000],
        ];
    }

    private function paymentOptions(): array
    {
        return [
            'transfer_manual' => 'Transfer manual',
            'bayar_di_tempat' => 'Bayar di tempat',
            'qris' => 'QRIS (Bayar Sekarang)',
        ];
    }

    private function shippingFee(string $method): int
    {
        return $this->shippingOptions()[$method]['fee'] ?? 0;
    }
}
