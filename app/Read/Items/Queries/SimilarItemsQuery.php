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

        // Phase 1.3: Use scope for published and available items
        $query = Item::query()
            ->where('id', '!=', $itemId) // Exclude current item
            ->where(function ($q) use ($user) {
                // Public items: only approved and available (using scope)
                $q->publishedAndAvailable();

                // If user is authenticated, also show their own items (regardless of approval status)
                if ($user) {
                    $q->orWhere(function ($own) use ($user) {
                        $own->where('user_id', $user->id)
                            ->whereNull('deleted_at')
                            ->whereNull('archived_at');
                    });
                }
            });

        // Filter by category if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter by operation type if provided
        if ($operationType) {
            $query->where('operation_type', $operationType);
        }

        // Filter by condition if provided (optional - don't require it)
        if ($condition) {
            $query->where('condition', $condition);
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc');

        // Phase 1.3: Select only needed columns
        $query->select('id', 'title', 'slug', 'description', 'condition', 'price', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at');

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
