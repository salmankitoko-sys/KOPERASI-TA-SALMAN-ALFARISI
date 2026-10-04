<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'tanggal_lahir',
        'email_verified_at',
        'role',
        'is_active',
        'last_login_at',
        'pekerjaan',
        'penghasilan',
        'status',
        'tgl_gabung',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const ROLE_ADMIN = 'admin';

    const ROLE_PENGURUS = 'pengurus';

    const ROLE_KETUA = 'ketua';

    const ROLE_BENDAHARA = 'bendahara';

    const ROLE_ANGGOTA = 'anggota';

    const ROLE_PELANGGAN = 'pelanggan';

    const ROLE_DPS = 'dps';

    public const MANAGED_STAFF_ROLES = [
        self::ROLE_KETUA,
        self::ROLE_PENGURUS,
        self::ROLE_BENDAHARA,
        self::ROLE_DPS,
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'tanggal_lahir' => 'date',
            'tgl_gabung' => 'date',
            'penghasilan' => 'decimal:2',
        ];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is pengurus.
     */
    public function isPengurus(): bool
    {
        return $this->role === self::ROLE_PENGURUS;
    }

    public function isKetua(): bool
    {
        return $this->role === self::ROLE_KETUA;
    }

    /**
     * Check if user is regular user.
     */
    public function isBendahara(): bool
    {
        return $this->role === self::ROLE_BENDAHARA;
    }

    public function isAnggota(): bool
    {
        return $this->role === self::ROLE_ANGGOTA;
    }

    /**
     * Check if user is marketplace-only customer.
     */
    public function isPelanggan(): bool
    {
        return $this->role === self::ROLE_PELANGGAN;
    }

    /**
     * Check if user is DPS.
     */
    public function isDps(): bool
    {
        return $this->role === self::ROLE_DPS;
    }

    /**
     * Get the dashboard route name for the user's role.
     */
    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_KETUA => 'ketua.dashboard',
            self::ROLE_PENGURUS => 'pengurus.dashboard',
            self::ROLE_BENDAHARA => 'bendahara.dashboard',
            self::ROLE_DPS => 'dps.dashboard',
            self::ROLE_PELANGGAN => 'pelanggan.dashboard',
            default => 'anggota.dashboard',
        };
    }

    /**
     * Simpanan (savings) belonging to this user.
     */
    public function simpanan()
    {
        return $this->hasMany(Simpanan::class);
    }

    /**
     * Inbox entries (notifications) for this user.
     */
    public function inboxEntries()
    {
        return $this->hasMany(InboxEntry::class);
    }

    /**
     * Count of unread inbox entries.
     */
    public function unreadInboxCount(): int
    {
        return $this->inboxEntries()->where('is_read', false)->count();
    }
}
