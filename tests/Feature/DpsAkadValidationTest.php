<?php

namespace Tests\Feature;

use App\Models\Angsuran;
use App\Models\DetailAkad;
use App\Models\Pembiayaan;
use App\Models\PembiayaanDokumen;
use App\Models\User;
use App\Models\ValidasiAkadDps;
use App\Support\AkadSyariahChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DpsAkadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_dps_can_validate_an_akad_without_changing_operational_status(): void
    {
        $dps = User::factory()->dps()->create();
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = $this->createPembiayaan($anggota);

        $this->actingAs($dps)
            ->postJson(route('dps.audit.validasi', $pembiayaan), $this->validationPayload($pembiayaan))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pembiayaan.status', 'diajukan')
            ->assertJsonPath('pembiayaan.status_validasi_dps', 'sesuai')
            ->assertJsonPath('validasi.versi', 1);

        $this->assertDatabaseHas('pembiayaan', [
            'id' => $pembiayaan->id,
            'status' => 'diajukan',
            'status_validasi_dps' => 'sesuai',
            'divalidasi_oleh_dps' => $dps->id,
        ]);
        $this->assertDatabaseHas('validasi_akad_dps', [
            'pembiayaan_id' => $pembiayaan->id,
            'versi' => 1,
            'hasil' => 'sesuai',
            'jumlah_tidak_sesuai' => 0,
        ]);
        $this->assertDatabaseHas('inbox_entries', [
            'user_id' => $pengurus->id,
            'title' => 'Hasil Validasi Akad '.$pembiayaan->kode,
        ]);
    }

    public function test_non_dps_user_cannot_validate_an_akad(): void
    {
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = $this->createPembiayaan($anggota);

        $this->actingAs($anggota)
            ->postJson(route('dps.audit.validasi', $pembiayaan), [
                'hasil' => 'sesuai',
            ])
            ->assertForbidden();
    }

    public function test_dps_must_add_correction_instructions_for_an_akad_that_needs_correction(): void
    {
        $dps = User::factory()->dps()->create();
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = $this->createPembiayaan($anggota);

        $payload = $this->validationPayload($pembiayaan, 'perlu_perbaikan');
        unset($payload['catatan_perbaikan']);

        $this->actingAs($dps)
            ->postJson(route('dps.audit.validasi', $pembiayaan), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('catatan_perbaikan');
    }

    public function test_pengurus_can_only_approve_after_dps_marks_akad_as_compliant(): void
    {
        $dps = User::factory()->dps()->create();
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = $this->createPembiayaan($anggota);

        $this->actingAs($pengurus)
            ->postJson(route('pengurus.pembiayaan.approve', $pembiayaan))
            ->assertUnprocessable()
            ->assertJsonPath('success', false);

        $this->actingAs($dps)
            ->postJson(route('dps.audit.validasi', $pembiayaan), $this->validationPayload($pembiayaan))
            ->assertOk();

        $this->actingAs($pengurus)
            ->postJson(route('pengurus.pembiayaan.approve', $pembiayaan))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pembiayaan', [
            'id' => $pembiayaan->id,
            'status' => 'disetujui',
            'status_validasi_dps' => 'sesuai',
        ]);
    }

    public function test_each_revalidation_creates_an_immutable_history_version(): void
    {
        $dps = User::factory()->dps()->create();
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = $this->createPembiayaan($anggota);

        $this->actingAs($dps)
            ->postJson(route('dps.audit.validasi', $pembiayaan), $this->validationPayload($pembiayaan))
            ->assertOk()
            ->assertJsonPath('validasi.versi', 1);

        $payload = $this->validationPayload($pembiayaan, 'perlu_perbaikan');
        $this->actingAs($dps)
            ->postJson(route('dps.audit.validasi', $pembiayaan), $payload)
            ->assertOk()
            ->assertJsonPath('validasi.versi', 2);

        $this->assertSame(2, ValidasiAkadDps::where('pembiayaan_id', $pembiayaan->id)->count());
        $this->assertDatabaseHas('validasi_akad_dps', [
            'pembiayaan_id' => $pembiayaan->id,
            'versi' => 1,
            'hasil' => 'sesuai',
        ]);
        $this->assertDatabaseHas('validasi_akad_dps', [
            'pembiayaan_id' => $pembiayaan->id,
            'versi' => 2,
            'hasil' => 'perlu_perbaikan',
        ]);
    }

    public function test_pengurus_cannot_approve_if_akad_data_changed_after_validation(): void
    {
        $dps = User::factory()->dps()->create();
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = $this->createPembiayaan($anggota);

        $this->actingAs($dps)
            ->postJson(route('dps.audit.validasi', $pembiayaan), $this->validationPayload($pembiayaan))
            ->assertOk();

        $pembiayaan->detail()->update(['harga_jual' => 1400000]);

        $this->actingAs($pengurus)
            ->postJson(route('pengurus.pembiayaan.approve', $pembiayaan))
            ->assertUnprocessable()
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('pembiayaan', [
            'id' => $pembiayaan->id,
            'status' => 'diajukan',
            'status_validasi_dps' => 'menunggu',
        ]);
    }

    private function createPembiayaan(User $anggota): Pembiayaan
    {
        $pembiayaan = Pembiayaan::create([
            'user_id' => $anggota->id,
            'kode' => 'AKAD-DPS-'.fake()->unique()->numerify('###'),
            'akad' => 'murabahah',
            'objek_pembiayaan' => 'Peralatan usaha',
            'jumlah_pembiayaan' => 1200000,
            'tenor' => 12,
            'angsuran_bulanan' => 110000,
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => 'diajukan',
        ]);

        DetailAkad::create([
            'pembiayaan_id' => $pembiayaan->id,
            'objek' => 'Peralatan usaha',
            'tenor' => 12,
            'harga_beli' => 1200000,
            'harga_jual' => 1320000,
            'margin_persen' => 10,
            'margin' => 120000,
            'dp' => 0,
            'angsuran_bulanan' => 110000,
        ]);

        PembiayaanDokumen::create([
            'pembiayaan_id' => $pembiayaan->id,
            'jenis' => 'formulir_pengajuan',
            'nama_asli' => 'formulir-akad.pdf',
            'path' => 'pembiayaan/testing/formulir-akad.pdf',
            'mime_type' => 'application/pdf',
            'ukuran' => 1024,
        ]);

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            Angsuran::create([
                'pembiayaan_id' => $pembiayaan->id,
                'bulan_ke' => $bulan,
                'jatuh_tempo' => now()->addMonths($bulan)->toDateString(),
                'jumlah_bayar' => 110000,
                'pokok' => 100000,
                'margin' => 10000,
                'sisa_pokok' => max(0, 1200000 - ($bulan * 100000)),
                'status' => 'belum_bayar',
            ]);
        }

        return $pembiayaan;
    }

    private function validationPayload(Pembiayaan $pembiayaan, string $hasil = 'sesuai'): array
    {
        $definitions = AkadSyariahChecklist::definitions($pembiayaan->akad);
        $checklist = collect($definitions)->mapWithKeys(fn (array $item) => [
            $item['kode'] => [
                'status' => 'sesuai',
                'catatan' => 'Telah diperiksa berdasarkan data dan dokumen pengajuan.',
            ],
        ])->all();

        if ($hasil !== 'sesuai') {
            $firstCode = $definitions[0]['kode'];
            $checklist[$firstCode] = [
                'status' => 'tidak_sesuai',
                'catatan' => 'Data pendukung pada kriteria ini belum lengkap.',
            ];
        }

        return [
            'hasil' => $hasil,
            'checklist' => $checklist,
            'kesimpulan' => 'Struktur akad dan dokumen telah diperiksa berdasarkan fatwa DSN-MUI yang relevan.',
            'catatan_perbaikan' => $hasil === 'sesuai'
                ? null
                : 'Lengkapi data pendukung dan ajukan kembali untuk pemeriksaan ulang DPS.',
            'referensi_tambahan' => null,
        ];
    }
}
