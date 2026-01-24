<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Command to check Redis setup and configuration
 * 
 * This command verifies that Redis is properly configured
 * and can be used for Cache and Queue.
 * 
 * Usage: php artisan redis:check
 */
class CheckRedisSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Redis setup and configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Checking Redis Setup...');
        $this->newLine();

        $allGood = true;

        // Check Redis connection
        $this->info('1️⃣ Checking Redis Connection...');
        try {
            Redis::connection()->ping();
            $this->info('   ✅ Redis connection successful');
        } catch (\Exception $e) {
            $this->error("   ❌ Redis connection failed: {$e->getMessage()}");
            $this->warn('   💡 Make sure Redis is running: redis-server');
            $allGood = false;
        }

        // Check Cache configuration
        $this->newLine();
        $this->info('2️⃣ Checking Cache Configuration...');
        $cacheDriver = config('cache.default');
        $this->line("   Cache Driver: {$cacheDriver}");
        
        if ($cacheDriver === 'redis') {
            $this->info('   ✅ Cache is configured to use Redis');
            
            // Test cache operations
            try {
                $testKey = 'redis_test_' . time();
                Cache::put($testKey, 'test_value', 60);
                $value = Cache::get($testKey);
                Cache::forget($testKey);
                
                if ($value === 'test_value') {
                    $this->info('   ✅ Cache operations working');
                } else {
                    $this->error('   ❌ Cache operations failed');
                    $allGood = false;
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Cache test failed: {$e->getMessage()}");
                $allGood = false;
            }
        } else {
            $this->warn("   ⚠️  Cache is using '{$cacheDriver}' instead of Redis");
            $this->line('   💡 Set CACHE_STORE=redis in .env');
        }

        // Check Queue configuration
        $this->newLine();
        $this->info('3️⃣ Checking Queue Configuration...');
        $queueDriver = config('queue.default');
        $this->line("   Queue Driver: {$queueDriver}");
        
        if ($queueDriver === 'redis') {
            $this->info('   ✅ Queue is configured to use Redis');
        } else {
            $this->warn("   ⚠️  Queue is using '{$queueDriver}' instead of Redis");
            $this->line('   💡 Set QUEUE_CONNECTION=redis in .env');
        }

        // Check Redis info
        $this->newLine();
        $this->info('4️⃣ Redis Server Information...');
        try {
            $info = Redis::connection()->info();
            $this->line("   Redis Version: {$info['redis_version']}");
            $this->line("   Used Memory: " . $this->formatBytes($info['used_memory']));
            $this->line("   Connected Clients: {$info['connected_clients']}");
            $this->line("   Total Commands: {$info['total_commands_processed']}");
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Could not get Redis info: {$e->getMessage()}");
        }

        // Summary
        $this->newLine();
        if ($allGood && $cacheDriver === 'redis' && $queueDriver === 'redis') {
            $this->info('✅ Redis setup is complete and working!');
            $this->line('   - Cache: ✅ Using Redis');
            $this->line('   - Queue: ✅ Using Redis');
        } else {
            $this->warn('⚠️  Redis setup needs attention:');
            if ($cacheDriver !== 'redis') {
                $this->line('   - Cache: Not using Redis (currently: ' . $cacheDriver . ')');
            }
            if ($queueDriver !== 'redis') {
                $this->line('   - Queue: Not using Redis (currently: ' . $queueDriver . ')');
            }
            if (!$allGood) {
                $this->line('   - Connection: Issues detected');
            }
        }

        return $allGood ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
