<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status',
        'failed_login_attempts', 'locked_until',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'locked_until' => 'datetime',
        'password'     => 'hashed',
    ];

    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('created_at');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('placed_at');
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
