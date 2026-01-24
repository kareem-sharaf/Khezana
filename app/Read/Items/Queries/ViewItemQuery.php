<?php

declare(strict_types=1);

namespace App\Read\Items\Queries;

use App\Models\Item;
use App\Models\User;
use App\Read\Items\Models\ItemReadModel;
use Illuminate\Support\Facades\Log;

class ViewItemQuery
{
    public function execute(int $itemId, ?string $slug = null, ?User $user = null): ?ItemReadModel
    {
        $startTime = microtime(true);
        
        // Phase 2.2: Use publishedAndAvailable scope; owner can always view
        $query = Item::query()
            ->where('id', $itemId)
            ->where(function ($q) use ($user) {
                $q->where(fn ($sub) => $sub->publishedAndAvailable());
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });

        if ($slug) {
            $query->where('slug', $slug);
        }

        $item = $query->select('id', 'title', 'slug', 'description', 'condition', 'price', 'deposit_amount', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at')
            ->with([
                'user:id,name,created_at',
                'category:id,name,slug,description',
                'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary', 'path_webp')
                                       ->orderBy('is_primary', 'desc')
                                       ->orderBy('id', 'asc'),
                'itemAttributes' => fn($q) => $q->select('id', 'attributable_id', 'attributable_type', 'attribute_id', 'value')
                                               ->with('attribute:id,name,type'),
            ])
            ->first();

        if (!$item) {
            return null;
        }

        $duration = (microtime(true) - $startTime) * 1000;
        $threshold = config('app.slow_query_threshold', 100);
        
        if ($duration > $threshold) {
            Log::warning('Slow query detected: ViewItemQuery', [
                'duration_ms' => round($duration, 2),
                'item_id' => $itemId,
                'has_user' => $user !== null,
            ]);
        }

        return ItemReadModel::fromModel($item);
    }
}
