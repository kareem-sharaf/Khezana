<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OperationType;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;

/**
 * Item Service
 * 
 * Handles business logic for items:
 * - Validates operation rules
 * - Validates category attributes
 * - Handles availability
 */
class ItemService
{
    /**
     * Validate operation rules based on operation type
     *
     * @param array $data Item data
     * @throws \Exception If validation fails
     */
    public function validateOperationRules(array $data): void
    {
        $operationType = OperationType::from($data['operation_type']);

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ];

        switch ($operationType) {
            case OperationType::SELL:
                $rules['price'] = 'required|numeric|min:0';
                $rules['deposit_amount'] = 'nullable|numeric|min:0';
                break;

            case OperationType::RENT:
                $rules['price'] = 'required|numeric|min:0';
                $rules['deposit_amount'] = 'required|numeric|min:0';
                break;

            case OperationType::DONATE:
                $rules['price'] = 'nullable|numeric|min:0';
                $rules['deposit_amount'] = 'nullable|numeric|min:0';
                break;
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . $validator->errors()->first());
        }
    }

    /**
     * Validate category attributes
     *
     * @param \App\Models\Item $item The item
     * @param array $attributes Attribute values
     * @throws \Exception If validation fails
     */
    public function validateCategoryAttributes(\App\Models\Item $item, array $attributes): void
    {
        $category = Category::with('attributes')->findOrFail($item->category_id);
        $allAttributes = $category->getAllAttributes();

        foreach ($allAttributes as $attribute) {
            if ($attribute->is_required) {
                $slug = $attribute->slug;
                if (!isset($attributes[$slug]) || empty($attributes[$slug])) {
                    throw new \Exception("Required attribute '{$attribute->name}' is missing.");
                }
            }

            // Validate attribute value based on type
            if (isset($attributes[$attribute->slug])) {
                $this->validateAttributeValue($attribute, $attributes[$attribute->slug]);
            }
        }
    }

    /**
     * Validate a single attribute value
     *
     * @param Attribute $attribute The attribute
     * @param mixed $value The value to validate
     * @throws \Exception If validation fails
     */
    private function validateAttributeValue(Attribute $attribute, mixed $value): void
    {
        $attributeType = $attribute->type;
        
        if ($attribute->isSelectType()) {
            // Get allowed values for this attribute
            $allowedValues = $attribute->values()->pluck('value')->toArray();
            if (!in_array($value, $allowedValues)) {
                throw new \Exception("Invalid value for attribute '{$attribute->name}'.");
            }
        } elseif ($attribute->isNumberType()) {
            if (!is_numeric($value)) {
                throw new \Exception("Attribute '{$attribute->name}' must be a number.");
            }
        }
        // Text type is always valid
    }

    /**
     * Toggle item availability
     *
     * @param Item $item The item
     * @param bool $isAvailable Availability status
     * @return Item
     */
    public function toggleAvailability(Item $item, bool $isAvailable): Item
    {
        $item->update(['is_available' => $isAvailable]);
        return $item->fresh();
    }
}
