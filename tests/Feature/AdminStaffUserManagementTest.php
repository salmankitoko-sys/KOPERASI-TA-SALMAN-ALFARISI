<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_reports_all_user_access_scope(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['role' => User::ROLE_PENGURUS]);
        User::factory()->create(['role' => User::ROLE_DPS]);
        User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        User::factory()->create(['role' => User::ROLE_PELANGGAN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Panel Admin')
            ->assertSee('Kelola seluruh user dari satu tempat')
            ->assertSee('User terbaru')
            ->assertSee('Ringkasan Role')
            ->assertDontSee('Tambah Pengurus')
            ->assertDontSee('class="border-b border-gray-200 bg-white"', false);
    }

    public function test_admin_sees_all_user_roles(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $dps = User::factory()->create(['role' => User::ROLE_DPS]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pelanggan = User::factory()->create(['role' => User::ROLE_PELANGGAN]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response
            ->assertOk()
            ->assertSee('Manajemen User')
            ->assertSee('Ekspor CSV')
            ->assertSee($admin->email)
            ->assertSee($pengurus->email)
            ->assertSee($dps->email)
            ->assertSee($anggota->email)
            ->assertSee($pelanggan->email)
            ->assertSee('Tambah User')
            ->assertSee('Edit')
            ->assertDontSee('Nonaktifkan')
            ->assertViewHas('users', function ($users) use ($admin, $pengurus, $dps, $anggota, $pelanggan): bool {
                return $users->contains('id', $admin->id)
                    && $users->contains('id', $pengurus->id)
                    && $users->contains('id', $dps->id)
                    && $users->contains('id', $anggota->id)
                    && $users->contains('id', $pelanggan->id);
            });
    }

    public function test_admin_can_filter_all_users_and_export_the_result(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $anggota = User::factory()->create([
            'name' => 'Anggota Aktif',
            'role' => User::ROLE_ANGGOTA,
            'is_active' => true,
        ]);
        $pelanggan = User::factory()->create([
            'name' => 'Pelanggan Nonaktif',
            'role' => User::ROLE_PELANGGAN,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => User::ROLE_PELANGGAN, 'status' => 'inactive']))
            ->assertOk()
            ->assertSee($pelanggan->email)
            ->assertDontSee($anggota->email);

        $this->actingAs($admin)
            ->get(route('admin.users.export', ['search' => 'Pelanggan Nonaktif']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_user_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $anggota = User::factory()->create([
            'role' => User::ROLE_ANGGOTA,
            'no_hp' => '08123456789',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $anggota))
            ->assertOk()
            ->assertSee('Profil Akun')
            ->assertSee($anggota->email)
            ->assertSee('Kontrol Data')
            ->assertSee('Hapus Akun');
    }

    public function test_admin_can_create_user_account(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Tambah User');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Bendahara Baru',
                'email' => 'bendahara.baru@example.test',
                'role' => User::ROLE_BENDAHARA,
                'is_active' => '1',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'bendahara.baru@example.test',
            'role' => User::ROLE_BENDAHARA,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_user_account(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_PENGURUS, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee('Edit User');

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Ketua Diperbarui',
                'email' => 'ketua.baru@example.test',
                'role' => User::ROLE_KETUA,
                'is_active' => '0',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ketua Diperbarui',
            'email' => 'ketua.baru@example.test',
            'role' => User::ROLE_KETUA,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_another_user_account(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $anggota))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($anggota);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($admin);
    }

    public function test_non_admin_users_cannot_access_user_monitoring(): void
    {
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);

        $this->actingAs($pengurus)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
