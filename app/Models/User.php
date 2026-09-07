<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'avatar_url',
        'cover_url',
        'membership_tier',
        'joined_date',
        'gender',
        'birthday',
        'coins',
        'voucher_count',
        'favorite_count',
        'order_count',
        'review_count',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'coins' => 'integer',
            'voucher_count' => 'integer',
            'favorite_count' => 'integer',
            'order_count' => 'integer',
            'review_count' => 'integer',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class)->orderByDesc('is_default');
    }

    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }
}
