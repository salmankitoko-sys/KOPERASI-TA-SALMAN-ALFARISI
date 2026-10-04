<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'tokos';

    protected $fillable = [
        'user_id',
        'nama_toko',
        'slug',
        'deskripsi',
        'kategori',
        'status',
        'rating_rata',
        'jumlah_ulasan',
    ];

    protected $casts = [
        'rating_rata' => 'decimal:2',
        'jumlah_ulasan' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'toko_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'toko_id');
    }
}

