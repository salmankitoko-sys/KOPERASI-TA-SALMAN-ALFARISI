<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailAkad extends Model
{
    protected $table = 'detail_akad';

protected $fillable = [
    'pembiayaan_id',

    'objek',
    'tenor',

    'harga_beli',
    'harga_jual',
    'margin_persen',
    'margin',
    'dp',
    'biaya_admin',
    'angsuran_bulanan',

    'modal',
    'modal_anggota',
    'porsi_modal_koperasi',
    'porsi_modal_anggota',
    'nisbah_koperasi',
    'nisbah_anggota',
    'estimasi_omzet',
    'estimasi_biaya',
    'estimasi_laba',
    'bagi_hasil_koperasi',
    'bagi_hasil_anggota',

    'nilai_aset',
    'ujrah_bulanan',
    'biaya_perawatan',
    'opsi_beli',
    'total_pembayaran'
];

public function pembiayaan()
{
    return $this->belongsTo(Pembiayaan::class);
}
}
