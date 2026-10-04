<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    protected $table = 'angsuran';

    protected $fillable = [
        'pembiayaan_id',
        'bulan_ke',
        'jatuh_tempo',
        'jumlah_bayar',
        'pokok',
        'margin',
        'sisa_pokok',
        'status',
        'tanggal_bayar',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }
}
