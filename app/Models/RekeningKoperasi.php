<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningKoperasi extends Model
{
    protected $table = 'rekening_koperasi';

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'kode_rekening',
        'is_aktif',
    ];
}
