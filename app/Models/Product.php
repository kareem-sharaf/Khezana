<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Enums\OperationType;
use App\Traits\HasApproval;
use App\Traits\HasAttributes;
use App\Traits\HasCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Product Model (Sell/Rent/Donate Clothes)
 * 
 * This model uses:
 * - HasCategory trait for category relationship
 * - HasAttributes trait for dynamic attributes
 * - HasApproval trait for content moderation
 */
class Product extends Model implements Approvable
{
    use HasFactory, HasApproval, HasCategory, HasAttributes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'type', // sell, rent, donate (OperationType enum)
        'price',
        'status',
    ];

    protected $casts = [
        'type' => OperationType::class,
        'price' => 'decimal:2',
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
     * Scope to get products by operation type
     */
    public function scopeByType($query, OperationType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get products by category
     */
    public function scopeByCategory($query, Category|int $category)
    {
        $categoryId = $category instanceof Category ? $category->id : $category;
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get products by category or its descendants
     */
    public function scopeByCategoryOrDescendants($query, Category|int $category)
    {
        $categoryId = $category instanceof Category ? $category->id : $category;
        $category = $category instanceof Category ? $category : Category::find($categoryId);
        
        if (!$category) {
            return $query;
        }

        $categoryIds = [$categoryId];
        $descendants = $category->descendants;
        $categoryIds = array_merge($categoryIds, $descendants->pluck('id')->toArray());

        return $query->whereIn('category_id', $categoryIds);
    }
}
