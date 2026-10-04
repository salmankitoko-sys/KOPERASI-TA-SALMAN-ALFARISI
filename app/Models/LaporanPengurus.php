<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPengurus extends Model
{
    protected $table = 'laporan_pengurus';

    protected $fillable = ['judul', 'periode_mulai', 'periode_selesai', 'ringkasan', 'capaian', 'kendala', 'tindak_lanjut', 'status', 'dibuat_oleh', 'dikirim_pada', 'ditinjau_oleh', 'ditinjau_pada', 'catatan_ketua'];

    protected $casts = ['periode_mulai' => 'date', 'periode_selesai' => 'date', 'dikirim_pada' => 'datetime', 'ditinjau_pada' => 'datetime'];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function peninjau()
    {
        return $this->belongsTo(User::class, 'ditinjau_oleh');
    }
}
