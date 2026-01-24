<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Performance\PerformanceMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Command to analyze current performance and create baseline report
 * 
 * This command analyzes:
 * - Slow database queries
 * - Memory usage
 * - File sizes (JS/CSS/Images)
 * - Cache hit rate
 * 
 * Usage: php artisan performance:analyze
 */
class AnalyzePerformanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:analyze 
                            {--export : Export detailed report to JSON file}
                            {--slow-queries : Analyze slow queries from logs}
                            {--memory : Analyze memory usage}
                            {--files : Analyze file sizes}
                            {--cache : Analyze cache performance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze current performance metrics and create baseline report';

    /**
     * Execute the console command.
     */
    public function handle(PerformanceMonitoringService $monitoringService): int
    {
        $this->info('🔍 Starting Performance Analysis...');
        $this->newLine();

        $report = [
            'timestamp' => now()->toIso8601String(),
            'database' => [],
            'memory' => [],
            'files' => [],
            'cache' => [],
            'queries' => [],
        ];

        // Analyze database
        $this->info('📊 Analyzing Database...');
        $report['database'] = $this->analyzeDatabase();
        $this->displayDatabaseAnalysis($report['database']);

        // Analyze slow queries
        if ($this->option('slow-queries') || !$this->hasOptions()) {
            $this->newLine();
            $this->info('🐌 Analyzing Slow Queries...');
            $report['queries'] = $this->analyzeSlowQueries();
            $this->displaySlowQueries($report['queries']);
        }

        // Analyze memory
        if ($this->option('memory') || !$this->hasOptions()) {
            $this->newLine();
            $this->info('💾 Analyzing Memory Usage...');
            $report['memory'] = $this->analyzeMemory();
            $this->displayMemoryAnalysis($report['memory']);
        }

        // Analyze files
        if ($this->option('files') || !$this->hasOptions()) {
            $this->newLine();
            $this->info('📁 Analyzing File Sizes...');
            $report['files'] = $this->analyzeFiles();
            $this->displayFilesAnalysis($report['files']);
        }

        // Analyze cache
        if ($this->option('cache') || !$this->hasOptions()) {
            $this->newLine();
            $this->info('⚡ Analyzing Cache Performance...');
            $report['cache'] = $this->analyzeCache();
            $this->displayCacheAnalysis($report['cache']);
        }

        // Get baseline metrics
        $this->newLine();
        $this->info('📈 Getting Baseline Metrics...');
        $baselineMetrics = $monitoringService->getBaselineMetrics();
        $report['baseline'] = $baselineMetrics;

        // Export if requested
        if ($this->option('export')) {
            $this->newLine();
            $this->info('💾 Exporting detailed report...');
            $path = $this->exportReport($report);
            $this->info("✅ Report exported to: {$path}");
        }

        // Summary
        $this->newLine();
        $this->displaySummary($report);

        $this->newLine();
        $this->info('✅ Performance analysis completed!');

        return Command::SUCCESS;
    }

    /**
     * Check if any specific options were provided
     */
    private function hasOptions(): bool
    {
        return $this->option('slow-queries') || 
               $this->option('memory') || 
               $this->option('files') || 
               $this->option('cache');
    }

    /**
     * Analyze database performance
     */
    private function analyzeDatabase(): array
    {
        try {
            $connection = DB::connection();
            
            // Get database size
            $dbSize = DB::selectOne("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb,
                    COUNT(*) AS table_count
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            
            // Get table sizes
            $tables = DB::select("
                SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    table_rows
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");
            
            // Get index information
            $indexes = DB::select("
                SELECT 
                    table_name,
                    COUNT(*) AS index_count
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                GROUP BY table_name
                ORDER BY index_count DESC
                LIMIT 10
            ");
            
            // Check for missing indexes on foreign keys
            $missingIndexes = $this->findMissingIndexes();
            
            return [
                'database_size_mb' => $dbSize->size_mb ?? 0,
                'table_count' => $dbSize->table_count ?? 0,
                'largest_tables' => $tables,
                'index_info' => $indexes,
                'missing_indexes' => $missingIndexes,
                'connection' => $connection->getName(),
            ];
        } catch (\Exception $e) {
            $this->error("Error analyzing database: {$e->getMessage()}");
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Find missing indexes on foreign keys
     */
    private function findMissingIndexes(): array
    {
        try {
            $missing = [];
            
            // Check items table
            $itemsIndexes = DB::select("SHOW INDEXES FROM items");
            $indexedColumns = array_column($itemsIndexes, 'Column_name');
            
            $foreignKeys = ['user_id', 'category_id'];
            foreach ($foreignKeys as $fk) {
                if (!in_array($fk, $indexedColumns)) {
                    $missing[] = [
                        'table' => 'items',
                        'column' => $fk,
                        'type' => 'foreign_key',
                    ];
                }
            }
            
            return $missing;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Analyze slow queries from logs
     */
    private function analyzeSlowQueries(): array
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return ['message' => 'Log file not found'];
        }
        
        $logContent = File::get($logPath);
        
        // Find slow query entries
        $slowQueries = [];
        $lines = explode("\n", $logContent);
        
        foreach ($lines as $line) {
            if (str_contains($line, 'Slow query detected')) {
                // Try to extract query info
                if (preg_match('/time_ms["\']:\s*(\d+\.?\d*)/', $line, $matches)) {
                    $slowQueries[] = [
                        'line' => $line,
                        'time_ms' => (float) $matches[1],
                    ];
                }
            }
        }
        
        // Get last 20 slow queries
        $slowQueries = array_slice($slowQueries, -20);
        
        return [
            'total_found' => count($slowQueries),
            'queries' => $slowQueries,
            'average_time_ms' => count($slowQueries) > 0 
                ? array_sum(array_column($slowQueries, 'time_ms')) / count($slowQueries) 
                : 0,
        ];
    }

    /**
     * Analyze memory usage
     */
    private function analyzeMemory(): array
    {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        return [
            'memory_limit' => $memoryLimit,
            'current_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'peak_usage_mb' => round($memoryPeak / 1024 / 1024, 2),
            'usage_percentage' => $this->calculateMemoryPercentage($memoryLimit, $memoryUsage),
            'php_version' => PHP_VERSION,
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
        ];
    }

    /**
     * Calculate memory usage percentage
     */
    private function calculateMemoryPercentage(string $limit, int $usage): float
    {
        $limitBytes = $this->convertToBytes($limit);
        if ($limitBytes == 0) {
            return 0;
        }
        return round(($usage / $limitBytes) * 100, 2);
    }

    /**
     * Convert memory limit string to bytes
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;
        
        return match ($last) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }

    /**
     * Analyze file sizes
     */
    private function analyzeFiles(): array
    {
        $analysis = [
            'js' => $this->analyzeDirectory(public_path('js'), 'js'),
            'css' => $this->analyzeDirectory(public_path('css'), 'css'),
            'images' => $this->analyzeImages(),
        ];
        
        $totalSize = 
            ($analysis['js']['total_size_mb'] ?? 0) +
            ($analysis['css']['total_size_mb'] ?? 0) +
            ($analysis['images']['total_size_mb'] ?? 0);
        
        $analysis['total_size_mb'] = round($totalSize, 2);
        
        return $analysis;
    }

    /**
     * Analyze directory
     */
    private function analyzeDirectory(string $path, string $type): array
    {
        if (!File::exists($path)) {
            return ['message' => "Directory not found: {$path}"];
        }
        
        $files = File::allFiles($path);
        $totalSize = 0;
        $fileCount = 0;
        $largestFiles = [];
        
        foreach ($files as $file) {
            $size = $file->getSize();
            $totalSize += $size;
            $fileCount++;
            
            $largestFiles[] = [
                'path' => $file->getRelativePathname(),
                'size_mb' => round($size / 1024 / 1024, 2),
            ];
        }
        
        // Sort by size and get top 10
        usort($largestFiles, fn($a, $b) => $b['size_mb'] <=> $a['size_mb']);
        $largestFiles = array_slice($largestFiles, 0, 10);
        
        return [
            'file_count' => $fileCount,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'average_size_kb' => $fileCount > 0 ? round(($totalSize / $fileCount) / 1024, 2) : 0,
            'largest_files' => $largestFiles,
        ];
    }

    /**
     * Analyze images
     */
    private function analyzeImages(): array
    {
        $publicDisk = Storage::disk('public');
        $itemsPath = 'items';
        
        if (!$publicDisk->exists($itemsPath)) {
            return ['message' => 'Items images directory not found'];
        }
        
        $totalSize = 0;
        $fileCount = 0;
        $largestFiles = [];
        
        // Get all image files
        $files = $publicDisk->allFiles($itemsPath);
        
        foreach ($files as $file) {
            if ($publicDisk->exists($file)) {
                $size = $publicDisk->size($file);
                $totalSize += $size;
                $fileCount++;
                
                $largestFiles[] = [
                    'path' => $file,
                    'size_mb' => round($size / 1024 / 1024, 2),
                ];
            }
        }
        
        // Sort by size and get top 20
        usort($largestFiles, fn($a, $b) => $b['size_mb'] <=> $a['size_mb']);
        $largestFiles = array_slice($largestFiles, 0, 20);
        
        return [
            'file_count' => $fileCount,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'average_size_kb' => $fileCount > 0 ? round(($totalSize / $fileCount) / 1024, 2) : 0,
            'largest_files' => $largestFiles,
        ];
    }

    /**
     * Analyze cache performance
     */
    private function analyzeCache(): array
    {
        $driver = config('cache.default');
        $prefix = config('cache.prefix');
        
        // Test cache operations
        $testKey = 'performance_test_' . time();
        $testValue = 'test_value';
        
        $start = microtime(true);
        Cache::put($testKey, $testValue, 60);
        $putTime = (microtime(true) - $start) * 1000;
        
        $start = microtime(true);
        $retrieved = Cache::get($testKey);
        $getTime = (microtime(true) - $start) * 1000;
        
        Cache::forget($testKey);
        
        // Count cache keys (approximate)
        $keyCount = $this->estimateCacheKeys();
        
        return [
            'driver' => $driver,
            'prefix' => $prefix,
            'put_time_ms' => round($putTime, 2),
            'get_time_ms' => round($getTime, 2),
            'working' => $retrieved === $testValue,
            'estimated_keys' => $keyCount,
        ];
    }

    /**
     * Estimate cache keys count (approximate)
     */
    private function estimateCacheKeys(): int
    {
        // This is a rough estimate based on common cache patterns
        // For database cache, we can query the cache table
        if (config('cache.default') === 'database') {
            try {
                return DB::table('cache')->count();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        return 0; // Can't estimate for other drivers easily
    }

    /**
     * Display database analysis
     */
    private function displayDatabaseAnalysis(array $data): void
    {
        $this->line("  Database Size: {$data['database_size_mb']} MB");
        $this->line("  Table Count: {$data['table_count']}");
        
        if (isset($data['largest_tables']) && count($data['largest_tables']) > 0) {
            $this->line("  Top Tables by Size:");
            foreach (array_slice($data['largest_tables'], 0, 5) as $table) {
                $this->line("    - {$table->table_name}: {$table->size_mb} MB ({$table->table_rows} rows)");
            }
        }
        
        if (isset($data['missing_indexes']) && count($data['missing_indexes']) > 0) {
            $this->warn("  ⚠️  Missing Indexes Found: " . count($data['missing_indexes']));
            foreach ($data['missing_indexes'] as $index) {
                $this->line("    - {$index['table']}.{$index['column']}");
            }
        }
    }

    /**
     * Display slow queries analysis
     */
    private function displaySlowQueries(array $data): void
    {
        if (isset($data['message'])) {
            $this->line("  {$data['message']}");
            return;
        }
        
        $this->line("  Total Slow Queries Found: {$data['total_found']}");
        if ($data['total_found'] > 0) {
            $this->line("  Average Time: {$data['average_time_ms']} ms");
            if (count($data['queries']) > 0) {
                $this->line("  Recent Slow Queries:");
                foreach (array_slice($data['queries'], -5) as $query) {
                    $this->line("    - {$query['time_ms']} ms");
                }
            }
        }
    }

    /**
     * Display memory analysis
     */
    private function displayMemoryAnalysis(array $data): void
    {
        $this->line("  Memory Limit: {$data['memory_limit']}");
        $this->line("  Current Usage: {$data['current_usage_mb']} MB ({$data['usage_percentage']}%)");
        $this->line("  Peak Usage: {$data['peak_usage_mb']} MB");
        $this->line("  OPcache: " . ($data['opcache_enabled'] ? '✅ Enabled' : '❌ Disabled'));
    }

    /**
     * Display files analysis
     */
    private function displayFilesAnalysis(array $data): void
    {
        $this->line("  Total Size: {$data['total_size_mb']} MB");
        
        if (isset($data['js'])) {
            $this->line("  JS Files: {$data['js']['file_count']} files, {$data['js']['total_size_mb']} MB");
        }
        if (isset($data['css'])) {
            $this->line("  CSS Files: {$data['css']['file_count']} files, {$data['css']['total_size_mb']} MB");
        }
        if (isset($data['images'])) {
            $this->line("  Images: {$data['images']['file_count']} files, {$data['images']['total_size_mb']} MB");
            if (isset($data['images']['largest_files']) && count($data['images']['largest_files']) > 0) {
                $this->line("  Largest Images:");
                foreach (array_slice($data['images']['largest_files'], 0, 5) as $file) {
                    $this->line("    - {$file['path']}: {$file['size_mb']} MB");
                }
            }
        }
    }

    /**
     * Display cache analysis
     */
    private function displayCacheAnalysis(array $data): void
    {
        $this->line("  Driver: {$data['driver']}");
        $this->line("  Put Time: {$data['put_time_ms']} ms");
        $this->line("  Get Time: {$data['get_time_ms']} ms");
        $this->line("  Status: " . ($data['working'] ? '✅ Working' : '❌ Not Working'));
        if (isset($data['estimated_keys'])) {
            $this->line("  Estimated Keys: {$data['estimated_keys']}");
        }
    }

    /**
     * Display summary
     */
    private function displaySummary(array $report): void
    {
        $this->newLine();
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  📊 Performance Analysis Summary");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        if (isset($report['database']['database_size_mb'])) {
            $this->line("  Database: {$report['database']['database_size_mb']} MB");
        }
        
        if (isset($report['files']['total_size_mb'])) {
            $this->line("  Files: {$report['files']['total_size_mb']} MB");
        }
        
        if (isset($report['memory']['current_usage_mb'])) {
            $this->line("  Memory: {$report['memory']['current_usage_mb']} MB");
        }
        
        if (isset($report['queries']['total_found'])) {
            $this->line("  Slow Queries: {$report['queries']['total_found']}");
        }
    }

    /**
     * Export report to JSON
     */
    private function exportReport(array $report): string
    {
        $filename = 'performance_analysis_' . now()->format('Y-m-d_His') . '.json';
        $path = storage_path("app/{$filename}");
        
        file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $path;
    }
}
