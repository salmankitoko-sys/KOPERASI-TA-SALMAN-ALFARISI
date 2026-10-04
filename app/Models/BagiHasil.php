<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BagiHasil extends Model
{
    protected $table = 'bagi_hasil';

    protected $fillable = [
        'user_id',
        'periode',
        'saldo_rata_rata',
        'nisbah_persen',
        'jumlah_diterima',
        'status',
    ];

    protected $casts = [
        'periode' => 'date',
        'saldo_rata_rata' => 'decimal:2',
        'nisbah_persen' => 'decimal:2',
        'jumlah_diterima' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

