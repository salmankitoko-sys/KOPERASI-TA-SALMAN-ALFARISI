<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KetuaRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ketua_can_view_own_monitoring_dashboard(): void
    {
        $ketua = User::factory()->create(['role' => User::ROLE_KETUA]);

        $this->actingAs($ketua)->get('/dashboard')->assertRedirect(route('ketua.dashboard'));
        $this->actingAs($ketua)->get(route('ketua.dashboard'))->assertOk()
            ->assertSee('Dashboard')->assertSee('Akses baca-saja')
            ->assertSee('Kinerja Koperasi')->assertSee('Laporan Pengurus')
            ->assertSee('Monitoring Keuangan')->assertSee('Mode Pengawasan')
            ->assertSee('Statistik Pembiayaan')->assertSee('Status Pembiayaan')
            ->assertSee('Komposisi Akad')->assertSee('Aktif/berjalan')
            ->assertDontSee('Unit Usaha')->assertDontSee('Evaluasi &amp; Arahan', false);
    }

    public function test_ketua_cannot_access_operational_dashboards(): void
    {
        $ketua = User::factory()->create(['role' => User::ROLE_KETUA]);

        foreach (['admin.dashboard', 'pengurus.dashboard', 'bendahara.dashboard', 'dps.dashboard'] as $routeName) {
            $this->actingAs($ketua)->get(route($routeName))->assertForbidden();
        }
    }

    public function test_pengurus_cannot_access_ketua_dashboard(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $this->actingAs($pengurus)->get(route('ketua.dashboard'))->assertForbidden();
    }
}
