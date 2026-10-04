<?php

namespace App\Providers;

use App\Services\DisbursementService;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayService::class);
        $this->app->singleton(DisbursementService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ============ GATE: DEWAN PENGAWAS SYARIAH (DPS) ============
        // DPS adalah auditor syariah independen. DPS dapat membaca data akad
        // serta mencatat temuan, opini, dan laporan pengawasan.

        // Read-only: audit & pengawasan akad/pembiayaan
        Gate::define('dps.view-akad', fn ($user) => $user->isDps());

        // Read-only: pemantauan simpanan, pembiayaan, dan layanan transaksi
        Gate::define('dps.view-operasional', fn ($user) => $user->isDps());

        // Read-only: moderasi produk marketplace
        Gate::define('dps.moderasi-produk', fn ($user) => $user->isDps());

        // Write: temuan audit (create + update, tanpa delete)
        Gate::define('dps.kelola-temuan', fn ($user) => $user->isDps());

        // Write: opini syariah (create + update)
        Gate::define('dps.kelola-opini', fn ($user) => $user->isDps());

        // Write: laporan pengawasan (create + update)
        Gate::define('dps.kelola-laporan', fn ($user) => $user->isDps());

        // Read-only: notifikasi DPS
        Gate::define('dps.view-notifikasi', fn ($user) => $user->isDps());

        // Blokir aksi transaksional yang bukan kewenangan DPS
        Gate::define('dps.transaksi', fn ($user) => false);
        Gate::define('dps.ubah-anggota', fn ($user) => false);
    }
}
