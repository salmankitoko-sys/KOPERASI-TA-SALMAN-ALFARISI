<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $table = 'webhook_logs';

    protected $fillable = [
        'type',
        'gateway',
        'external_id',
        'order_id',
        'ip_address',
        'is_valid',
        'error_message',
        'payload',
        'verified_data',
    ];

    protected $casts = [
        'payload' => 'array',
        'verified_data' => 'array',
        'is_valid' => 'boolean',
    ];
}
