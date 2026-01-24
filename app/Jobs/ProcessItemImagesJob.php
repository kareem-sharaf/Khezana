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
        $isFirst = true;

        foreach ($this->tempPaths as $tempPath) {
            try {
                $imageData = $imageService->processAndStoreFromPath(
                    $tempPath,
                    $this->itemId,
                    $this->disk,
                );

                ItemImage::create([
                    'item_id' => $this->itemId,
                    'path' => $imageData['path'],
                    'path_webp' => $imageData['path_webp'] ?? null,
                    'disk' => $imageData['disk'],
                    'is_primary' => $isFirst,
                ]);

                $isFirst = false;
            } catch (\Throwable $e) {
                Log::error('ProcessItemImagesJob: failed to process image', [
                    'item_id' => $this->itemId,
                    'temp_path' => $tempPath,
                    'error' => $e->getMessage(),
                ]);
                // Continue with other images; this one is skipped
            }
        }

        $cacheService->invalidateItem($this->itemId);
    }
}
