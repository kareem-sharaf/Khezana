<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryCacheService
{
    private const TTL_TREE = 3600; // 1 hour
    private const TTL_ATTRIBUTES = 3600; // 1 hour

    public function getTree(): Collection
    {
        return Cache::remember('categories:tree', self::TTL_TREE, function () {
            return Category::query()
                ->where('is_active', true)
                ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')])
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();
        });
    }

    public function getCategoryAttributes(int $categoryId): Collection
    {
        $key = "category:{$categoryId}:attributes";
        
        return Cache::remember($key, self::TTL_ATTRIBUTES, function () use ($categoryId) {
            $category = Category::find($categoryId);
            if (!$category) {
                return collect();
            }
            
            return $category->getAllAttributes();
        });
    }

    public function invalidateTree(): void
    {
        Cache::forget('categories:tree');
    }

    public function invalidateCategoryAttributes(int $categoryId): void
    {
        Cache::forget("category:{$categoryId}:attributes");
    }

    public function invalidateAll(): void
    {
        $this->invalidateTree();
        
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getStore()->connection();
            $keys = $redis->keys('category:*:attributes');
            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }
}
