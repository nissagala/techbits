<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'status', 'subtotal', 'shipping_fee', 'total',
        'shipping_address', 'payment_cardholder', 'payment_last4', 'payment_expiry', 'placed_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'placed_at'        => 'datetime',
    ];

    public static $statusFlow = [
        'pending'    => 'processing',
        'processing' => 'shipped',
        'shipped'    => 'delivered',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at');
    }

    public function setOrderNumber(): void
    {
        $this->order_number = 'TB-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        $this->save();
    }

    public function canAdvance(): bool
    {
        return isset(self::$statusFlow[$this->status]);
    }

    public function nextStatus(): ?string
    {
        return self::$statusFlow[$this->status] ?? null;
    }

    public function canCancel(): bool
    {
        return ! in_array($this->status, ['delivered', 'cancelled']);
    }
}
