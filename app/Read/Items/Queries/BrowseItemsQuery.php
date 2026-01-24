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
    public function execute(array $filters = [], ?string $sort = null, int $page = 1, int $perPage = 9, ?User $user = null): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        // Performance fix #1: Use JOINs instead of whereHas for better performance
        // Build query with proper structure for public items and user's own items
        $query = Item::query()
            ->leftJoin('approvals', function($join) {
                $join->on('items.id', '=', 'approvals.approvable_id')
                     ->where('approvals.approvable_type', '=', Item::class);
            })
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->whereNull('items.deleted_at')
            ->whereNull('items.archived_at')
            ->where(function($q) use ($user) {
                // Public items: approved and available (must have approval and active category)
                $q->where(function($public) {
                    $public->whereNotNull('approvals.id')
                           ->where('approvals.status', '=', ApprovalStatus::APPROVED->value)
                           ->whereNotNull('categories.id')
                           ->where('categories.is_active', true)
                           ->where(function($avail) {
                               $avail->where('items.availability_status', ItemAvailability::AVAILABLE->value)
                                     ->orWhere('items.is_available', true);
                           });
                });
                
                // If user is authenticated, also show their own items (approved or pending only)
                if ($user) {
                    $q->orWhere(function($own) use ($user) {
                        $own->where('items.user_id', $user->id)
                            ->whereNotNull('approvals.id')
                            ->whereIn('approvals.status', [
                                ApprovalStatus::APPROVED->value,
                                ApprovalStatus::PENDING->value
                            ]);
                    });
                }
            });

        if (isset($filters['search']) && $filters['search']) {
            // Performance fix: Use table prefix for search when joins are present
            $term = trim($filters['search']);
            if ($term !== '') {
                $driver = $query->getConnection()->getDriverName();
                if ($driver === 'mysql') {
                    try {
                        $query->where(function ($q) use ($term) {
                            $q->whereRaw('MATCH(items.title, items.description) AGAINST(? IN NATURAL LANGUAGE MODE)', [$term]);
                        });
                    } catch (\Throwable $e) {
                        $query->where(function ($q) use ($term) {
                            $q->where('items.title', 'like', "%{$term}%")
                              ->orWhere('items.description', 'like', "%{$term}%");
                        });
                    }
                } else {
                    $query->where(function ($q) use ($term) {
                        $q->where('items.title', 'like', "%{$term}%")
                          ->orWhere('items.description', 'like', "%{$term}%");
                    });
                }
            }
        }

        // Validate and apply operation_type filter
        if (isset($filters['operation_type']) && $filters['operation_type']) {
            $validTypes = ['sell', 'rent', 'donate'];
            if (in_array($filters['operation_type'], $validTypes)) {
                $query->where('items.operation_type', $filters['operation_type']);
            }
        }

        // Validate and apply category_id filter - already joined, just filter
        if (isset($filters['category_id']) && $filters['category_id']) {
            $categoryId = (int) $filters['category_id'];
            $query->where('items.category_id', $categoryId);
        }

        // Validate and apply condition filter
        if (isset($filters['condition']) && $filters['condition']) {
            $validConditions = ['new', 'used'];
            if (in_array($filters['condition'], $validConditions)) {
                $query->where('items.condition', $filters['condition']);
            }
        }

        // Apply price filters - exclude donate items from price filtering
        $hasPriceFilter = (isset($filters['price_min']) && $filters['price_min']) || 
                         (isset($filters['price_max']) && $filters['price_max']);
        
        if ($hasPriceFilter) {
            $query->where(function($q) use ($filters) {
                // Always include donate items (they don't have price restrictions)
                $q->where('items.operation_type', 'donate')
                  ->orWhere(function($priceQ) use ($filters) {
                      // Apply price filters only to sell and rent items
                      $priceQ->whereIn('items.operation_type', ['sell', 'rent']);
                      
                      if (isset($filters['price_min']) && $filters['price_min']) {
                          $priceQ->where('items.price', '>=', (float) $filters['price_min']);
                      }
                      
                      if (isset($filters['price_max']) && $filters['price_max']) {
                          $priceQ->where('items.price', '<=', (float) $filters['price_max']);
                      }
                  });
            });
        }

        match($sort) {
            'price_asc' => $query->orderBy('items.price', 'asc'),
            'price_desc' => $query->orderBy('items.price', 'desc'),
            'title_asc' => $query->orderBy('items.title', 'asc'),
            'title_desc' => $query->orderBy('items.title', 'desc'),
            'updated_at_desc' => $query->orderBy('items.updated_at', 'desc'),
            default => $query->orderBy('items.created_at', 'desc'),
        };

        // Performance fix: Select only needed columns with table prefixes
        // Use groupBy to avoid duplicate rows from joins and ensure proper pagination
        $query->groupBy('items.id', 'items.title', 'items.slug', 'items.description', 'items.condition', 
                       'items.price', 'items.operation_type', 'items.availability_status', 
                       'items.user_id', 'items.category_id', 'items.created_at', 'items.updated_at')
              ->select('items.id', 'items.title', 'items.slug', 'items.description', 'items.condition', 
                      'items.price', 'items.operation_type', 'items.availability_status', 
                      'items.user_id', 'items.category_id', 'items.created_at', 'items.updated_at');

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
