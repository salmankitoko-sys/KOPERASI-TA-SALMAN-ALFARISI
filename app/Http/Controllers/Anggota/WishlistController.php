<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    public function index()
    {
        // Fitur wishlist dapat dikembangkan dengan tabel wishlist terpisah
        // Saat ini gunakan session atau coming-soon
        return view('anggota.wishlist.index', [
            'produkFavorit' => collect(), // TODO: implementasi dengan model Wishlist
        ]);
    }
}
