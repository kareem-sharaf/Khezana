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
            $errors = $validator->errors()->all();
            \Illuminate\Support\Facades\Log::warning('ItemService: Operation rules validation failed', [
                'operation_type' => $data['operation_type'] ?? 'unknown',
                'errors' => $errors,
                'data' => $data
            ]);
            throw new \Illuminate\Validation\ValidationException($validator);
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

        // Skip 'condition' attribute validation when item condition is 'new'
        $isNewCondition = $item->condition === 'new';

        foreach ($allAttributes as $attribute) {
            // Skip 'condition' attribute for new items
            if ($isNewCondition && $attribute->slug === 'condition') {
                continue;
            }

            if ($attribute->is_required) {
                $slug = $attribute->slug;
                if (!isset($attributes[$slug]) || empty($attributes[$slug])) {
                    \Illuminate\Support\Facades\Log::warning('ItemService: Required attribute missing', [
                        'attribute' => $attribute->name,
                        'slug' => $slug,
                        'item_id' => $item->id
                    ]);
                    throw new \Exception(__('items.messages.attributes_required') . ': ' . $attribute->name);
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
                \Illuminate\Support\Facades\Log::warning('ItemService: Invalid attribute value', [
                    'attribute' => $attribute->name,
                    'value' => $value,
                    'allowed_values' => $allowedValues
                ]);
                throw new \Exception(__('items.messages.invalid_attribute_value', ['attribute' => $attribute->name]));
            }
        } elseif ($attribute->isNumberType()) {
            if (!is_numeric($value)) {
                \Illuminate\Support\Facades\Log::warning('ItemService: Attribute must be numeric', [
                    'attribute' => $attribute->name,
                    'value' => $value
                ]);
                throw new \Exception(__('items.messages.attribute_must_be_numeric', ['attribute' => $attribute->name]));
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
