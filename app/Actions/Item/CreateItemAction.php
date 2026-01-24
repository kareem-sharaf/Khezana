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
        private readonly CacheService $cacheService
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
        // Validate operation rules
        $this->itemService->validateOperationRules($data);

        $tempPaths = [];
        $item = DB::transaction(function () use ($data, $user, $attributes, $images, &$tempPaths) {
            // Items are always available when created
            $isAvailable = true;

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
                'availability_status' => ItemAvailability::AVAILABLE,
            ]);

            if ($attributes) {
                $this->itemService->validateCategoryAttributes($item, $attributes);
                $item->setAttributeValues($attributes);
            }

            if ($images && is_array($images)) {
                $tempPaths = $this->storeImagesToTemp($images);
            }

            $this->submitForApprovalAction->execute($item, $user);

            return $item;
        });

        // Invalidate cache after transaction to avoid blocking response
        $this->cacheService->invalidateItem($item->id);

        if (!empty($tempPaths)) {
            ProcessItemImagesJob::dispatch($item->id, $tempPaths, 'public');
        }

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

        // Performance fix #13: Reduced logging - only log errors
        foreach ($images as $index => $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }
            try {
                $this->imageService->validateFile($file);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('CreateItemAction: Invalid image skipped', [
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
            } else {
                \Illuminate\Support\Facades\Log::error('CreateItemAction: Failed to store image to temp', [
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return $tempPaths;
    }
}
