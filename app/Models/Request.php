<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Enums\RequestStatus;
use App\Traits\HasApproval;
use App\Traits\HasAttributes;
use App\Traits\HasCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Request Model
 * 
 * Represents a user's request for a specific clothing item.
 * Other users can respond with offers (sell, rent, donate).
 * 
 * Uses:
 * - HasCategory trait for category relationship
 * - HasAttributes trait for dynamic attributes
 * - HasApproval trait for content moderation
 */
class Request extends Model implements Approvable
{
    use HasFactory, HasApproval, HasCategory, HasAttributes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => RequestStatus::class,
    ];

    /**
     * Get the user who created the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all offers for this request
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Check if request is open (accepting offers)
     */
    public function isOpen(): bool
    {
        return $this->status === RequestStatus::OPEN;
    }

    /**
     * Check if request can receive new offers
     */
    public function canReceiveOffers(): bool
    {
        return $this->status->canReceiveOffers();
    }

    /**
     * Check if request is closed
     */
    public function isClosed(): bool
    {
        return $this->status === RequestStatus::CLOSED;
    }

    /**
     * Check if request is fulfilled
     */
    public function isFulfilled(): bool
    {
        return $this->status === RequestStatus::FULFILLED;
    }

    /**
     * Scope to get only open requests
     */
    public function scopeOpen($query)
    {
        return $query->where('status', RequestStatus::OPEN->value);
    }

    /**
     * Scope to get only closed requests
     */
    public function scopeClosed($query)
    {
        return $query->where('status', RequestStatus::CLOSED->value);
    }

    /**
     * Scope to get only fulfilled requests
     */
    public function scopeFulfilled($query)
    {
        return $query->where('status', RequestStatus::FULFILLED->value);
    }

    /**
     * Scope to get only published (approved) requests
     */
    public function scopePublished($query)
    {
        return $query->approved();
    }

    /**
     * Get the user who created/submitted this request
     * Required by Approvable interface
     */
    public function getSubmitter(): ?User
    {
        return $this->user;
    }

    /**
     * Get the title for display in approval lists
     * Required by Approvable interface
     */
    public function getApprovalTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the type identifier for polymorphic relationships
     * Required by Approvable interface
     */
    public function getApprovalType(): string
    {
        return 'request';
    }
}
