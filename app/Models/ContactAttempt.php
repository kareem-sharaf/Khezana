<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactAttempt extends Model
{
    protected $table = 'contact_attempts';

    protected $fillable = [
        'user_id',
        'item_id',
        'channel',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who made the attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item that was contacted about.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
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
}
