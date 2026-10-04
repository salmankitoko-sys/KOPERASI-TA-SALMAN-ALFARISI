<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_can_checkout_cart_and_stock_is_reduced(): void
    {
        $seller = User::factory()->create(['role' => 'anggota']);
        $buyer = User::factory()->create(['role' => 'pelanggan']);

        $toko = Toko::create([
            'user_id' => $seller->id,
            'nama_toko' => 'Toko Sakinah',
            'slug' => 'toko-sakinah',
            'status' => 'aktif',
        ]);

        $produk = Produk::create([
            'toko_id' => $toko->id,
            'nama' => 'Beras Organik',
            'slug' => 'beras-organik',
            'kategori' => 'Sembako',
            'harga' => 25000,
            'stok' => 10,
            'akad' => 'murabahah',
            'status' => 'aktif',
        ]);

        $this->post(route('marketplace.cart.add', $produk->id), [
            'qty' => 2,
        ])->assertRedirect(route('marketplace.cart'));

        $this
            ->actingAs($buyer)
            ->post(route('marketplace.checkout.store'), [
                'alamat_kirim' => 'Jl. Koperasi No. 1',
                'metode_pengiriman' => 'diantar_penjual',
                'metode_pembayaran' => 'transfer_manual',
                'catatan_pembeli' => 'Antar sore hari.',
            ])
            ->assertRedirect(route('marketplace.orders.index'));

        $pesanan = Pesanan::with('items')->first();

        $this->assertNotNull($pesanan);
        $this->assertSame($buyer->id, $pesanan->pembeli_id);
        $this->assertSame($toko->id, $pesanan->toko_id);
        $this->assertSame('menunggu', $pesanan->status);
        $this->assertSame('menunggu_verifikasi', $pesanan->status_pembayaran);
        $this->assertSame(55000.0, (float) $pesanan->total);
        $this->assertCount(1, $pesanan->items);
        $this->assertSame(2, $pesanan->items->first()->qty);
        $this->assertSame(8, $produk->fresh()->stok);
    }
    public function test_pelanggan_can_confirm_received_order_from_shared_orders_route(): void
    {
        $seller = User::factory()->create(['role' => 'anggota']);
        $buyer = User::factory()->create(['role' => 'pelanggan']);

        $toko = Toko::create([
            'user_id' => $seller->id,
            'nama_toko' => 'Toko Amanah',
            'slug' => 'toko-amanah',
            'status' => 'aktif',
        ]);

        $pesanan = Pesanan::create([
            'pembeli_id' => $buyer->id,
            'toko_id' => $toko->id,
            'nomor_pesanan' => 'PD-TEST-TERIMA',
            'status' => 'dikirim',
            'status_pembayaran' => 'menunggu',
            'akad' => 'murabahah',
            'total' => 25000,
            'biaya_pengiriman' => 0,
            'alamat_kirim' => 'Jl. Pelanggan No. 1',
        ]);

        $this
            ->actingAs($buyer)
            ->patch(route('marketplace.orders.received', $pesanan->id))
            ->assertRedirect();

        $pesanan->refresh();

        $this->assertSame('selesai', $pesanan->status);
        $this->assertSame('terverifikasi', $pesanan->status_pembayaran);
    }
}
