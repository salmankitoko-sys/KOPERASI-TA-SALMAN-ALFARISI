<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetoranSimpanan extends Model
{
    protected $table = 'setoran_simpanan';

    protected $fillable = [
        'user_id',
        'payment_id',
        'jenis_simpanan',
        'nominal',
        'bukti_transfer',
        'tanggal_setor',
        'status',
        'diverifikasi_oleh',
        'saldo_setelah',
        'catatan_penolakan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'saldo_setelah' => 'decimal:2',
        'tanggal_setor' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
