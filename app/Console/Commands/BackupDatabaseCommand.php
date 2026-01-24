<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Command to backup database
 * 
 * This command creates a backup of the database.
 * Can be scheduled to run automatically.
 * 
 * Usage: php artisan db:backup
 */
class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup 
                            {--path= : Custom backup path}
                            {--compress : Compress backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('💾 Starting database backup...');
        
        $connection = DB::connection();
        $database = config("database.connections.{$connection->getName()}.database");
        $username = config("database.connections.{$connection->getName()}.username");
        $password = config("database.connections.{$connection->getName()}.password");
        $host = config("database.connections.{$connection->getName()}.host");
        $port = config("database.connections.{$connection->getName()}.port", 3306);
        
        // Generate backup filename
        $timestamp = now()->format('Y-m-d_His');
        $filename = "backup_{$database}_{$timestamp}.sql";
        
        // Determine backup path
        $backupPath = $this->option('path') 
            ? $this->option('path') 
            : storage_path("app/backups/{$filename}");
        
        // Create backups directory if it doesn't exist
        $backupDir = dirname($backupPath);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );
        
        $this->line("   Executing backup...");
        
        // Execute backup
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->error('❌ Backup failed!');
            $this->error('   Make sure mysqldump is installed and accessible.');
            $this->error('   Command: ' . $command);
            return Command::FAILURE;
        }
        
        $fileSize = filesize($backupPath);
        
        // Compress if requested
        if ($this->option('compress')) {
            $this->line("   Compressing backup...");
            $compressedPath = $backupPath . '.gz';
            $gz = gzopen($compressedPath, 'w9');
            $fp = fopen($backupPath, 'r');
            
            while (!feof($fp)) {
                gzwrite($gz, fread($fp, 8192));
            }
            
            fclose($fp);
            gzclose($gz);
            unlink($backupPath);
            
            $backupPath = $compressedPath;
            $fileSize = filesize($backupPath);
        }
        
        $this->info("✅ Backup completed successfully!");
        $this->line("   File: {$backupPath}");
        $this->line("   Size: " . $this->formatBytes($fileSize));
        
        // Cleanup old backups (keep last 30 days)
        $this->cleanupOldBackups($backupDir);
        
        return Command::SUCCESS;
    }

    /**
     * Cleanup old backup files (keep last 30 days)
     */
    private function cleanupOldBackups(string $backupDir): void
    {
        $files = glob($backupDir . '/backup_*.sql*');
        $cutoffDate = now()->subDays(30);
        
        $deleted = 0;
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                $deleted++;
            }
        }
        
        if ($deleted > 0) {
            $this->line("   Cleaned up {$deleted} old backup(s)");
        }
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
