<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTemuan extends Model
{
    protected $table = 'audit_temuan';

    protected $fillable = [
        'nomor_temuan',
        'pembiayaan_id',
        'produk_id',
        'jenis_temuan',
        'kategori',
        'tingkat_resiko',
        'deskripsi',
        'rekomendasi',
        'status',
        'dibuat_oleh',
        'diverifikasi_oleh',
        'tanggal_temuan',
        'tanggal_penutupan',
    ];

    protected $casts = [
        'tanggal_temuan'    => 'date',
        'tanggal_penutupan' => 'date',
    ];

    public function pembiayaan(): BelongsTo
    {
        return $this->belongsTo(Pembiayaan::class, 'pembiayaan_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}

