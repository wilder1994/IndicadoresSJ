<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USUARIO = 'usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'user_zones')->withTimestamps();
    }

    public function createdCaptures(): HasMany
    {
        return $this->hasMany(IndicatorCapture::class, 'created_by_user_id');
    }

    public function updatedCaptures(): HasMany
    {
        return $this->hasMany(IndicatorCapture::class, 'updated_by_user_id');
    }

    public function improvements(): HasMany
    {
        return $this->hasMany(Improvement::class, 'created_by_user_id');
    }

    public function documentVersions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'author_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function hasZoneAccess(int|string $zoneId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->zones()->whereKey($zoneId)->exists();
    }
}
