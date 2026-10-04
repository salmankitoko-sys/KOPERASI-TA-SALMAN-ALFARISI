<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpiniSyariah extends Model
{
    protected $table = 'opini_syariah';

    protected $fillable = [
        'nomor_opini',
        'jenis_objek',
        'objek_id',
        'hasil',
        'catatan',
        'ditandatangani_oleh',
        'tanggal_opini',
    ];

    protected $casts = [
        'tanggal_opini' => 'date',
    ];

    public function pembiayaan(): BelongsTo
    {
        return $this->belongsTo(Pembiayaan::class, 'objek_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'objek_id');
    }

    public function dps(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditandatangani_oleh');
    }
}

