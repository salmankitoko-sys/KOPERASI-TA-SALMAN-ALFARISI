<?php

namespace Tests\Feature;

use App\Models\LaporanPengurus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPengurusFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_can_create_and_send_report_to_ketua(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        User::factory()->create(['role' => User::ROLE_KETUA]);

        $this->actingAs($pengurus)->post(route('pengurus.laporan.store'), [
            'judul' => 'Laporan Bulanan', 'periode_mulai' => '2026-08-01', 'periode_selesai' => '2026-08-31',
            'ringkasan' => 'Operasional berjalan baik.', 'capaian' => 'Target anggota tercapai.',
        ])->assertRedirect();

        $laporan = LaporanPengurus::firstOrFail();
        $this->assertSame('draf', $laporan->status);
        $this->actingAs($pengurus)->post(route('pengurus.laporan.send', $laporan))->assertRedirect();
        $this->assertSame('dikirim', $laporan->fresh()->status);
    }

    public function test_ketua_can_request_follow_up_and_accept_resubmission(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $ketua = User::factory()->create(['role' => User::ROLE_KETUA]);
        $laporan = LaporanPengurus::create(['judul' => 'Laporan Bulanan', 'periode_mulai' => '2026-08-01', 'periode_selesai' => '2026-08-31', 'ringkasan' => 'Ringkasan', 'status' => 'dikirim', 'dibuat_oleh' => $pengurus->id, 'dikirim_pada' => now()]);

        $this->actingAs($ketua)->post(route('ketua.laporan.review', $laporan), ['keputusan' => 'perlu_tindak_lanjut', 'catatan_ketua' => 'Lengkapi data capaian.'])->assertRedirect(route('ketua.dashboard', ['tab' => 'laporan']));
        $this->assertSame('perlu_tindak_lanjut', $laporan->fresh()->status);

        $this->actingAs($pengurus)->post(route('pengurus.laporan.send', $laporan))->assertRedirect();
        $this->actingAs($ketua)->post(route('ketua.laporan.review', $laporan->fresh()), ['keputusan' => 'diterima'])->assertRedirect();
        $this->assertSame('diterima', $laporan->fresh()->status);
        $this->assertSame($ketua->id, $laporan->fresh()->ditinjau_oleh);
    }

    public function test_other_roles_cannot_manage_reports(): void
    {
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $laporan = LaporanPengurus::create(['judul' => 'Laporan', 'periode_mulai' => '2026-08-01', 'periode_selesai' => '2026-08-31', 'ringkasan' => 'Ringkasan', 'status' => 'dikirim', 'dibuat_oleh' => $pengurus->id]);
        $this->actingAs($anggota)->get(route('pengurus.laporan.index'))->assertForbidden();
        $this->actingAs($anggota)->post(route('ketua.laporan.review', $laporan))->assertForbidden();
    }
}
