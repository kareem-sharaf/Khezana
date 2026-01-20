<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Traits\HasApproval;
use App\Traits\HasAttributes;
use App\Traits\HasCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Item Model
 * 
 * Represents a unified item that can be sold, rented, or donated.
 * Uses:
 * - HasCategory trait for category relationship
 * - HasAttributes trait for dynamic attributes
 * - HasApproval trait for content moderation
 */
class Item extends Model implements Approvable
{
    use HasFactory, HasApproval, HasCategory, HasAttributes;

    protected $fillable = [
        'user_id',
        'category_id',
        'operation_type',
        'title',
        'description',
        'price',
        'deposit_amount',
        'is_available',
    ];

    protected $casts = [
        'operation_type' => OperationType::class,
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the user who owns the item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all images for this item
     */
    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class)->orderBy('is_primary', 'desc');
    }

    /**
     * Get the primary image
     */
    public function primaryImage(): ?ItemImage
    {
        return $this->images()->where('is_primary', true)->first();
    }

    /**
     * Get the user who created/submitted this item
     * Required by Approvable interface
     */
    public function getSubmitter(): ?User
    {
        return $this->user;
    }

    /**
     * Get the title/name for display in approval lists
     * Required by Approvable interface
     */
    public function getApprovalTitle(): string
    {
        return $this->title ?? 'Untitled Item';
    }

    /**
     * Get the type identifier for polymorphic relationships
     * Required by Approvable interface
     */
    public function getApprovalType(): string
    {
        return self::class;
    }

    /**
     * Check if item requires price
     */
    public function requiresPrice(): bool
    {
        return in_array($this->operation_type, [OperationType::SELL, OperationType::RENT]);
    }

    /**
     * Check if item requires deposit
     */
    public function requiresDeposit(): bool
    {
        return $this->operation_type === OperationType::RENT;
    }

    /**
     * Scope to get only available items
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to get only unavailable items
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope to filter by operation type
     */
    public function scopeByOperationType($query, OperationType $type)
    {
        return $query->where('operation_type', $type);
    }

    /**
     * Scope to get only published (approved) items
     */
    public function scopePublished($query)
    {
        return $query->approved();
    }
}
