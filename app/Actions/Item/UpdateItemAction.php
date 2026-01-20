<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Enums\OperationType;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Support\Facades\DB;

/**
 * Action to update an existing item
 */
class UpdateItemAction
{
    public function __construct(
        private readonly ItemService $itemService
    ) {
    }

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
            // Update the item
            $item->update([
                'category_id' => $data['category_id'] ?? $item->category_id,
                'operation_type' => isset($data['operation_type']) 
                    ? OperationType::from($data['operation_type']) 
                    : $item->operation_type,
                'title' => $data['title'] ?? $item->title,
                'description' => $data['description'] ?? $item->description,
                'price' => $data['price'] ?? $item->price,
                'deposit_amount' => $data['deposit_amount'] ?? $item->deposit_amount,
                'is_available' => $data['is_available'] ?? $item->is_available,
            ]);

            // Update dynamic attributes if provided
            if ($attributes !== null) {
                $this->itemService->validateCategoryAttributes($item, $attributes);
                // Remove old attributes and set new ones
                $item->itemAttributes()->delete();
                $item->setAttributeValues($attributes);
            }

            // Update images if provided
            if ($images !== null) {
                $item->images()->delete();
                $this->attachImages($item, $images);
            }

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
