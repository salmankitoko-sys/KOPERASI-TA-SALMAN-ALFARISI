<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanModal extends Model
{
    protected $table = 'pengajuan_modal';

    protected $fillable = [
        'user_id',
        'toko_id',
        'tujuan',
        'jumlah_diajukan',
        'keterangan',
        'status',
        'tanggal_pengajuan',
    ];

    protected $casts = [
        'jumlah_diajukan' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }
}

