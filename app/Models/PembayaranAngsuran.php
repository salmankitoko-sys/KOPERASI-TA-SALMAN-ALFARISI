<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAngsuran extends Model
{
    protected $table = 'pembayaran_angsuran';

    protected $fillable = [
        'pembiayaan_id',
        'angsuran_id',
        'payment_id',
        'jumlah_dibayar',
        'tanggal_bayar',
        'bukti_transfer',
        'status',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'catatan_penolakan',
        'catatan',
    ];

    protected $casts = [
        'jumlah_dibayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'tanggal_verifikasi' => 'date',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class);
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
