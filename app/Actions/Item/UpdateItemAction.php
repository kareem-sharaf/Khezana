<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Item;
use App\Services\Cache\CacheService;
use App\Services\ItemService;
use Illuminate\Support\Facades\DB;

/**
 * Action to update an existing item
 */
class UpdateItemAction
{
    private const SENSITIVE_FIELDS = ['price', 'operation_type', 'category_id'];

    public function __construct(
        private readonly ItemService $itemService,
        private readonly SubmitForApprovalAction $submitForApprovalAction,
        private readonly CacheService $cacheService
    ) {}

    /**
     * Update an item
     *
     * @param Item $item The item to update
     * @param array $data Updated data
     * @param array|null $attributes Updated attributes
     * @param array|null $images New image paths (will replace existing)
     * @return Item
     * @throws \Exception If validation fails
     */
    public function execute(Item $item, array $data, ?array $attributes = null, ?array $images = null): Item
    {
        // Validate operation rules
        $this->itemService->validateOperationRules($data);

        return DB::transaction(function () use ($item, $data, $attributes, $images) {
            $item->refresh();
            $item->ensureCanBeModified();

            $wasApproved = $item->isApproved();
            $hasSensitiveChanges = false;
            $hasAttributeChanges = $attributes !== null;

            // Check for sensitive field changes
            foreach (self::SENSITIVE_FIELDS as $field) {
                if (!isset($data[$field])) {
                    continue;
                }

                $newValue = $field === 'operation_type'
                    ? OperationType::from($data[$field])
                    : $data[$field];

                if ($newValue != $item->$field) {
                    $hasSensitiveChanges = true;
                    break;
                }
            }

            $isAvailable = $data['is_available'] ?? $item->is_available;
            $availabilityStatus = $isAvailable ? ItemAvailability::AVAILABLE : ItemAvailability::UNAVAILABLE;

            // Update the item
            $item->update([
                'category_id' => $data['category_id'] ?? $item->category_id,
                'operation_type' => isset($data['operation_type'])
                    ? OperationType::from($data['operation_type'])
                    : $item->operation_type,
                'title' => $data['title'] ?? $item->title,
                'description' => $data['description'] ?? $item->description,
                'governorate' => $data['governorate'] ?? $item->governorate,
                'condition' => $data['condition'] ?? $item->condition,
                'price' => $data['price'] ?? $item->price,
                'deposit_amount' => $data['deposit_amount'] ?? $item->deposit_amount,
                'is_available' => $isAvailable,
                'availability_status' => $availabilityStatus,
            ]);

            // Update dynamic attributes if provided
            if ($hasAttributeChanges) {
                /** @var \App\Models\Item $item */
                $this->itemService->validateCategoryAttributes($item, $attributes);
                $item->itemAttributes()->delete();
                $item->setAttributeValues($attributes);
            }

            // Update images if provided
            if ($images !== null) {
                $item->images()->delete();
                $this->attachImages($item, $images);
            }

            // Re-submit for approval if sensitive fields changed and item was approved
            if ($wasApproved && ($hasSensitiveChanges || $hasAttributeChanges)) {
                $approval = $item->approval();
                if ($approval && $approval->status !== ApprovalStatus::PENDING) {
                    $this->submitForApprovalAction->execute($item, $item->user);
                }
            }

            // Invalidate cache after update
            $this->cacheService->invalidateItem($item->id);

            return $item->fresh(['user', 'category', 'images']);
        });
    }

    /**
     * Attach images to item
     */
    private function attachImages(Item $item, array $images): void
    {
        $isFirst = true;
        foreach ($images as $imagePath) {
            $item->images()->create([
                'path' => $imagePath,
                'is_primary' => $isFirst,
            ]);
            $isFirst = false;
        }
    }
}
