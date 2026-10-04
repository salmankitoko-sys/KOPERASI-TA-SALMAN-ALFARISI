<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidasiAkadDps extends Model
{
    protected $table = 'validasi_akad_dps';

    protected $fillable = [
        'pembiayaan_id',
        'versi',
        'hasil',
        'checklist',
        'snapshot_data',
        'snapshot_hash',
        'referensi_fatwa',
        'referensi_tambahan',
        'kesimpulan',
        'catatan_perbaikan',
        'jumlah_kriteria',
        'jumlah_sesuai',
        'jumlah_tidak_sesuai',
        'divalidasi_oleh',
        'divalidasi_pada',
    ];

    protected $casts = [
        'checklist' => 'array',
        'snapshot_data' => 'array',
        'referensi_fatwa' => 'array',
        'divalidasi_pada' => 'datetime',
    ];

    public function pembiayaan(): BelongsTo
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}
