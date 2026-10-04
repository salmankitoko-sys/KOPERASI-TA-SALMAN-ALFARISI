<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('anggota.dashboard', absolute: false));
        $this->assertNotNull($user->refresh()->last_login_at);
    }

    public function test_pengurus_is_redirected_to_pengurus_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PENGURUS,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pengurus.dashboard', absolute: false));
    }

    public function test_admin_login_ignores_intended_url_from_other_roles(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->get(route('pelanggan.dashboard'));

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_dashboard_route_redirects_pengurus_to_pengurus_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PENGURUS,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('pengurus.dashboard', absolute: false));
    }

    public function test_role_dashboards_render_only_their_intended_headers(): void
    {
        $pengurus = User::factory()->create([
            'role' => User::ROLE_PENGURUS,
        ]);
        $anggota = User::factory()->create([
            'role' => User::ROLE_ANGGOTA,
        ]);

        $this->actingAs($pengurus)
            ->get(route('pengurus.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Pengurus')
            ->assertDontSee('class="border-b border-gray-200 bg-white"', false);

        $this->actingAs($anggota)
            ->get(route('anggota.dashboard', ['tab' => 'toko']))
            ->assertOk()
            ->assertSee('Selamat datang')
            ->assertSee('Pengingat angsuran')
            ->assertSee("url.searchParams.get('tab')", false)
            ->assertSee("setActiveTab('toko')", false)
            ->assertDontSee('class="border-b border-gray-200 bg-white"', false);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_can_not_authenticate(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

