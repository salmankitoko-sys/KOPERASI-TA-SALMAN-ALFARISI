<?php

namespace Tests\Feature;

use App\Models\InboxEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PengurusNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_can_send_a_broadcast_to_all_anggota(): void
    {
        Mail::fake();

        $pengurus = User::factory()->create([
            'role' => User::ROLE_PENGURUS,
        ]);
        $anggota = User::factory()->create([
            'role' => User::ROLE_ANGGOTA,
        ]);

        $response = $this
            ->actingAs($pengurus)
            ->from(route('pengurus.dashboard', ['tab' => 'notifikasi']))
            ->post(route('pengurus.marketplace.notifikasi.broadcast'), [
                'recipient' => 'semua_anggota',
                'title' => 'Pengumuman RAT',
                'message' => 'Rapat anggota tahunan akan dilaksanakan pekan depan.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success')
            ->assertRedirect(route('pengurus.dashboard', ['tab' => 'notifikasi']));

        $this->assertDatabaseHas(InboxEntry::class, [
            'user_id' => $anggota->id,
            'title' => 'Pengumuman RAT',
            'message' => 'Rapat anggota tahunan akan dilaksanakan pekan depan.',
            'is_read' => false,
        ]);
    }

    public function test_pengurus_broadcast_rejects_unknown_recipient_groups(): void
    {
        $pengurus = User::factory()->create([
            'role' => User::ROLE_PENGURUS,
        ]);

        $response = $this
            ->actingAs($pengurus)
            ->from(route('pengurus.dashboard', ['tab' => 'notifikasi']))
            ->post(route('pengurus.marketplace.notifikasi.broadcast'), [
                'recipient' => 'anggota_aktif',
                'title' => 'Pengumuman',
                'message' => 'Pesan uji.',
            ]);

        $response
            ->assertSessionHasErrors('recipient')
            ->assertRedirect(route('pengurus.dashboard', ['tab' => 'notifikasi']));
    }
}
