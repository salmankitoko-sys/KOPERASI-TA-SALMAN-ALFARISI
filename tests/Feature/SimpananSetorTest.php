<?php

namespace Tests\Feature;

use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SimpananSetorTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_can_submit_simpanan_setoran(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'anggota']);
        $otherMember = User::factory()->create(['role' => 'anggota']);
        $receipt = UploadedFile::fake()->create('bukti-transfer.pdf', 120, 'application/pdf');

        $response = $this
            ->actingAs($user)
            ->from(route('anggota.dashboard'))
            ->post(route('anggota.simpanan.setor'), [
                'user_id' => $otherMember->id,
                'jenis_simpanan' => 'sukarela',
                'nominal' => 150000,
                'tanggal_setor' => now()->toDateString(),
                'bukti_transfer' => $receipt,
            ]);

        $response->assertRedirect(route('anggota.dashboard', ['tab' => 'simpanan']));

        $setoran = SetoranSimpanan::sole();
        $mutasi = TransaksiPembayaran::sole();

        $this->assertSame($user->id, $setoran->user_id);
        $this->assertSame('sukarela', $setoran->jenis_simpanan);
        $this->assertSame('menunggu_verifikasi', $setoran->status);
        $this->assertSame(150000.0, (float) $setoran->nominal);
        $this->assertNotNull($setoran->bukti_transfer);
        $this->assertSame($setoran->bukti_transfer, $mutasi->bukti_transfer);
        $this->assertSame('setoran_simpanan:'.$setoran->id, $mutasi->referensi);
        $this->assertSame(0, Simpanan::count());
        Storage::disk('public')->assertExists($setoran->bukti_transfer);
    }

    public function test_verified_member_deposit_keeps_its_transfer_receipt(): void
    {
        Storage::fake('public');

        $anggota = User::factory()->create(['role' => 'anggota']);
        $bendahara = User::factory()->create(['role' => 'bendahara']);

        $this->actingAs($anggota)->post(route('anggota.simpanan.setor'), [
            'jenis_simpanan' => 'wajib',
            'nominal' => 200000,
            'tanggal_setor' => now()->toDateString(),
            'bukti_transfer' => UploadedFile::fake()->create('setoran-wajib.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('anggota.dashboard', ['tab' => 'simpanan']));

        $setoran = SetoranSimpanan::sole();
        $mutasi = TransaksiPembayaran::sole();

        $this->actingAs($bendahara)
            ->from(route('bendahara.transaksi.mutasi.index'))
            ->post(route('bendahara.transaksi.mutasi.approve', $mutasi->id))
            ->assertRedirect(route('bendahara.transaksi.mutasi.index'));

        $simpanan = Simpanan::sole();

        $this->assertSame('diverifikasi', $setoran->fresh()->status);
        $this->assertSame('masuk', $simpanan->status);
        $this->assertSame($setoran->bukti_transfer, $simpanan->bukti_transfer);
        $this->assertSame($anggota->id, $simpanan->user_id);
    }
}
