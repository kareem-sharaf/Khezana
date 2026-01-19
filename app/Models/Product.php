<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Product Model (Example - Sell/Rent/Donate Clothes)
 * 
 * This is an example model showing how to use the Approval module
 */
class Product extends Model implements Approvable
{
    use HasFactory, HasApproval;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type', // sell, rent, donate
        'price',
        'status',
        // ... other fields
    ];

    /**
     * Get the user who owns the product
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created/submitted this product
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
        return $this->title ?? 'Untitled Product';
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
     * Scope to get only published (approved) products
     */
    public function scopePublished($query)
    {
        return $query->approved();
    }

    /**
     * Scope to get products by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
