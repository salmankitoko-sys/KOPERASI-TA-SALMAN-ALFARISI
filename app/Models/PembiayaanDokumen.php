<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PembiayaanDokumen extends Model
{
    protected $table = 'pembiayaan_dokumen';

    protected $fillable = [
        'pembiayaan_id',
        'jenis',
        'nama_asli',
        'path',
        'mime_type',
        'ukuran',
    ];

    protected $appends = [
        'url',
        'ukuran_label',
        'jenis_label',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getUkuranLabelAttribute(): string
    {
        if (!$this->ukuran) {
            return '-';
        }

        return $this->ukuran >= 1048576
            ? number_format($this->ukuran / 1048576, 2, ',', '.') . ' MB'
            : number_format($this->ukuran / 1024, 0, ',', '.') . ' KB';
    }

    public function getJenisLabelAttribute(): string
    {
        return [
            'identitas' => 'Identitas/KTP',
            'penghasilan' => 'Bukti Penghasilan',
            'usaha' => 'Legalitas/Profil Usaha',
            'rab' => 'RAB/Invoice/Quotation',
            'agunan' => 'Dokumen Agunan',
            'formulir_pengajuan' => 'Formulir Pengajuan',
            'lainnya' => 'Dokumen Lainnya',
        ][$this->jenis] ?? ucfirst((string) $this->jenis);
    }
}
