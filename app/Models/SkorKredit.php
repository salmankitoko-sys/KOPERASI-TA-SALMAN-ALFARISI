<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkorKredit extends Model
{
    protected $table = 'skor_kredit';

    protected $fillable = [
        'user_id',
        'skor',
        'faktor_simpanan',
        'faktor_omzet',
        'faktor_ketepatan',
        'faktor_lama_anggota',
        'dihitung_pada',
    ];

    protected $casts = [
        'dihitung_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope ambil skor kredit terbaru.
     */
    public function scopeTerbaru($query)
    {
        return $query->latest('dihitung_pada');
    }
}

