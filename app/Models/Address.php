<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient', 'line1', 'line2',
        'city', 'district', 'postal_code', 'phone', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toSnapshot(): array
    {
        return [
            'label'      => $this->label,
            'recipient'  => $this->recipient,
            'line1'      => $this->line1,
            'line2'      => $this->line2,
            'city'       => $this->city,
            'district'   => $this->district,
            'postal_code' => $this->postal_code,
            'phone'      => $this->phone,
        ];
    }
}
