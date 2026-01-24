<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ItemImage;
use App\Services\Cache\CacheService;
use App\Services\ImageOptimizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Phase 3.1: Process uploaded images asynchronously
 *
 * Stores temp paths, processes via ImageOptimizationService, creates ItemImage records.
 */
class ProcessItemImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        private readonly int $itemId,
        private readonly array $tempPaths,
        private readonly string $disk = 'public',
    ) {
        $this->onQueue('default');
    }

    public function handle(ImageOptimizationService $imageService, CacheService $cacheService): void
    {
        Log::info('ProcessItemImagesJob: Starting image processing', [
            'item_id' => $this->itemId,
            'temp_paths_count' => count($this->tempPaths),
            'temp_paths' => $this->tempPaths,
        ]);

        $isFirst = true;
        $processedCount = 0;
        $failedCount = 0;

        foreach ($this->tempPaths as $tempPath) {
            try {
                Log::debug('ProcessItemImagesJob: Processing image', [
                    'item_id' => $this->itemId,
                    'temp_path' => $tempPath,
                ]);

                $imageData = $imageService->processAndStoreFromPath(
                    $tempPath,
                    $this->itemId,
                    $this->disk,
                );

                $itemImage = ItemImage::create([
                    'item_id' => $this->itemId,
                    'path' => $imageData['path'],
                    'path_webp' => $imageData['path_webp'] ?? null,
                    'disk' => $imageData['disk'],
                    'is_primary' => $isFirst,
                ]);

                Log::info('ProcessItemImagesJob: Image processed successfully', [
                    'item_id' => $this->itemId,
                    'image_id' => $itemImage->id,
                    'path' => $imageData['path'],
                    'is_primary' => $isFirst,
                ]);

                $isFirst = false;
                $processedCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                Log::error('ProcessItemImagesJob: failed to process image', [
                    'item_id' => $this->itemId,
                    'temp_path' => $tempPath,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Continue with other images; this one is skipped
            }
        }

        Log::info('ProcessItemImagesJob: Image processing completed', [
            'item_id' => $this->itemId,
            'processed_count' => $processedCount,
            'failed_count' => $failedCount,
            'total_count' => count($this->tempPaths),
        ]);

        $cacheService->invalidateItem($this->itemId);
    }
}
