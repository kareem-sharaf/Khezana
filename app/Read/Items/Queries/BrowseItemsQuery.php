<?php

declare(strict_types=1);

namespace App\Read\Items\Queries;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrowseItemsQuery
{
    public function execute(array $filters = [], ?string $sort = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        $query = Item::query()
            ->whereHas('approvalRelation', fn($q) => $q->where('status', ApprovalStatus::APPROVED->value))
            ->where(function($q) {
                $q->where('availability_status', ItemAvailability::AVAILABLE->value)
                  ->orWhere('is_available', true);
            })
            ->whereNull('deleted_at')
            ->whereNull('archived_at');

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
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

        $query->select('id', 'title', 'slug', 'description', 'condition', 'price', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at');

        $query->with([
            'user:id,name',
            'category:id,name,slug',
            'images' => fn($q) => $q->select('id', 'item_id', 'path', 'is_primary')
                                   ->orderBy('is_primary', 'desc')
                                   ->limit(1),
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
