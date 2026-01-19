<?php

namespace App\Traits;

use App\Models\Attribute;
use App\Models\ItemAttribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttributes
{
    /**
     * Get all attributes for this model.
     */
    public function itemAttributes(): MorphMany
    {
        return $this->morphMany(ItemAttribute::class, 'attributable');
    }

    /**
     * Get attribute value by attribute slug.
     */
    public function getAttributeValue(string $attributeSlug): ?string
    {
        $attribute = Attribute::where('slug', $attributeSlug)->first();

        if (!$attribute) {
            return null;
        }

        $itemAttribute = $this->itemAttributes()
            ->where('attribute_id', $attribute->id)
            ->first();

        return $itemAttribute?->value;
    }

    /**
     * Set attribute value.
     */
    public function setAttributeValue(string $attributeSlug, string $value): void
    {
        $attribute = Attribute::where('slug', $attributeSlug)->first();

        if (!$attribute) {
            return;
        }

        $this->itemAttributes()->updateOrCreate(
            ['attribute_id' => $attribute->id],
            ['value' => $value]
        );
    }

    /**
     * Set multiple attribute values.
     */
    public function setAttributeValues(array $attributes): void
    {
        foreach ($attributes as $attributeSlug => $value) {
            $this->setAttributeValue($attributeSlug, $value);
        }
    }

    /**
     * Get all attributes as key-value pairs.
     */
    public function getAttributesArray(): array
    {
        return $this->itemAttributes()
            ->with('attribute')
            ->get()
            ->mapWithKeys(function ($itemAttribute) {
                return [$itemAttribute->attribute->slug => $itemAttribute->value];
            })
            ->toArray();
    }

    /**
     * Remove attribute value.
     */
    public function removeAttributeValue(string $attributeSlug): void
    {
        $attribute = Attribute::where('slug', $attributeSlug)->first();

        if (!$attribute) {
            return;
        }

        $this->itemAttributes()
            ->where('attribute_id', $attribute->id)
            ->delete();
    }
}
