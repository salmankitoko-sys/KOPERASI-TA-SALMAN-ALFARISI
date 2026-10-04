<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'payment_code',
        'user_id',
        'type',
        'payable_id',
        'amount',
        'fee',
        'total_amount',
        'payment_method',
        'gateway',
        'gateway_reference',
        'external_id',
        'qr_string',
        'qr_url',
        'status',
        'paid_at',
        'expired_at',
        'gateway_response',
        'webhook_received_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'gateway_response' => 'array',
        'webhook_received_at' => 'datetime',
    ];

    // ─── Status Constants ──────────────────────────────────────

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_ANGSURAN = 'angsuran';
    const TYPE_SIMPANAN = 'simpanan';
    const TYPE_MARKETPLACE = 'marketplace';

    // ─── Relationships ─────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic-like: return the related model based on type + payable_id.
     */
    public function payable()
    {
        return match ($this->type) {
            self::TYPE_ANGSURAN => Angsuran::find($this->payable_id),
            self::TYPE_SIMPANAN => SetoranSimpanan::find($this->payable_id),
            self::TYPE_MARKETPLACE => Pesanan::find($this->payable_id),
            default => null,
        };
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where('expired_at', '<=', now());
    }

    // ─── Helpers ───────────────────────────────────────────────

    /**
     * Generate unique payment code.
     * Format: INV-YYYYMMDD-XXXX
     */
    public static function generatePaymentCode(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $lastToday = static::where('payment_code', 'like', $prefix . '%')
            ->orderByDesc('payment_code')
            ->value('payment_code');

        if ($lastToday) {
            $sequence = (int) substr($lastToday, -4) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique external_id for gateway.
     */
    public static function generateExternalId(): string
    {
        do {
            $id = 'PAY-' . strtoupper(Str::random(16));
        } while (static::where('external_id', $id)->exists());

        return $id;
    }

    /**
     * Check if this payment can still be paid.
     */
    public function isPayable(): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->expired_at
            && $this->expired_at->isFuture();
    }

    /**
     * Check if payment is successful.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    // ─── Accessors ─────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ANGSURAN => 'Angsuran Pembiayaan',
            self::TYPE_SIMPANAN => 'Setoran Simpanan',
            self::TYPE_MARKETPLACE => 'Pembelian Marketplace',
            default => ucfirst($this->type),
        };
    }
}
