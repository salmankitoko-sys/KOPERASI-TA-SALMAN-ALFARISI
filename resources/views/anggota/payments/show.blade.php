<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Pembayaran QRIS</h2>
                <p class="mt-0.5 text-sm text-gray-500">Scan QR Code untuk menyelesaikan pembayaran.</p>
            </div>
            <a href="{{ route('anggota.transaksi.dashboard') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            {{-- Success / Error Flash --}}
            @if (session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            {{-- Status Badge --}}
            <div id="statusBadge" class="mb-6 text-center" data-status="{{ $payment->status }}">
                @if ($payment->status === 'pending')
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 ring-1 ring-amber-200">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
                        Menunggu Pembayaran
                    </span>
                @elseif ($payment->status === 'paid')
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-200">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                        Pembayaran Berhasil
                    </span>
                @elseif ($payment->status === 'expired')
                    <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-600 ring-1 ring-gray-200">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                        Kedaluwarsa
                    </span>
                @elseif ($payment->status === 'failed')
                    <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 ring-1 ring-red-200">
                        Pembayaran Gagal
                    </span>
                @endif
            </div>

            {{-- QR Code Card --}}
            @if ($payment->status === 'pending' && $payment->qr_string)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-center">
                        <p class="mb-4 text-sm font-medium text-gray-500">Scan QR Code ini dengan aplikasi e-wallet atau mobile banking Anda</p>

                        <button id="pay-button" type="button" class="mb-3 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Tampilkan QR Code QRIS</button>
                        <p id="qris-error" class="mb-3 hidden text-sm font-medium text-red-600" role="alert"></p>
                        @if($payment->qr_url)
                            <div class="mb-5">
                                <a id="qris-direct-link" href="{{ $payment->qr_url }}" target="_blank" rel="noopener" class="text-sm font-semibold text-indigo-700 underline underline-offset-2">
                                    QR tidak muncul? Buka halaman pembayaran langsung
                                </a>
                            </div>
                        @endif

                        {{-- QR Code Image --}}
                        <div id="qris-image" class="mx-auto mb-4 flex h-64 w-64 items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50">
                            <img
                                src="{{ $payment->qr_url }}"
                                alt="QR Code QRIS"
                                class="h-full w-full object-contain p-2"
                                onerror="this.parentElement.innerHTML='<div class=\'text-center text-gray-400\'><p class=\'mt-2 text-sm\'>Gagal memuat QR Code</p></div>'"
                            >
                        </div>
                        <p class="mb-1 text-xs text-gray-400">Masa berlaku QR Code</p>
                        <p id="countdown" class="text-lg font-bold text-indigo-600" data-expired="{{ $payment->expired_at?->toIso8601String() }}">
                            --:--
                        </p>
                    </div>
                </div>
            @endif

            {{-- Payment Details --}}
            <div class="mt-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-400">Detail Pembayaran</h3>

                <dl class="space-y-3">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Kode Pembayaran</dt>
                        <dd class="font-mono text-sm font-semibold text-gray-900">{{ $payment->payment_code }}</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Jenis Pembayaran</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $payment->type_label }}</dd>
                    </div>

                    @if ($payment->type === 'angsuran' && $payable)
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Pembiayaan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payable->pembiayaan->kode ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Angsuran Ke-</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payable->bulan_ke }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Jatuh Tempo</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($payable->jatuh_tempo)->translatedFormat('d F Y') }}</dd>
                        </div>
                    @endif

                    @if ($payment->type === 'simpanan' && $payable)
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Jenis Simpanan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ ucfirst($payable->jenis_simpanan) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Tanggal Setor</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($payable->tanggal_setor)->translatedFormat('d F Y') }}</dd>
                        </div>
                    @endif

                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Nominal</dt>
                            <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                        </div>
                        @if ($payment->fee > 0)
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Biaya Admin</dt>
                                <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($payment->fee, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                            <dt class="text-sm font-bold text-gray-700">Total Dibayar</dt>
                            <dd class="text-lg font-bold text-indigo-600">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</dd>
                        </div>
                    </div>

                    @if ($payment->paid_at)
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Dibayar Pada</dt>
                            <dd class="text-sm font-medium text-emerald-600">{{ $payment->paid_at->translatedFormat('d F Y, H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Actions --}}
            @if ($payment->status === 'pending')
                <div class="mt-5 flex gap-3">
                    <form method="POST" action="{{ route('anggota.payments.cancel', $payment->payment_code) }}" class="flex-1" onsubmit="return confirm('Batalkan pembayaran ini?')">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Batalkan Pembayaran
                        </button>
                    </form>
                </div>
            @endif

            @if ($payment->status === 'paid')
                <div class="mt-5 text-center">
                    <a href="{{ route('anggota.transaksi.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                        Lihat Dashboard Transaksi
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Auto-check status & countdown timer --}}
    @if ($payment->status === 'pending')
    <script src="{{ config('payment.midtrans.snap_js_url') }}" data-client-key="{{ config('payment.midtrans.client_key') }}"></script>
    <script>
        const snapToken = {{ Js::from($payment->qr_string) }};
        const payButton = document.getElementById('pay-button');
        const qrisImage = document.getElementById('qris-image');
        const qrisError = document.getElementById('qris-error');

        // Midtrans Snap renders the QR inside its popup. Avoid displaying the
        // redirect URL as an image because it is an HTML page, not a QR asset.
        qrisImage?.remove();

        if (payButton) {
            payButton.addEventListener('click', () => {
                if (snapToken && window.snap?.pay) {
                    qrisError?.classList.add('hidden');
                    window.snap.pay(snapToken, {
                        onSuccess: () => location.reload(),
                        onPending: () => checkStatus(),
                        onError: () => checkStatus(),
                        onClose: () => checkStatus(),
                    });
                    return;
                }

                const directLink = document.getElementById('qris-direct-link');
                if (directLink?.href) {
                    window.location.assign(directLink.href);
                    return;
                }

                if (qrisError) {
                    qrisError.textContent = snapToken
                        ? 'Layanan QRIS gagal dimuat. Muat ulang halaman lalu coba lagi.'
                        : 'Token pembayaran QRIS tidak tersedia. Silakan batalkan dan buat pembayaran baru.';
                    qrisError.classList.remove('hidden');
                }
            });
        }
        const paymentCode = {{ Js::from($payment->payment_code) }};
        const expiredAt = {{ Js::from($payment->expired_at?->toIso8601String()) }};
        let checkInterval;

        // Countdown timer
        function updateCountdown() {
            const el = document.getElementById('countdown');
            if (!el || !expiredAt) return;

            const now = new Date();
            const exp = new Date(expiredAt);
            const diff = exp - now;

            if (diff <= 0) {
                el.textContent = 'Kedaluwarsa';
                el.classList.remove('text-indigo-600');
                el.classList.add('text-red-600');
                clearInterval(checkInterval);
                // Refresh page to show expired status
                setTimeout(() => location.reload(), 2000);
                return;
            }

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            el.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        // Poll payment status every 5 seconds
        function checkStatus() {
            fetch(`{{ route('anggota.payments.status', $payment->payment_code) }}`)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'paid' || data.status === 'failed' || data.status === 'expired') {
                        clearInterval(checkInterval);
                        // Reload page to show updated status
                        location.reload();
                    }
                })
                .catch(() => {});
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
        checkInterval = setInterval(checkStatus, 5000);
    </script>
    @endif
</x-app-layout>
