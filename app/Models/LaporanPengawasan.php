<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPengawasan extends Model
{
    protected $table = 'laporan_pengawasan';

    protected $fillable = [
        'periode',
        'semester',
        'periode_mulai',
        'periode_selesai',
        'ringkasan',
        'rekomendasi',
        'data_laporan',
        'file_laporan',
        'tanggal_publikasi',
        'dikirim_pada',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'dikirim_pada' => 'datetime',
        'data_laporan' => 'array',
    ];

    public function dps(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}

