<?php

declare(strict_types=1);

namespace App\Read\Items\Queries;

use App\Models\Item;
use App\Models\User;
use App\Read\Items\Models\ItemReadModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SimilarItemsQuery
{
    public function execute(int $itemId, ?int $categoryId = null, ?string $operationType = null, ?string $condition = null, int $limit = 12, ?User $user = null): Collection
    {
        $startTime = microtime(true);

        // Performance fix: Use JOINs instead of scope for better performance
        $query = Item::query()
            ->where('items.id', '!=', $itemId) // Exclude current item
            ->leftJoin('approvals', function($join) {
                $join->on('items.id', '=', 'approvals.approvable_id')
                     ->where('approvals.approvable_type', '=', Item::class);
            })
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->whereNull('items.deleted_at')
            ->whereNull('items.archived_at')
            ->where(function ($q) use ($user) {
                // Public items: approved and available (must have approval and active category)
                $q->where(function($public) {
                    $public->whereNotNull('approvals.id')
                           ->where('approvals.status', '=', \App\Enums\ApprovalStatus::APPROVED->value)
                           ->whereNotNull('categories.id')
                           ->where('categories.is_active', true)
                           ->where(function($avail) {
                               $avail->where('items.availability_status', \App\Enums\ItemAvailability::AVAILABLE->value)
                                     ->orWhere('items.is_available', true);
                           });
                });

                // If user is authenticated, also show their own items
                if ($user) {
                    $q->orWhere('items.user_id', $user->id);
                }
            });

        // Filter by category if provided
        if ($categoryId) {
            $query->where('items.category_id', $categoryId);
        }

        // Filter by operation type if provided
        if ($operationType) {
            $query->where('items.operation_type', $operationType);
        }

        // Filter by condition if provided (optional - don't require it)
        if ($condition) {
            $query->where('items.condition', $condition);
        }

        // Order by most recent first
        $query->orderBy('items.created_at', 'desc');

        // Performance fix: Select only needed columns with table prefixes
        $query->distinct()
              ->select('items.id', 'items.title', 'items.slug', 'items.description', 'items.condition', 
                      'items.price', 'items.operation_type', 'items.availability_status', 
                      'items.user_id', 'items.category_id', 'items.created_at', 'items.updated_at');

        // Phase 1.3: Optimized Eager Loading - only load what's needed
        $query->with([
            'user:id,name', // Only id and name from users table
            'category:id,name,slug', // Only id, name, slug from categories table
            'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary')
                ->orderBy('is_primary', 'desc')
                ->limit(1), // Only primary image or first image
        ]);

        $items = $query->limit($limit)->get();

        $duration = (microtime(true) - $startTime) * 1000;
        $threshold = config('app.slow_query_threshold', 100);

        if ($duration > $threshold) {
            Log::warning('Slow query detected: SimilarItemsQuery', [
                'duration_ms' => round($duration, 2),
                'item_id' => $itemId,
                'category_id' => $categoryId,
                'limit' => $limit,
            ]);
        }

        // Convert to ItemReadModel collection
        return $items->map(fn($item) => ItemReadModel::fromModel($item));
    }
}
