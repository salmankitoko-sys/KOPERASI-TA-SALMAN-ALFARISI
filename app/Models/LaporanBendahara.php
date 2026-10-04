<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBendahara extends Model
{
    protected $table = 'laporan_bendahara';
    protected $guarded = ['id'];

    protected $casts = [
        'periode_mulai' => 'date', 'periode_selesai' => 'date',
        'dikirim_pada' => 'datetime', 'ditinjau_pada' => 'datetime',
        'kas_masuk' => 'decimal:2', 'kas_keluar' => 'decimal:2', 'saldo_bersih' => 'decimal:2',
        'setoran_simpanan' => 'decimal:2', 'penerimaan_angsuran' => 'decimal:2', 'pencairan_pembiayaan' => 'decimal:2',
    ];

    public function pembuat() { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function peninjau() { return $this->belongsTo(User::class, 'ditinjau_oleh'); }
}
