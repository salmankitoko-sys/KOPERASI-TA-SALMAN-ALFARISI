<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananItem extends Model
{
    protected $table = 'pesanan_item';

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'nama_produk_snapshot',
        'harga_satuan',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'qty' => 'integer',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}

