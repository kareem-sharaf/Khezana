<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Phase 5.2: Error dashboard – recent errors from logs
 *
 * Usage: php artisan errors:dashboard
 */
class ErrorsDashboardCommand extends Command
{
    protected $signature = 'errors:dashboard
                            {--lines=50 : Number of recent log lines to scan}
                            {--show=20 : Number of error entries to display}
                            {--export= : Export to JSON file}';

    protected $description = 'Show recent errors and warnings from application logs';

    private const PATTERNS = [
        'error' => ['\\.ERROR\\b', 'Slow query detected'],
        'warning' => ['\\.WARNING\\b'],
        'critical' => ['\\.CRITICAL\\b'],
        'exception' => ['exception', 'Exception', 'ErrorException'],
    ];

    public function handle(): int
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = (int) $this->option('lines');
        $show = (int) $this->option('show');
        $exportPath = $this->option('export');

        $this->info('📋 Error Dashboard');
        $this->newLine();

        if (!File::exists($logPath)) {
            $this->warn('Log file not found: ' . $logPath);
            return self::FAILURE;
        }

        $content = $this->tail($logPath, $lines);
        $all = explode("\n", $content);
        $entries = [];

        foreach ($all as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            foreach (self::PATTERNS as $level => $pats) {
                foreach ($pats as $pat) {
                    if (preg_match('/' . $pat . '/i', $line)) {
                        $entries[] = [
                            'level' => $level,
                            'line' => strlen($line) > 500 ? substr($line, 0, 500) . '...' : $line,
                            'ts' => preg_match('/^\[([^\]]+)\]/', $line, $m) ? $m[1] : null,
                        ];
                        break 2;
                    }
                }
            }
        }

        $total = count($entries);
        $display = array_slice($entries, -$show);

        $this->table(
            ['Level', 'Time', 'Preview'],
            array_map(static fn (array $e) => [
                $e['level'],
                $e['ts'] ?? '-',
                substr($e['line'], 0, 80) . (strlen($e['line']) > 80 ? '…' : ''),
            ], $display)
        );

        $this->newLine();
        $this->line("Total matching entries (last {$lines} log lines): <comment>{$total}</comment>");

        if ($exportPath && $exportPath !== '') {
            $json = json_encode(['total' => $total, 'entries' => $entries], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            File::put($exportPath, $json);
            $this->info("Exported to: {$exportPath}");
        }

        return self::SUCCESS;
    }

    private function tail(string $path, int $numLines): string
    {
        $fp = fopen($path, 'rb');
        if (!$fp) {
            return '';
        }
        $chunk = 8192;
        $data = '';
        $size = filesize($path);
        $pos = max(0, $size - 1024 * 512);
        fseek($fp, $pos);
        $data = fread($fp, 1024 * 512);
        fclose($fp);
        $all = explode("\n", (string) $data);
        $slice = array_slice($all, -$numLines);
        return implode("\n", $slice);
    }
}
