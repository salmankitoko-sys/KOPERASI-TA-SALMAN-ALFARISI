<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDana extends Model
{
    protected $table = 'pencairan_dana';

    protected $fillable = [
        'pembiayaan_id',
        'rekening_koperasi_id',
        'nominal_pencairan',
        'bank_tujuan',
        'no_rekening_tujuan',
        'nama_pemilik_rekening',
        'bukti_transfer',
        'tanggal_pencairan',
        'dicairkan_oleh',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'nomor_referensi_transfer',
        'tanggal_transfer',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'status',
        'catatan',
        'metode_pencairan',
        'disbursement_reference',
        'disbursement_status',
        'disbursement_at',
        'disbursement_response',
    ];

    protected $casts = [
        'nominal_pencairan' => 'decimal:2',
        'tanggal_pencairan' => 'date',
        'tanggal_persetujuan' => 'date',
        'tanggal_transfer' => 'date',
        'tanggal_verifikasi' => 'date',
        'disbursement_at' => 'datetime',
        'disbursement_response' => 'array',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function dicairkanOleh()
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    public function rekeningKoperasi()
    {
        return $this->belongsTo(RekeningKoperasi::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
