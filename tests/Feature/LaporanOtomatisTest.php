<?php

namespace Tests\Feature;

use App\Models\LaporanPengurus;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanOtomatisTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_can_open_combined_operational_report(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $this->actingAs($pengurus)->get(route('pengurus.laporan-otomatis.index'))
            ->assertOk()->assertSee('Laporan Anggota')->assertSee('Laporan Simpanan')
            ->assertSee('Laporan Pembiayaan')->assertDontSee('Laporan Marketplace')
            ->assertDontSee('Laporan SHU')->assertDontSee('RAT');
    }

    public function test_savings_report_uses_period_filter_and_exports_csv(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        Simpanan::create(['user_id' => $anggota->id, 'jenis' => 'wajib', 'jumlah' => 150000, 'status' => 'masuk', 'tanggal' => '2026-08-10']);

        $params = ['from' => '2026-08-01', 'to' => '2026-08-31'];
        $this->actingAs($pengurus)->get(route('pengurus.laporan-otomatis.index', $params))->assertOk()->assertSee('150.000')->assertSee($anggota->name);
        $this->actingAs($pengurus)->get(route('pengurus.laporan-otomatis.export', $params))->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_non_pengurus_cannot_access_operational_reports(): void
    {
        $ketua = User::factory()->create(['role' => User::ROLE_KETUA]);
        $this->actingAs($ketua)->get(route('pengurus.laporan-otomatis.index'))->assertForbidden();
    }

    public function test_pengurus_can_submit_automatic_report_to_ketua(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        User::factory()->create(['role' => User::ROLE_KETUA]);

        $this->actingAs($pengurus)->post(route('pengurus.laporan-otomatis.submit'), [
            'from' => '2026-01-01', 'to' => '2026-12-31',
            'kendala' => 'Partisipasi perlu ditingkatkan.', 'tindak_lanjut' => 'Sosialisasi anggota.',
        ])
            ->assertRedirect(route('pengurus.laporan.index'));

        $laporan = LaporanPengurus::firstOrFail();
        $this->assertSame('dikirim', $laporan->status);
        $this->assertSame($pengurus->id, $laporan->dibuat_oleh);
        $this->assertStringContainsString('Laporan Operasional Koperasi', $laporan->judul);
        $this->assertStringContainsString('Anggota baru', $laporan->capaian);
        $this->actingAs($pengurus)->get(route('laporan-pengurus.pdf', $laporan))
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
