<x-app-layout>
    @php($rupiah = fn ($value) => 'Rp ' . number_format($value ?? 0, 0, ',', '.'))

    <div class='mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8'>
        <div class='mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between'>
            <div>
                <p class='text-sm font-bold uppercase tracking-wide text-orange-600'>Marketplace</p>
                <h1 class='text-2xl font-black text-gray-900'>Pembayaran Pesanan</h1>
                <p class='mt-1 text-sm text-gray-500'>Selesaikan tagihan pesanan dari satu halaman.</p>
            </div>
            <div class='flex gap-2'>
                <a href='{{ route('marketplace.orders.index') }}' class='rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700'>Pesanan Saya</a>
                <a href='{{ route('marketplace') }}' class='rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white'>Belanja Lagi</a>
            </div>
        </div>

        @if(session('success'))
            <div class='mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800'>{{ session('success') }}</div>
        @endif

        @forelse($orders as $order)
            @php($payment = $payments->get($order->id))
            <article class='mb-5 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm'>
                <div class='flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between'>
                    <div>
                        <p class='font-black text-gray-900'>{{ $order->toko?->nama_toko ?? 'Toko Anggota' }}</p>
                        <p class='mt-1 text-xs font-semibold text-gray-500'>No. Pesanan: {{ $order->nomor_pesanan }}</p>
                    </div>
                    <span class='w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold capitalize text-amber-800'>{{ str_replace('_', ' ', $order->status_pembayaran) }}</span>
                </div>

                <div class='grid gap-5 p-5 lg:grid-cols-[1fr_260px]'>
                    <div>
                        <p class='mb-3 text-xs font-black uppercase tracking-wide text-gray-500'>Detail Produk</p>
                        <div class='space-y-3'>
                            @foreach($order->items as $item)
                                <div class='flex items-start justify-between gap-4 rounded-xl bg-gray-50 px-4 py-3'>
                                    <div>
                                        <p class='font-bold text-gray-800'>{{ $item->nama_produk_snapshot }}</p>
                                        <p class='mt-1 text-xs text-gray-500'>{{ $item->qty }} x {{ $rupiah($item->harga_satuan) }}</p>
                                    </div>
                                    <p class='whitespace-nowrap text-sm font-black text-gray-800'>{{ $rupiah($item->subtotal) }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class='mt-4 grid grid-cols-2 gap-3 text-sm'>
                            <div class='rounded-lg border border-gray-100 p-3'><p class='text-xs text-gray-500'>Pengiriman</p><p class='mt-1 font-bold capitalize text-gray-800'>{{ str_replace('_', ' ', $order->metode_pengiriman) }}</p></div>
                            <div class='rounded-lg border border-gray-100 p-3'><p class='text-xs text-gray-500'>Metode Bayar</p><p class='mt-1 font-bold capitalize text-gray-800'>{{ str_replace('_', ' ', $order->metode_pembayaran) }}</p></div>
                        </div>
                    </div>

                    <aside class='flex flex-col justify-between rounded-xl bg-slate-900 p-5 text-white'>
                        <div>
                            <p class='text-xs font-bold uppercase tracking-wide text-slate-300'>Total Tagihan</p>
                            <p class='mt-2 text-2xl font-black'>{{ $rupiah($order->total) }}</p>
                            <p class='mt-2 text-xs text-slate-300'>Termasuk ongkir {{ $rupiah($order->biaya_pengiriman) }}</p>
                        </div>
                        <div class='mt-6'>
                            @if($order->metode_pembayaran === 'qris' && $payment?->qr_string && $payment->status === \App\Models\Payment::STATUS_PENDING)
                                <button type='button' class='snap-pay w-full rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white hover:bg-orange-600' data-token='{{ $payment->qr_string }}'>Bayar dengan QRIS</button>
                            @elseif($order->metode_pembayaran === 'transfer_manual')
                                <a href='{{ route('marketplace.payment', ['orders' => $order->id]) }}' class='block w-full rounded-lg bg-orange-500 px-4 py-3 text-center text-sm font-black text-white hover:bg-orange-600'>Transfer & Upload Bukti</a>
                            @elseif($order->metode_pembayaran === 'bayar_di_tempat')
                                <p class='text-sm font-semibold text-slate-200'>Dibayar saat barang diterima.</p>
                            @else
                                <p class='text-sm font-semibold text-rose-300'>Tagihan QRIS belum tersedia. Hubungi pengurus.</p>
                            @endif
                        </div>
                    </aside>
                </div>
            </article>
        @empty
            <div class='rounded-xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center'>
                <p class='font-bold text-gray-800'>Tidak ada tagihan marketplace yang perlu dibayar.</p>
                <a href='{{ route('marketplace.orders.index') }}' class='mt-4 inline-block text-sm font-bold text-orange-600'>Lihat riwayat pesanan</a>
            </div>
        @endforelse
    </div>

    @if($payments->contains(fn ($payment) => $payment->qr_string && $payment->status === \App\Models\Payment::STATUS_PENDING))
        <script src='https://app.sandbox.midtrans.com/snap/snap.js' data-client-key='{{ config('payment.midtrans.client_key') }}'></script>
        <script>
            document.querySelectorAll('.snap-pay').forEach((button) => {
                button.addEventListener('click', () => window.snap.pay(button.dataset.token, {
                    onSuccess: () => window.location.reload(),
                    onPending: () => window.location.reload(),
                    onError: () => alert('Pembayaran tidak dapat dimulai. Silakan coba lagi.'),
                }));
            });
        </script>
    @endif
</x-app-layout>
