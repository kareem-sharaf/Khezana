<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferAttempt extends Model
{
    protected $table = 'offer_attempts';

    protected $fillable = [
        'user_id',
        'request_id',
        'channel',
        'operation_type',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who made the offer attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the request that the offer was made for.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get channel label for display.
     */
    public function getChannelLabelAttribute(): string
    {
        return match($this->channel) {
            'whatsapp' => 'واتساب',
            'telegram' => 'تلغرام',
            default => $this->channel,
        };
    }

    /**
     * Get operation type label for display.
     */
    public function getOperationTypeLabelAttribute(): string
    {
        return match($this->operation_type) {
            'sell' => 'بيع',
            'rent' => 'تأجير',
            'donate' => 'تبرع',
            default => $this->operation_type ?? '-',
        };
    }
}
