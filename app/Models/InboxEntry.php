<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboxEntry extends Model
{
    protected $table = 'inbox_entries';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
