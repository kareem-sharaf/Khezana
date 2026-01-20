<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OfferStatus;
use App\Enums\OperationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Offer Model
 * 
 * Represents an offer made by a user in response to a request.
 * An offer can be:
 * - Linked to an existing Item
 * - Or a direct offer without an Item
 * 
 * Business Rules:
 * - One user can only make one offer per request
 * - Offer can only be made on open and approved requests
 * - When accepted, request is fulfilled and other offers are rejected
 */
class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'item_id',
        'operation_type',
        'price',
        'deposit_amount',
        'status',
        'message',
    ];

    protected $casts = [
        'operation_type' => OperationType::class,
        'status' => OfferStatus::class,
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    /**
     * Get the request this offer belongs to
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the user who made the offer
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item this offer is linked to (nullable)
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Check if offer is pending
     */
    public function isPending(): bool
    {
        return $this->status === OfferStatus::PENDING;
    }

    /**
     * Check if offer is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === OfferStatus::ACCEPTED;
    }

    /**
     * Check if offer is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === OfferStatus::REJECTED;
    }

    /**
     * Check if offer is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === OfferStatus::CANCELLED;
    }

    /**
     * Check if offer can be updated
     */
    public function canBeUpdated(): bool
    {
        return $this->status->canBeUpdated();
    }

    /**
     * Check if offer is final (accepted or rejected)
     */
    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    /**
     * Scope to get only pending offers
     */
    public function scopePending($query)
    {
        return $query->where('status', OfferStatus::PENDING->value);
    }

    /**
     * Scope to get only accepted offers
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', OfferStatus::ACCEPTED->value);
    }

    /**
     * Scope to get offers for a specific request
     */
    public function scopeForRequest($query, int $requestId)
    {
        return $query->where('request_id', $requestId);
    }

    /**
     * Scope to get offers by operation type
     */
    public function scopeByOperationType($query, OperationType $type)
    {
        return $query->where('operation_type', $type->value);
    }
}
