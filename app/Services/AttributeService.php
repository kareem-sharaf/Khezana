<?php

namespace App\Services;

use App\Actions\Attribute\AssignAttributeToCategoryAction;
use App\Actions\Attribute\RemoveAttributeFromCategoryAction;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AttributeService extends BaseService
{
    public function __construct(
        private AssignAttributeToCategoryAction $assignAction,
        private RemoveAttributeFromCategoryAction $removeAction
    ) {
    }

    /**
     * Create a new attribute.
     */
    public function create(array $data): Attribute
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Attribute::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        return Attribute::create($data);
    }

    /**
     * Update an attribute.
     */
    public function update(Attribute $attribute, array $data): Attribute
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique (except for current attribute)
        if (isset($data['slug'])) {
            $baseSlug = $data['slug'];
            $counter = 1;
            while (Attribute::where('slug', $data['slug'])->where('id', '!=', $attribute->id)->exists()) {
                $data['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $attribute->update($data);

        return $attribute->fresh();
    }

    /**
     * Delete an attribute.
     */
    public function delete(Attribute $attribute): bool
    {
        return $attribute->delete();
    }

    /**
     * Assign attribute to category.
     */
    public function assignToCategory(Category $category, Attribute $attribute): void
    {
        $this->assignAction->execute($category, $attribute);
    }

    /**
     * Assign multiple attributes to category.
     */
    public function assignMultipleToCategory(Category $category, array $attributeIds): void
    {
        $this->assignAction->executeMultiple($category, $attributeIds);
    }

    /**
     * Remove attribute from category.
     */
    public function removeFromCategory(Category $category, Attribute $attribute): void
    {
        $this->removeAction->execute($category, $attribute);
    }

    /**
     * Get attributes for a category (including from parent categories).
     */
    public function getAttributesForCategory(Category $category): Collection
    {
        return $category->getAllAttributes();
    }

    /**
     * Validate attribute values for a category.
     */
    public function validateAttributeValues(Category $category, array $attributeValues): array
    {
        $errors = [];
        $requiredAttributes = $category->getAllAttributes()->where('is_required', true);

        // Check required attributes
        foreach ($requiredAttributes as $attribute) {
            if (!isset($attributeValues[$attribute->slug]) || empty($attributeValues[$attribute->slug])) {
                $errors[$attribute->slug] = "The {$attribute->name} field is required.";
            }
        }

        // Validate attribute values
        foreach ($attributeValues as $attributeSlug => $value) {
            $attribute = Attribute::where('slug', $attributeSlug)->first();

            if (!$attribute) {
                $errors[$attributeSlug] = "Unknown attribute: {$attributeSlug}";
                continue;
            }

            // Check if attribute belongs to category
            if (!$category->getAllAttributes()->contains('id', $attribute->id)) {
                $errors[$attributeSlug] = "Attribute {$attribute->name} is not valid for this category.";
                continue;
            }

            // Validate based on type
            if ($attribute->isSelectType()) {
                $validValues = $attribute->values()->pluck('value')->toArray();
                if (!in_array($value, $validValues)) {
                    $errors[$attributeSlug] = "Invalid value for {$attribute->name}. Must be one of: " . implode(', ', $validValues);
                }
            } elseif ($attribute->isNumberType()) {
                if (!is_numeric($value)) {
                    $errors[$attributeSlug] = "The {$attribute->name} must be a number.";
                }
            }
        }

        return $errors;
    }

    /**
     * Create or update attribute value.
     */
    public function createOrUpdateValue(Attribute $attribute, string $value): AttributeValue
    {
        return AttributeValue::firstOrCreate(
            [
                'attribute_id' => $attribute->id,
                'value' => $value,
            ]
        );
    }

    /**
     * Delete attribute value.
     */
    public function deleteValue(AttributeValue $attributeValue): bool
    {
        return $attributeValue->delete();
    }
}
