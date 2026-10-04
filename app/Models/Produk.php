<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'toko_id',
        'nama',
        'slug',
        'deskripsi',
        'kategori',
        'harga',
        'stok',
        'akad',
        'status',
        'foto_url',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    /**
     * Scope produk yang aktif (bisa dijual).
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}

