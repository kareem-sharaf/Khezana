<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    private const TTL_INDEX = 300; // 5 minutes
    private const TTL_SHOW = 600; // 10 minutes
    private const TTL_CATEGORIES = 3600; // 1 hour
    private const TTL_ATTRIBUTES = 3600; // 1 hour

    public function getItemsIndexKey(array $filters, ?string $sort, int $page, string $locale): string
    {
        $filterHash = md5(json_encode($filters) . $sort . $page);
        return "items:index:{$locale}:page:{$page}:filters:{$filterHash}";
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

    public function rememberItemsIndex(callable $callback, array $filters, ?string $sort, int $page, string $locale)
    {
        $key = $this->getItemsIndexKey($filters, $sort, $page, $locale);
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
        $cacheHit = Cache::has($key);
        
        $result = Cache::remember($key, $ttl, function () use ($callback, $key, $context) {
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
                'cache_hit' => $cacheHit,
            ]);
        }
        
        return $result;
    }

    public function invalidateItem(int $itemId): void
    {
        $patterns = [
            "item:{$itemId}:*",
            "items:index:*",
        ];
        
        $this->invalidatePatterns($patterns);
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
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->connection();
                $keys = $redis->keys($prefix);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Cache invalidation failed', [
                'prefix' => $prefix,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
