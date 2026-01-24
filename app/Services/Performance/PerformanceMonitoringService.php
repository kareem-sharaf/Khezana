<?php

declare(strict_types=1);

namespace App\Services\Performance;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service for monitoring and recording performance metrics (KPIs)
 * 
 * This service records baseline performance metrics and tracks improvements
 * throughout the optimization phases.
 */
class PerformanceMonitoringService
{
    /**
     * Record baseline metrics for a specific operation
     *
     * @param string $operation Operation name (e.g., 'item_creation', 'page_load')
     * @param float $duration Duration in milliseconds
     * @param array $metadata Additional metadata
     */
    public function recordMetric(string $operation, float $duration, array $metadata = []): void
    {
        $key = "performance:{$operation}:" . now()->format('Y-m-d');
        
        $metrics = Cache::get($key, []);
        $metrics[] = [
            'duration' => $duration,
            'timestamp' => now()->toIso8601String(),
            'metadata' => $metadata,
        ];
        
        // Keep only last 1000 records per day
        if (count($metrics) > 1000) {
            $metrics = array_slice($metrics, -1000);
        }
        
        Cache::put($key, $metrics, now()->endOfDay());
        
        // Log slow operations
        if ($duration > $this->getSlowThreshold($operation)) {
            Log::warning("Slow operation detected: {$operation}", [
                'duration_ms' => $duration,
                'threshold_ms' => $this->getSlowThreshold($operation),
                'metadata' => $metadata,
            ]);
        }
    }

    /**
     * Get average duration for an operation
     *
     * @param string $operation Operation name
     * @param int $days Number of days to look back
     * @return float|null Average duration in milliseconds
     */
    public function getAverageDuration(string $operation, int $days = 1): ?float
    {
        $key = "performance:{$operation}:" . now()->format('Y-m-d');
        $metrics = Cache::get($key, []);
        
        if (empty($metrics)) {
            return null;
        }
        
        $durations = array_column($metrics, 'duration');
        return array_sum($durations) / count($durations);
    }

    /**
     * Get performance summary for baseline
     *
     * @return array Performance metrics summary
     */
    public function getBaselineMetrics(): array
    {
        return [
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'storage' => $this->getStorageMetrics(),
            'system' => $this->getSystemMetrics(),
        ];
    }

    /**
     * Get database performance metrics
     *
     * @return array
     */
    private function getDatabaseMetrics(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            // Get database size
            $dbSize = DB::selectOne("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            
            // Get table counts
            $tables = DB::select("SHOW TABLE STATUS");
            $totalRows = array_sum(array_column($tables, 'Rows'));
            
            return [
                'connection' => $connection->getName(),
                'database_size_mb' => $dbSize->size_mb ?? 0,
                'total_tables' => count($tables),
                'total_rows' => $totalRows,
                'status' => $pdo ? 'connected' : 'disconnected',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get database metrics', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get cache performance metrics
     *
     * @return array
     */
    private function getCacheMetrics(): array
    {
        $driver = config('cache.default');
        $prefix = config('cache.prefix');
        
        return [
            'driver' => $driver,
            'prefix' => $prefix,
            'status' => $this->testCacheConnection(),
        ];
    }

    /**
     * Get storage metrics
     *
     * @return array
     */
    private function getStorageMetrics(): array
    {
        try {
            $publicDisk = Storage::disk('public');
            $localDisk = Storage::disk('local');
            
            return [
                'public_disk' => [
                    'driver' => config('filesystems.disks.public.driver'),
                    'exists' => $publicDisk->exists('.'),
                ],
                'local_disk' => [
                    'driver' => config('filesystems.disks.local.driver'),
                    'exists' => $localDisk->exists('.'),
                ],
                'default_disk' => config('filesystems.default'),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get storage metrics', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get system metrics
     *
     * @return array
     */
    private function getSystemMetrics(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
        ];
    }

    /**
     * Test cache connection
     *
     * @return string
     */
    private function testCacheConnection(): string
    {
        try {
            $testKey = 'performance_test_' . time();
            Cache::put($testKey, 'test', 1);
            $result = Cache::get($testKey);
            Cache::forget($testKey);
            
            return $result === 'test' ? 'working' : 'failed';
        } catch (\Exception $e) {
            return 'error: ' . $e->getMessage();
        }
    }

    /**
     * Get slow threshold for an operation
     *
     * @param string $operation
     * @return float Threshold in milliseconds
     */
    private function getSlowThreshold(string $operation): float
    {
        return match ($operation) {
            'item_creation' => 5000, // 5 seconds
            'page_load' => 2000, // 2 seconds
            'search_query' => 500, // 500ms
            'image_processing' => 3000, // 3 seconds
            default => 1000, // 1 second
        };
    }

    /**
     * Export baseline report to file
     *
     * @param string $filename
     * @return string Path to exported file
     */
    public function exportBaselineReport(string $filename = 'baseline_report.json'): string
    {
        $report = [
            'timestamp' => now()->toIso8601String(),
            'metrics' => $this->getBaselineMetrics(),
            'operations' => [
                'item_creation' => [
                    'average_ms' => $this->getAverageDuration('item_creation'),
                    'threshold_ms' => $this->getSlowThreshold('item_creation'),
                ],
                'page_load' => [
                    'average_ms' => $this->getAverageDuration('page_load'),
                    'threshold_ms' => $this->getSlowThreshold('page_load'),
                ],
                'search_query' => [
                    'average_ms' => $this->getAverageDuration('search_query'),
                    'threshold_ms' => $this->getSlowThreshold('search_query'),
                ],
            ],
        ];
        
        $path = storage_path("app/{$filename}");
        file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $path;
    }
}
