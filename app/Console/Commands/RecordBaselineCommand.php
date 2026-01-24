<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Performance\PerformanceMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Command to record baseline performance metrics
 * 
 * This command measures and records current performance metrics
 * before starting optimization phases.
 * 
 * Usage: php artisan performance:baseline
 */
class RecordBaselineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:baseline 
                            {--export : Export report to JSON file}
                            {--test-operations : Test actual operations (item creation, etc.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record baseline performance metrics for optimization tracking';

    /**
     * Execute the console command.
     */
    public function handle(PerformanceMonitoringService $monitoringService): int
    {
        $this->info('🚀 Starting Baseline Performance Measurement...');
        $this->newLine();

        // Get baseline metrics
        $this->info('📊 Collecting system metrics...');
        $metrics = $monitoringService->getBaselineMetrics();

        // Display database metrics
        $this->displaySection('Database Metrics', $metrics['database'] ?? []);

        // Display cache metrics
        $this->displaySection('Cache Metrics', $metrics['cache'] ?? []);

        // Display storage metrics
        $this->displaySection('Storage Metrics', $metrics['storage'] ?? []);

        // Display system metrics
        $this->displaySection('System Metrics', $metrics['system'] ?? []);

        // Test operations if requested
        if ($this->option('test-operations')) {
            $this->newLine();
            $this->info('🧪 Testing operations...');
            $this->testOperations($monitoringService);
        }

        // Export if requested
        if ($this->option('export')) {
            $this->newLine();
            $this->info('💾 Exporting baseline report...');
            $path = $monitoringService->exportBaselineReport();
            $this->info("✅ Report exported to: {$path}");
        }

        $this->newLine();
        $this->info('✅ Baseline measurement completed!');
        $this->info('📝 Next steps:');
        $this->line('   1. Run detailed analysis: php artisan performance:analyze --export');
        $this->line('   2. Review the metrics above');
        $this->line('   3. Access Telescope at: ' . config('app.url') . '/' . config('telescope.path', 'telescope'));
        $this->line('   4. Monitor slow queries in logs');
        $this->line('   5. Proceed to Phase 0.3: إعداد بيئة التطوير');

        return Command::SUCCESS;
    }

    /**
     * Display a section of metrics
     */
    private function displaySection(string $title, array $data): void
    {
        $this->newLine();
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  {$title}");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->line("  {$key}:");
                foreach ($value as $subKey => $subValue) {
                    $this->line("    {$subKey}: " . $this->formatValue($subValue));
                }
            } else {
                $this->line("  {$key}: " . $this->formatValue($value));
            }
        }
    }

    /**
     * Format value for display
     */
    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '✅ Yes' : '❌ No';
        }

        if (is_null($value)) {
            return 'N/A';
        }

        return (string) $value;
    }

    /**
     * Test actual operations
     */
    private function testOperations(PerformanceMonitoringService $monitoringService): void
    {
        // Test database query
        $this->line('  Testing database query...');
        $start = microtime(true);
        DB::table('items')->count();
        $duration = (microtime(true) - $start) * 1000;
        $monitoringService->recordMetric('test_db_query', $duration);
        $this->line("    ✅ Database query: {$duration}ms");

        // Test cache
        $this->line('  Testing cache...');
        $start = microtime(true);
        \Illuminate\Support\Facades\Cache::put('test_key', 'test_value', 60);
        \Illuminate\Support\Facades\Cache::get('test_key');
        $duration = (microtime(true) - $start) * 1000;
        $monitoringService->recordMetric('test_cache', $duration);
        $this->line("    ✅ Cache operation: {$duration}ms");
    }
}
