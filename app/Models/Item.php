<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Approvable;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\User;
use App\Traits\HasApproval;
use App\Traits\HasAttributes;
use App\Traits\HasCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
    use HasFactory, HasApproval, HasCategory, HasAttributes, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'category_id',
        'operation_type',
        'title',
        'slug',
        'description',
        'governorate',
        'condition',
        'price',
        'deposit_amount',
        'is_available',
        'availability_status',
        'archived_at',
    ];

    protected $casts = [
        'operation_type' => OperationType::class,
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'is_available' => 'boolean',
        'availability_status' => ItemAvailability::class,
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who owns the item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch where this item is located
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Check if the item is in a branch
     */
    public function isInBranch(): bool
    {
        return $this->branch_id !== null;
    }

    /**
     * Check if the item is with the seller (not in a branch)
     */
    public function isWithSeller(): bool
    {
        return $this->branch_id === null;
    }

    /**
     * Check if the item requires verification at a branch
     */
    public function isVerificationRequired(): bool
    {
        return $this->approvalRelation?->status === \App\Enums\ApprovalStatus::VERIFICATION_REQUIRED;
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

    /**
     * Phase 1.3: Scope to get published and available items
     * This is the most common query pattern
     */
    public function scopePublishedAndAvailable($query)
    {
        return $query->whereHas('approvalRelation', fn($a) =>
            $a->where('status', \App\Enums\ApprovalStatus::APPROVED->value)
        )
        ->where(function ($q) {
            $q->where('availability_status', ItemAvailability::AVAILABLE->value)
              ->orWhere('is_available', true);
        })
        ->whereNull('deleted_at')
        ->whereNull('archived_at');
    }

    /**
     * Phase 2.1: Scope for search - Full-Text when available (MySQL), fallback to LIKE (SQLite, etc.)
     */
    public function scopeSearch($query, string $term)
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        // Performance fix: Use table prefix when joins are present
        $hasJoins = $this->hasJoins($query);
        $tablePrefix = $hasJoins ? 'items.' : '';

        $driver = $query->getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return $query->where(function ($q) use ($term, $tablePrefix) {
                $q->where($tablePrefix . 'title', 'like', "%{$term}%")
                  ->orWhere($tablePrefix . 'description', 'like', "%{$term}%");
            });
        }

        try {
            // For full-text search, we need to specify table prefix
            if ($hasJoins) {
                return $query->where(function ($q) use ($term) {
                    $q->whereRaw('MATCH(items.title, items.description) AGAINST(? IN NATURAL LANGUAGE MODE)', [$term]);
                });
            }
            return $query->whereFullText(['title', 'description'], $term);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Full-text search failed, using LIKE', [
                'error' => $e->getMessage(),
                'term' => $term,
            ]);

            return $query->where(function ($q) use ($term, $tablePrefix) {
                $q->where($tablePrefix . 'title', 'like', "%{$term}%")
                  ->orWhere($tablePrefix . 'description', 'like', "%{$term}%");
            });
        }
    }

    /**
     * Check if query has joins
     */
    private function hasJoins($query): bool
    {
        $joins = $query->getQuery()->joins ?? [];
        return !empty($joins);
    }

    /**
     * Ensure item can receive offers (BR-024)
     *
     * @throws \Exception If item cannot receive offers
     */
    public function ensureCanReceiveOffers(): void
    {
        if (!$this->isApproved()) {
            throw new \Exception('Item must be approved to receive offers.');
        }

        $isAvailable = $this->availability_status
            ? $this->availability_status === ItemAvailability::AVAILABLE
            : $this->is_available;

        if (!$isAvailable) {
            throw new \Exception('Item must be available to receive offers.');
        }
    }

    public function ensureCanBeModified(?User $user = null): void
    {
        // Admins can always modify items, even if pending
        if ($user && $user->hasAnyRole(['admin', 'super_admin'])) {
            return;
        }

        if ($this->isPending()) {
            throw new \Exception('Cannot modify item while it is pending approval.');
        }
    }

    public function archive(): void
    {
        $this->update(['archived_at' => now()]);
        $this->delete();
    }

    /**
     * Check if the item is archived
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Item $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title);
            }
        });
    }
}
