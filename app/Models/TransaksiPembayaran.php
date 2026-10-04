<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPembayaran extends Model
{
    protected $table = 'transaksi_pembayaran';

    protected $fillable = [
        'user_id',
        'jenis_transaksi',
        'judul',
        'jumlah',
        'metode_pembayaran',
        'status',
        'referensi',
        'keterangan',
        'rekening_koperasi_id',
        'bukti_transfer',
    ];

    public function rekening()
    {
        return $this->belongsTo(RekeningKoperasi::class, 'rekening_koperasi_id');
    }

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
