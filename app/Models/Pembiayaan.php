<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembiayaan extends Model
{
    protected $table = 'pembiayaan';

    protected $fillable = [
        'user_id',
        'toko_id',
        'kode',
        'akad',
        'tujuan_pembiayaan',
        'objek_pembiayaan',
        'rencana_penggunaan_dana',
        'estimasi_omzet_usaha',
        'jumlah_pembiayaan',
        'tenor',
        'angsuran_bulanan',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'bank_tujuan',
        'no_rekening_tujuan',
        'nama_pemilik_rekening',
        'tanggal_pencairan_diharapkan',
        'status',
        'status_validasi_dps',
        'catatan_validasi_dps',
        'divalidasi_oleh_dps',
        'tanggal_validasi_dps',
    ];

    protected $casts = [
        'jumlah_pembiayaan' => 'decimal:2',
        'angsuran_bulanan' => 'decimal:2',
        'estimasi_omzet_usaha' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_persetujuan' => 'date',
        'tanggal_pencairan_diharapkan' => 'date',
        'tanggal_validasi_dps' => 'datetime',
    ];

    public function detail()
    {
        return $this->hasOne(DetailAkad::class);
    }

    public function dokumen()
    {
        return $this->hasMany(PembiayaanDokumen::class);
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class);
    }

    public function pencairanDana()
    {
        return $this->hasMany(PencairanDana::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    public function validatorDps()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh_dps');
    }

    public function riwayatValidasiDps()
    {
        return $this->hasMany(ValidasiAkadDps::class)->orderByDesc('versi');
    }
}
