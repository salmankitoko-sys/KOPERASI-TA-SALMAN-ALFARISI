<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'pembeli_id',
        'toko_id',
        'nomor_pesanan',
        'status',
        'status_pembayaran',
        'akad',
        'total',
        'biaya_pengiriman',
        'metode_pengiriman',
        'metode_pembayaran',
        'alamat_kirim',
        'catatan_pembeli',
        'bukti_transfer',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'biaya_pengiriman' => 'decimal:2',
    ];

    public function pembeli()
    {
        return $this->belongsTo(User::class, 'pembeli_id');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    public function items()
    {
        return $this->hasMany(PesananItem::class, 'pesanan_id');
    }
}

