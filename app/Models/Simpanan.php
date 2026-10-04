<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';

    protected $fillable = [
        'user_id',
        'jenis',
        'jumlah',
        'keterangan',
        'bukti_transfer',
        'status',
        'tanggal',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter jenis simpanan.
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope hanya yang sudah masuk (diverifikasi).
     */
    public function scopeMasuk($query)
    {
        return $query->where('status', 'masuk');
    }
}

