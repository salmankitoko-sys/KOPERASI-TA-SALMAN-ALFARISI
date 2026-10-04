<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BendaharaNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bendahara_can_open_all_finance_navigation_pages(): void
    {
        $bendahara = User::factory()->create(['role' => User::ROLE_BENDAHARA]);

        $routes = [
            'bendahara.dashboard',
            'bendahara.pencairan.index',
            'bendahara.angsuran.index',
            'bendahara.simpanan.index',
            'bendahara.transaksi.mutasi.index',
            'bendahara.transaksi.rekening.index',
            'bendahara.keuangan.index',
            'bendahara.laporan.index',
            'bendahara.notifikasi',
        ];

        foreach ($routes as $routeName) {
            $this->actingAs($bendahara)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Zona Keuangan');
        }
    }

    public function test_bendahara_dashboard_contains_operational_navigation_and_navbar(): void
    {
        $bendahara = User::factory()->create(['role' => User::ROLE_BENDAHARA]);

        $this->actingAs($bendahara)
            ->get(route('bendahara.dashboard'))
            ->assertOk()
            ->assertSee('Pusat Operasional Keuangan')
            ->assertSee('Pencairan Dana')
            ->assertDontSee('>Pembayaran<', false)
            ->assertSee('Mutasi Kas')
            ->assertSee('Rekening Koperasi')
            ->assertSee('Laporan Keuangan')
            ->assertSee('Proses transaksi');
    }

    public function test_pengurus_cannot_access_bendahara_operational_routes(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);

        foreach (['bendahara.dashboard', 'bendahara.pencairan.index', 'bendahara.transaksi.mutasi.index', 'bendahara.transaksi.rekening.index'] as $routeName) {
            $this->actingAs($pengurus)->get(route($routeName))->assertForbidden();
        }
    }
}
