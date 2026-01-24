<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Item;
use App\Models\User;
use App\Jobs\ProcessItemImagesJob;
use App\Services\Cache\CacheService;
use App\Services\ImageOptimizationService;
use App\Services\ItemService;
use App\Services\Performance\PerformanceMonitoringService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Action to create a new item
 */
class CreateItemAction
{
    public function __construct(
        private readonly ItemService $itemService,
        private readonly SubmitForApprovalAction $submitForApprovalAction,
        private readonly ImageOptimizationService $imageService,
        private readonly CacheService $cacheService,
        private readonly PerformanceMonitoringService $performanceMonitoring
    ) {
    }

    /**
     * Create a new item
     *
     * @param array $data Item data
     * @param User $user The user creating the item
     * @param array|null $attributes Dynamic attributes
     * @param array|null $images UploadedFile instances
     * @return Item
     * @throws \Exception If validation fails
     */
    public function execute(array $data, User $user, ?array $attributes = null, ?array $images = null): Item
    {
        // Phase 0.1: Start performance monitoring
        $startTime = microtime(true);
        $imageCount = $images ? count($images) : 0;

        // Validate operation rules
        $this->itemService->validateOperationRules($data);

        $tempPaths = [];
        $item = DB::transaction(function () use ($data, $user, $attributes, $images, &$tempPaths) {
            $isAvailable = $data['is_available'] ?? true;

            $item = Item::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'operation_type' => OperationType::from($data['operation_type']),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'governorate' => $data['governorate'] ?? null,
                'condition' => $data['condition'] ?? null,
                'price' => $data['price'] ?? null,
                'deposit_amount' => $data['deposit_amount'] ?? null,
                'is_available' => $isAvailable,
                'availability_status' => $isAvailable ? ItemAvailability::AVAILABLE : ItemAvailability::UNAVAILABLE,
            ]);

            if ($attributes) {
                $this->itemService->validateCategoryAttributes($item, $attributes);
                $item->setAttributeValues($attributes);
            }

            if ($images && is_array($images)) {
                $tempPaths = $this->storeImagesToTemp($images);
            }

            $this->submitForApprovalAction->execute($item, $user);
            $this->cacheService->invalidateItem($item->id);

            return $item->fresh(['user', 'category', 'images']);
        });

        if (!empty($tempPaths)) {
            ProcessItemImagesJob::dispatch($item->id, $tempPaths, 'public');
        }

        // Phase 0.1: Record performance metrics
        $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        $this->performanceMonitoring->recordMetric('item_creation', $duration, [
            'user_id' => $user->id,
            'category_id' => $data['category_id'],
            'image_count' => $imageCount,
            'has_attributes' => !empty($attributes),
        ]);

        return $item;
    }

    /**
     * Phase 3.1: Store uploaded images to temp and return paths for queue processing
     *
     * @param array<UploadedFile> $images
     * @return array<int, string> Temp paths relative to public disk
     */
    private function storeImagesToTemp(array $images): array
    {
        $tempPaths = [];
        $disk = 'public';

        foreach ($images as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }
            try {
                $this->imageService->validateFile($file);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Skipping invalid image in queue', [
                    'name' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
            $name = \Illuminate\Support\Str::uuid() . '.' . $ext;
            $path = $file->storeAs('temp', $name, $disk);
            if ($path) {
                $tempPaths[] = $path;
            }
        }

        return $tempPaths;
    }
}
