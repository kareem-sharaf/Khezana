<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    private const TTL_INDEX = 300; // 5 minutes (Performance fix #4: increased from 60s)
    private const TTL_SHOW = 600; // 10 minutes
    private const TTL_CATEGORIES = 3600; // 1 hour
    private const TTL_ATTRIBUTES = 3600; // 1 hour

    public function getItemsIndexKey(array $filters, ?string $sort, int $page, string $locale, ?int $userId = null, ?int $perPage = null): string
    {
        // Performance fix #9: Remove userId from cache key to prevent fragmentation
        // User-specific data can be added in view layer
        // Include per_page in cache key to ensure different pagination sizes are cached separately
        $perPagePart = $perPage ? ":per_page:{$perPage}" : "";
        $filterHash = md5(json_encode($filters) . $sort . $page . ($perPage ?? ''));
        return "items:index:{$locale}:page:{$page}{$perPagePart}:filters:{$filterHash}";
    }

    public function getItemShowKey(int $itemId, ?string $slug, ?int $userId, string $locale): string
    {
        $userPart = $userId ? ":user:{$userId}" : ":guest";
        $slugPart = $slug ? ":{$slug}" : "";
        return "item:{$itemId}{$slugPart}{$userPart}:{$locale}";
    }

    public function getRequestsIndexKey(array $filters, ?string $sort, int $page, string $locale): string
    {
        $filterHash = md5(json_encode($filters) . $sort . $page);
        return "requests:index:{$locale}:page:{$page}:filters:{$filterHash}";
    }

    public function getRequestShowKey(int $requestId, ?string $slug, ?int $userId, string $locale): string
    {
        $userPart = $userId ? ":user:{$userId}" : ":guest";
        $slugPart = $slug ? ":{$slug}" : "";
        return "request:{$requestId}{$slugPart}{$userPart}:{$locale}";
    }

    public function getCategoriesKey(): string
    {
        return "categories:tree";
    }

    public function getCategoryAttributesKey(int $categoryId): string
    {
        return "category:{$categoryId}:attributes";
    }

    public function rememberItemsIndex(callable $callback, array $filters, ?string $sort, int $page, string $locale, ?int $userId = null, ?int $perPage = null)
    {
        // Performance fix #9: Ignore userId parameter to prevent cache fragmentation
        $key = $this->getItemsIndexKey($filters, $sort, $page, $locale, null, $perPage);
        return $this->remember($key, self::TTL_INDEX, $callback, 'items_index');
    }

    public function rememberItemShow(callable $callback, int $itemId, ?string $slug, ?int $userId, string $locale)
    {
        $key = $this->getItemShowKey($itemId, $slug, $userId, $locale);
        return $this->remember($key, self::TTL_SHOW, $callback, 'item_show');
    }

    public function rememberRequestsIndex(callable $callback, array $filters, ?string $sort, int $page, string $locale)
    {
        $key = $this->getRequestsIndexKey($filters, $sort, $page, $locale);
        return $this->remember($key, self::TTL_INDEX, $callback, 'requests_index');
    }

    public function rememberRequestShow(callable $callback, int $requestId, ?string $slug, ?int $userId, string $locale)
    {
        $key = $this->getRequestShowKey($requestId, $slug, $userId, $locale);
        return $this->remember($key, self::TTL_SHOW, $callback, 'request_show');
    }

    public function rememberCategories(callable $callback)
    {
        $key = $this->getCategoriesKey();
        return $this->remember($key, self::TTL_CATEGORIES, $callback, 'categories');
    }

    public function rememberCategoryAttributes(int $categoryId, callable $callback)
    {
        $key = $this->getCategoryAttributesKey($categoryId);
        return $this->remember($key, self::TTL_ATTRIBUTES, $callback, 'category_attributes');
    }

    private function remember(string $key, int $ttl, callable $callback, string $context): mixed
    {
        $startTime = microtime(true);
        
        // Performance fix #10: Remove Cache::has() - Cache::remember() already checks internally
        $result = Cache::remember($key, $ttl, function () use ($callback, $key, $context, $ttl) {
            $result = $callback();
            
            if (config('app.log_cache_misses', false)) {
                Log::info("Cache miss", [
                    'key' => $key,
                    'context' => $context,
                    'ttl' => $ttl,
                ]);
            }
            
            return $result;
        });
        
        $duration = (microtime(true) - $startTime) * 1000;
        
        if ($duration > config('app.slow_query_threshold', 100)) {
            Log::warning("Slow cache operation", [
                'key' => $key,
                'context' => $context,
                'duration_ms' => round($duration, 2),
            ]);
        }
        
        return $result;
    }

    public function invalidateItem(int $itemId): void
    {
        // Invalidate specific item cache
        $patterns = [
            "item:{$itemId}:*",
        ];
        $this->invalidatePatterns($patterns);
        
        // Invalidate all items index pages (to show new items immediately)
        $this->invalidateItemsIndex();
    }

    /**
     * Invalidate all items index cache
     * This ensures new items appear immediately
     */
    public function invalidateItemsIndex(): void
    {
        $pattern = "items:index:*";
        $this->invalidateByPrefix($pattern);
    }

    public function invalidateRequest(int $requestId): void
    {
        $patterns = [
            "request:{$requestId}:*",
            "requests:index:*",
        ];
        
        $this->invalidatePatterns($patterns);
    }

    public function invalidateCategories(): void
    {
        Cache::forget($this->getCategoriesKey());
    }

    public function invalidateCategoryAttributes(int $categoryId): void
    {
        Cache::forget($this->getCategoryAttributesKey($categoryId));
    }

    private function invalidatePatterns(array $patterns): void
    {
        foreach ($patterns as $pattern) {
            if (Cache::getStore() instanceof \Illuminate\Cache\TaggedCache) {
                Cache::tags(explode(':', $pattern))->flush();
            } else {
                $this->invalidateByPrefix($pattern);
            }
        }
    }

    private function invalidateByPrefix(string $prefix): void
    {
        try {
            $store = Cache::getStore();
            if ($store instanceof \Illuminate\Cache\RedisStore) {
                $redis = $store->getRedis();
                // Use SCAN instead of KEYS for better performance in production
                $cursor = 0;
                $allKeys = [];
                do {
                    $result = $redis->scan($cursor, ['match' => $prefix, 'count' => 100]);
                    $cursor = $result[0];
                    $allKeys = array_merge($allKeys, $result[1]);
                } while ($cursor !== 0);
                
                if (!empty($allKeys)) {
                    $redis->del($allKeys);
                }
            } else {
                // For file/database cache, try to use keys() if available
                // Otherwise, we'll rely on TTL expiration
                // Note: File cache doesn't support pattern matching efficiently
                Log::debug('Cache invalidation by prefix not fully supported for this cache driver', [
                    'prefix' => $prefix,
                    'driver' => get_class($store),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Cache invalidation failed', [
                'prefix' => $prefix,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
