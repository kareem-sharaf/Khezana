<?php

declare(strict_types=1);

namespace App\Read\Items\Queries;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Models\Item;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrowseItemsQuery
{
    public function execute(array $filters = [], ?string $sort = null, int $page = 1, int $perPage = 20, ?User $user = null): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        // Phase 1.3: Use scope for published and available items
        $query = Item::query()
            ->where(function($q) use ($user) {
                // Public items: only approved and available (using scope)
                $q->publishedAndAvailable();
                
                // If user is authenticated, also show their own items (regardless of approval status)
                if ($user) {
                    $q->orWhere(function($own) use ($user) {
                        $own->where('user_id', $user->id)
                            ->whereNull('deleted_at')
                            ->whereNull('archived_at');
                    });
                }
            });

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        if (isset($filters['operation_type']) && $filters['operation_type']) {
            $query->where('operation_type', $filters['operation_type']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['condition']) && $filters['condition']) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['price_min']) && $filters['price_min']) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (isset($filters['price_max']) && $filters['price_max']) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        match($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'updated_at_desc' => $query->orderBy('updated_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        // Phase 1.3: Select only needed columns (already optimized)
        $query->select('id', 'title', 'slug', 'description', 'condition', 'price', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at');

        // Phase 1.3: Optimized Eager Loading - only load what's needed
        $query->with([
            'user:id,name', // Only id and name from users table
            'category:id,name,slug', // Only id, name, slug from categories table
            'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary', 'path_webp')
                                   ->orderBy('is_primary', 'desc')
                                   ->orderBy('id', 'asc'), // Get primary first, then by ID
        ]);

        $paginator = $query->paginate(min($perPage, 50), ['*'], 'page', max(1, $page));

        $duration = (microtime(true) - $startTime) * 1000;
        $threshold = config('app.slow_query_threshold', 100);
        
        if ($duration > $threshold) {
            Log::warning('Slow query detected: BrowseItemsQuery', [
                'duration_ms' => round($duration, 2),
                'filters' => $filters,
                'sort' => $sort,
                'page' => $page,
            ]);
        }

        return $paginator;
    }
}
