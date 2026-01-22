<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Enums\ItemAvailability;
use App\Enums\OperationType;
use App\Models\Item;
use App\Models\User;
use App\Services\ItemService;
use Illuminate\Support\Facades\DB;

/**
 * Action to create a new item
 */
class CreateItemAction
{
    public function __construct(
        private readonly ItemService $itemService,
        private readonly SubmitForApprovalAction $submitForApprovalAction
    ) {
    }

    /**
     * Create a new item
     *
     * @param array $data Item data
     * @param User $user The user creating the item
     * @param array|null $attributes Dynamic attributes
     * @param array|null $images Image paths
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

            // Attach images if provided
            if ($images) {
                $this->attachImages($item, $images);
            }

            // Create approval automatically
            $this->submitForApprovalAction->execute($item, $user);

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
