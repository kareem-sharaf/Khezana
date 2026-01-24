<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Performance\PerformanceMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Phase 6.3: Performance dashboard – KPIs and recent slow ops
 *
 * Usage: php artisan performance:dashboard
 */
class PerformanceDashboardCommand extends Command
{
    protected $signature = 'performance:dashboard
                            {--json : Output as JSON }';

    protected $description = 'Display performance dashboard (KPIs, slow ops summary)';

    public function handle(PerformanceMonitoringService $monitoring): int
    {
        $report = [
            'generated_at' => now()->toIso8601String(),
            'baseline' => [],
            'recent_slow_ops' => 0,
        ];

        try {
            $report['baseline'] = $monitoring->getBaselineMetrics();
        } catch (\Throwable $e) {
            $report['baseline'] = ['error' => $e->getMessage()];
        }

        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $content = $this->tail($logPath, 500);
            $report['recent_slow_ops'] = substr_count($content, 'Slow query detected')
                + substr_count($content, 'Slow operation detected');
        }

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        $this->info('📊 Performance Dashboard');
        $this->newLine();

        $base = $report['baseline'];
        $rows = [];

        if (isset($base['database']['database_size_mb'])) {
            $rows[] = ['Database size', $base['database']['database_size_mb'] . ' MB'];
            $rows[] = ['DB tables', (string) ($base['database']['total_tables'] ?? '-')];
            $rows[] = ['DB rows', (string) ($base['database']['total_rows'] ?? '-')];
        }
        if (isset($base['cache']['driver'])) {
            $rows[] = ['Cache driver', $base['cache']['driver']];
            $rows[] = ['Cache status', $base['cache']['status'] ?? '-'];
        }
        if (isset($base['system']['memory_usage_mb'])) {
            $rows[] = ['Memory (PHP)', $base['system']['memory_usage_mb'] . ' MB'];
        }
        $rows[] = ['Recent slow ops (log)', (string) $report['recent_slow_ops']];

        $this->table(['Metric', 'Value'], $rows);
        $this->newLine();
        $this->line('Full baseline: php artisan performance:baseline --export');
        $this->line('Recent errors: php artisan errors:dashboard');

        return self::SUCCESS;
    }

    private function tail(string $path, int $numLines): string
    {
        $fp = @fopen($path, 'rb');
        if (!$fp) {
            return '';
        }
        $size = filesize($path);
        $pos = max(0, $size - 256 * 1024);
        fseek($fp, $pos);
        $data = (string) fread($fp, 256 * 1024);
        fclose($fp);
        $all = explode("\n", $data);
        $slice = array_slice($all, -$numLines);
        return implode("\n", $slice);
    }
}
