<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'code', 'purpose', 'expires_at', 'attempts', 'invalidated_at', 'created_at',
    ];

    protected $casts = [
        'expires_at'     => 'datetime',
        'invalidated_at' => 'datetime',
        'created_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return $this->invalidated_at === null
            && $this->expires_at->isFuture()
            && $this->attempts < 5;
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
        if ($this->attempts >= 5) {
            $this->update(['invalidated_at' => now()]);
        }
    }

    public function invalidate(): void
    {
        $this->update(['invalidated_at' => now()]);
    }

    public function canResend(): bool
    {
        return $this->created_at->diffInSeconds(now()) >= 60;
    }
}
