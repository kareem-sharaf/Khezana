<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Item;
use App\Models\User;
use App\Services\Cache\CacheService;
use App\Services\ImageOptimizationService;
use App\Services\ItemService;
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

        return DB::transaction(function () use ($data, $user, $attributes, $images) {
            $isAvailable = $data['is_available'] ?? true;
            
            // Create the item
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

            // Set dynamic attributes if provided
            if ($attributes) {
                $this->itemService->validateCategoryAttributes($item, $attributes);
                $item->setAttributeValues($attributes);
            }

            // Process and attach images if provided
            if ($images && is_array($images)) {
                $this->attachImages($item, $images);
            }

            // Create approval automatically
            $this->submitForApprovalAction->execute($item, $user);

            // Invalidate cache to show new item immediately
            $this->cacheService->invalidateItem($item->id);

            return $item->fresh(['user', 'category', 'images']);
        });
    }

    /**
     * Attach images to item
     * 
     * @param Item $item The item to attach images to
     * @param array $images Array of UploadedFile instances
     */
    private function attachImages(Item $item, array $images): void
    {
        $isFirst = true;
        $disk = 'public'; // Default disk, can be configured via env
        
        foreach ($images as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }
            
            try {
                // Process and store image using ImageOptimizationService
                $imageData = $this->imageService->processAndStore($file, $item->id, $disk);
                
                // Save image record in database
                $item->images()->create([
                    'path' => $imageData['path'],
                    'disk' => $imageData['disk'],
                    'is_primary' => $isFirst,
                ]);
                
                $isFirst = false;
            } catch (\Exception $e) {
                // Log error but continue with other images
                \Illuminate\Support\Facades\Log::error('Failed to process image', [
                    'item_id' => $item->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
