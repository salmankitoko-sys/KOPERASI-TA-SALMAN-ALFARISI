<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Anggota\BagiHasilController;
use App\Http\Controllers\Anggota\LapakController;
use App\Http\Controllers\Anggota\LapakPesananController;
use App\Http\Controllers\Anggota\LapakProdukController;
use App\Http\Controllers\Anggota\MarketplaceController;
use App\Http\Controllers\Anggota\PaymentController;
use App\Http\Controllers\Anggota\PaymentMethodController;
use App\Http\Controllers\Anggota\PembiayaanController;
use App\Http\Controllers\Anggota\PesananController;
use App\Http\Controllers\Anggota\SimpananController;
use App\Http\Controllers\Anggota\TransaksiController as AnggotaTransaksiController;
use App\Http\Controllers\Anggota\WishlistController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\Bendahara\TransaksiController;
use App\Http\Controllers\Bendahara\LaporanBendaharaController;
use App\Http\Controllers\BendaharaDashboardController;
use App\Http\Controllers\DPS\AuditController;
use App\Http\Controllers\DPS\LaporanPengawasanController;
use App\Http\Controllers\DPS\NotifikasiController;
use App\Http\Controllers\DpsDashboardController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\Ketua\LaporanPengurusController as KetuaLaporanPengurusController;
use App\Http\Controllers\Ketua\LaporanBendaharaController as KetuaLaporanBendaharaController;
use App\Http\Controllers\KetuaDashboardController;
use App\Http\Controllers\LaporanPengurusPdfController;
use App\Http\Controllers\LaporanBendaharaPdfController;
use App\Http\Controllers\LaporanPengawasanPdfController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PelangganDashboardController;
use App\Http\Controllers\Pengurus\AnggotaController;
use App\Http\Controllers\Pengurus\LaporanOtomatisController;
use App\Http\Controllers\Pengurus\LaporanPengurusController;
use App\Http\Controllers\Pengurus\NotificationController;
use App\Http\Controllers\Pengurus\TokoVerifikasiController;
use App\Http\Controllers\Pengurus\TransaksiExportController;
use App\Http\Controllers\PengurusDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicMarketplaceController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicMarketplaceController::class, 'index'])
    ->name('home');

Route::get('/marketplace', [PublicMarketplaceController::class, 'marketplace'])
    ->name('marketplace');

Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/keranjang', [PublicMarketplaceController::class, 'cart'])->name('cart');
    Route::post('/keranjang/{produk}', [PublicMarketplaceController::class, 'addToCart'])->name('cart.add');
    Route::patch('/keranjang/{produk}', [PublicMarketplaceController::class, 'updateCart'])->name('cart.update');
    Route::delete('/keranjang/{produk}', [PublicMarketplaceController::class, 'removeFromCart'])->name('cart.remove');

    Route::middleware(['auth', 'role:anggota,pelanggan'])->group(function () {
        Route::get('/checkout', [PublicMarketplaceController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [PublicMarketplaceController::class, 'storeCheckout'])->name('checkout.store');
        Route::get('/pembayaran', [PublicMarketplaceController::class, 'payment'])->name('payment');
        Route::get('/pembayaran-saya', [PublicMarketplaceController::class, 'paymentHub'])->name('payment.hub');
    });

    Route::get('/produk/{produk:slug}', [PublicMarketplaceController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'role:anggota,pelanggan'])
    ->prefix('pesanan-saya')
    ->name('marketplace.orders.')
    ->group(function () {
        Route::get('/', [PesananController::class, 'index'])->name('index');
        Route::patch('/{id}/terima', [PesananController::class, 'markReceived'])->name('received');
        Route::get('/{id}/payment-status', [PesananController::class, 'paymentStatus'])->name('paymentStatus');
        Route::post('/{id}/upload-bukti', [PesananController::class, 'uploadBukti'])->name('uploadBukti');
    });

Route::middleware(['auth', 'role:pelanggan'])
    ->prefix('pelanggan')
    ->name('pelanggan.')
    ->group(function () {
        Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');
    });

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:pengurus,ketua'])
    ->get('/laporan-pengurus/{laporan}/pdf', LaporanPengurusPdfController::class)
    ->name('laporan-pengurus.pdf');

Route::middleware(['auth', 'role:bendahara,ketua'])->get('/laporan-bendahara/{laporan}/pdf', LaporanBendaharaPdfController::class)->name('laporan-bendahara.pdf');

Route::middleware(['auth', 'role:dps,ketua'])
    ->get('/laporan-pengawasan/{laporan}/pdf', LaporanPengawasanPdfController::class)
    ->name('laporan-pengawasan.pdf');

// Admin Dashboard
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');

    // User management
    Route::resource('/users', AdminUserController::class)->names('users');

    // Admin Notifications
    Route::get('/notifikasi', [AdminNotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [AdminNotificationController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifikasi.read-all');
});

// Ketua Koperasi: pimpinan pengurus dengan akses monitoring baca-saja.
Route::middleware(['auth', 'role:ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', [KetuaDashboardController::class, 'index'])->name('dashboard');
    Route::post('/laporan-pengurus/{laporan}/tinjau', [KetuaLaporanPengurusController::class, 'review'])->name('laporan.review');
    Route::post('/laporan-bendahara/{laporan}/tinjau', [KetuaLaporanBendaharaController::class, 'review'])->name('laporan-bendahara.review');
});

// Pengurus Dashboard
Route::middleware(['auth', 'role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', [PengurusDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan-pengurus', [LaporanPengurusController::class, 'index'])->name('laporan.index');
    Route::post('/laporan-pengurus', [LaporanPengurusController::class, 'store'])->name('laporan.store');
    Route::put('/laporan-pengurus/{laporan}', [LaporanPengurusController::class, 'update'])->name('laporan.update');
    Route::post('/laporan-pengurus/{laporan}/kirim', [LaporanPengurusController::class, 'send'])->name('laporan.send');
    Route::get('/laporan-operasional', [LaporanOtomatisController::class, 'index'])->name('laporan-otomatis.index');
    Route::get('/laporan-operasional/export', [LaporanOtomatisController::class, 'export'])->name('laporan-otomatis.export');
    Route::post('/laporan-operasional/kirim-ketua', [LaporanOtomatisController::class, 'submit'])->name('laporan-otomatis.submit');

    // Anggota Management (CRUD JSON)
    Route::resource('/anggota', AnggotaController::class)->names('anggota');

    // Pembiayaan Management
    Route::prefix('pembiayaan')->name('pembiayaan.')->group(function () {
        Route::get('/', [App\Http\Controllers\Pengurus\PembiayaanController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Pengurus\PembiayaanController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Pengurus\PembiayaanController::class, 'approve'])->name('approve');
        Route::post('/{id}/tolak', [App\Http\Controllers\Pengurus\PembiayaanController::class, 'tolak'])->name('tolak');
        Route::get('/{id}/angsuran', [App\Http\Controllers\Pengurus\PembiayaanController::class, 'angsuran'])->name('angsuran');
        Route::get('/{id}/riwayat-bayar', [App\Http\Controllers\Pengurus\PembiayaanController::class, 'riwayatBayar'])->name('riwayat-bayar');
    });

    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::post('/toko/{id}/approve', [TokoVerifikasiController::class, 'approve'])->name('toko.approve');
        Route::post('/toko/{id}/reject', [TokoVerifikasiController::class, 'reject'])->name('toko.reject');

        // Produk moderasi oleh pengurus
        Route::get('/produk/{id}', [App\Http\Controllers\Pengurus\ProdukModerasiController::class, 'show'])->name('produk.show');
        Route::post('/produk/{id}/approve', [App\Http\Controllers\Pengurus\ProdukModerasiController::class, 'approve'])->name('produk.approve');
        Route::post('/produk/{id}/reject', [App\Http\Controllers\Pengurus\ProdukModerasiController::class, 'reject'])->name('produk.reject');

        // Notifikasi & Broadcast oleh pengurus
        Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
        Route::post('/notifikasi/broadcast', [NotificationController::class, 'broadcast'])->name('notifikasi.broadcast');
    });
});

// Bendahara: pelaksana transaksi dan pengelola kas
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [BendaharaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifikasi', [InboxController::class, 'index'])->name('notifikasi');
    Route::get('/pencairan', [TransaksiController::class, 'index'])->defaults('section', 'pencairan')->name('pencairan.index');
    Route::get('/angsuran', [TransaksiController::class, 'index'])->defaults('section', 'angsuran')->name('angsuran.index');
    Route::get('/keuangan', [TransaksiController::class, 'mutasiIndex'])->name('keuangan.index');
    Route::get('/laporan-keuangan', [LaporanBendaharaController::class, 'index'])->name('laporan.index');
    Route::post('/laporan-keuangan', [LaporanBendaharaController::class, 'store'])->name('laporan.store');
    Route::put('/laporan-keuangan/{laporan}', [LaporanBendaharaController::class, 'update'])->name('laporan.update');
    Route::post('/laporan-keuangan/{laporan}/kirim', [LaporanBendaharaController::class, 'send'])->name('laporan.send');
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('index');
        Route::get('/rekening', [TransaksiController::class, 'rekeningIndex'])->name('rekening.index');
        Route::post('/rekening', [TransaksiController::class, 'rekeningStore'])->name('rekening.store');
        Route::patch('/rekening/{id}/status', [TransaksiController::class, 'rekeningUpdateStatus'])->name('rekening.status');
        Route::post('/pencairan/{id}/approve', [TransaksiController::class, 'pencairanApprove'])->name('pencairan.approve');
        Route::post('/pencairan/{id}/transfer', [TransaksiController::class, 'pencairanTransfer'])->name('pencairan.transfer');
        Route::post('/pencairan/{id}/disbursement', [TransaksiController::class, 'pencairanDisbursement'])->name('pencairan.disbursement');
        Route::post('/pencairan/{id}/cairkan', [TransaksiController::class, 'pencairanCairkan'])->name('pencairan.cairkan');
        Route::post('/pencairan/{id}/tolak', [TransaksiController::class, 'pencairanTolak'])->name('pencairan.tolak');
        Route::post('/angsuran/{id}/approve', [TransaksiController::class, 'angsuranApprove'])->name('angsuran.approve');
        Route::post('/angsuran/{id}/tolak', [TransaksiController::class, 'angsuranTolak'])->name('angsuran.tolak');
        Route::get('/mutasi', [TransaksiController::class, 'mutasiIndex'])->name('mutasi.index');
        Route::get('/mutasi/export', [TransaksiExportController::class, 'export'])->name('mutasi.export');
        Route::post('/mutasi/{id}/approve', [TransaksiController::class, 'mutasiApprove'])->name('mutasi.approve');
        Route::post('/mutasi/{id}/tolak', [TransaksiController::class, 'mutasiTolak'])->name('mutasi.tolak');
    });
});

// Anggota Dashboard
Route::middleware(['auth', 'role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

// ============ DPS (Dewan Pengawas Syariah) ============
// DPS = auditor syariah independen. Read-only terhadap transaksi dan
// dapat merekam temuan, opini syariah, serta laporan pengawasan.
Route::middleware(['auth', 'role:dps'])->prefix('dps')->name('dps.')->group(function () {
    Route::get('/dashboard', [DpsDashboardController::class, 'index'])->name('dashboard');
    // Audit kepatuhan akad + validasi DPS
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/{id}', [AuditController::class, 'show'])->name('show');
        Route::post('/', [AuditController::class, 'store'])->name('store');
        Route::post('/{pembiayaan}/validasi', [AuditController::class, 'store'])->name('validasi');
        Route::get('/{id}/riwayat', [AuditController::class, 'history'])->name('history');
        Route::get('/{id}/perbandingan', [AuditController::class, 'compareSnapshots'])->name('compare');
    });

    // Laporan Pengawasan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanPengawasanController::class, 'index'])->name('index');
        Route::get('/{id}', [LaporanPengawasanController::class, 'show'])->name('show');
        Route::post('/generate', [LaporanPengawasanController::class, 'generate'])->name('generate');
        Route::put('/{id}', [LaporanPengawasanController::class, 'update'])->name('update');
    });

    // Notifikasi DPS
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotifikasiController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotifikasiController::class, 'markAllRead'])->name('read-all');
    });
});

Route::middleware(['auth', 'role:anggota'])
    ->prefix('anggota')
    ->name('anggota.')
    ->group(function () {

        // ---------- ZONA KOPERASI ----------
        Route::prefix('simpanan')->name('simpanan.')->group(function () {
            Route::get('/', [SimpananController::class, 'index'])->name('index');
        });

        Route::prefix('pembiayaan')->name('pembiayaan.')->group(function () {
            Route::get('/', [PembiayaanController::class, 'index'])->name('index');
            Route::get('/ajukan', [PembiayaanController::class, 'create'])->name('ajukan');
            Route::get('/formulir', [PembiayaanController::class, 'downloadFormulir'])->name('formulir');
            Route::post('/', [PembiayaanController::class, 'store'])->name('store');
        });

        Route::prefix('bagi-hasil')->name('bagihasil.')->group(function () {
            Route::get('/', [BagiHasilController::class, 'index'])->name('index');
        });

        Route::prefix('angsuran')->name('angsuran.')->group(function () {
            Route::get('/', [AngsuranController::class, 'index'])->name('index');
            Route::get('/{angsuran}/bayar', [PaymentMethodController::class, 'showAngsuranMethod'])->name('bayar');
        });

        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
            Route::get('/pencairan', [AnggotaTransaksiController::class, 'pencairanCreate'])->name('pencairan.create');
            Route::post('/pencairan', [AnggotaTransaksiController::class, 'pencairanStore'])->name('pencairan.store');
            Route::get('/angsuran', [AnggotaTransaksiController::class, 'angsuranCreate'])->name('angsuran.create');
            Route::post('/angsuran', [AnggotaTransaksiController::class, 'angsuranStore'])->name('angsuran.store');
        });

        // ---------- MARKETPLACE ----------
        Route::prefix('marketplace')->name('marketplace.')->group(function () {
            Route::get('/', [MarketplaceController::class, 'index'])->name('index');

            // Notifikasi anggota
            Route::get('/notifikasi', [App\Http\Controllers\Anggota\NotificationController::class, 'index'])->name('notifikasi.index');
            Route::get('/notifikasi/api', [App\Http\Controllers\Anggota\NotificationController::class, 'apiNotifications'])->name('notifikasi.api');
        });

        Route::prefix('pesanan')->name('pesanan.')->group(function () {
            Route::get('/', [PesananController::class, 'index'])->name('index');
            Route::patch('/{id}/terima', [PesananController::class, 'markReceived'])->name('received');
        });

        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
        });

        // ---------- PEMBAYARAN QRIS ----------
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/riwayat', [PaymentController::class, 'history'])
                ->name('history');
            Route::post('/qris-angsuran', [PaymentController::class, 'createQrisAngsuran'])
                ->name('qris.angsuran');
            Route::get('/simpanan/qris', [PaymentController::class, 'simpananForm'])
                ->name('simpanan.qris');
            Route::post('/qris-simpanan', [PaymentController::class, 'createQrisSimpanan'])
                ->name('qris.simpanan');
            Route::get('/{paymentCode}', [PaymentController::class, 'show'])
                ->name('show');
            Route::get('/{paymentCode}/status', [PaymentController::class, 'status'])
                ->name('status');
            Route::post('/{paymentCode}/cancel', [PaymentController::class, 'cancel'])
                ->name('cancel');
        });

        // ---------- LAPAK SAYA ----------
        Route::prefix('lapak')->name('lapak.')->group(function () {
            Route::get('/', [LapakController::class, 'index'])->name('index');
            Route::post('/', [LapakController::class, 'store'])->name('store');

            Route::prefix('produk')->name('produk.')->group(function () {
                Route::get('/', [LapakProdukController::class, 'index'])->name('index');
                Route::get('/tambah', [LapakProdukController::class, 'create'])->name('create');
                Route::post('/tambah', [LapakProdukController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [LapakProdukController::class, 'edit'])->name('edit');
                Route::put('/{id}', [LapakProdukController::class, 'update'])->name('update');
                Route::delete('/{id}', [LapakProdukController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('pesanan')->name('pesanan.')->group(function () {
                Route::get('/', [LapakPesananController::class, 'index'])->name('index');
                Route::patch('/{id}/status', [LapakPesananController::class, 'updateStatus'])->name('status');
                Route::post('/{id}/konfirmasi-pembayaran', [LapakPesananController::class, 'confirmPayment'])->name('confirmPayment');
            });
        });

        // ---------- KOMPATIBILITAS URL LAMA: PEMBIAYAAN USAHA ----------
        Route::get('/pembiayaan-usaha', fn () => redirect()->route('anggota.pembiayaan.index'))
            ->name('pembiayaanusaha.index');
        Route::get('/pembiayaan-usaha/ajukan', fn () => redirect()->route('anggota.pembiayaan.ajukan', ['tujuan' => 'modal_usaha']))
            ->name('pembiayaanusaha.ajukan');
        Route::post('/pembiayaan-usaha/ajukan', fn () => redirect()->route('anggota.pembiayaan.ajukan', ['tujuan' => 'modal_usaha']))
            ->name('pembiayaanusaha.store');
    });

// ============ WEBHOOK: Payment Gateway Callback ============
// Webhook endpoint TANPA auth & TANPA CSRF Ã¢â‚¬â€ hanya bisa diakses dari gateway
// Rate limited: max 60 requests per minute per IP
Route::post('/payments/webhook', [PaymentWebhookController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('payment.webhook');
Route::post('/payments/disbursement-webhook', [PaymentWebhookController::class, 'handleDisbursement'])
    ->middleware('throttle:60,1')
    ->name('payment.disbursement-webhook');

// Inbox routes (notifikasi pengguna)
Route::middleware('auth')->group(function () {
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::post('/inbox/{id}/read', [InboxController::class, 'markRead'])->name('inbox.read');
    Route::post('/inbox/read-all', [InboxController::class, 'markAllRead'])->name('inbox.read-all');
});

require __DIR__.'/auth.php';


