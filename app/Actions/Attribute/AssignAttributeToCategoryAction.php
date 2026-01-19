<?php

namespace App\Actions\Attribute;

use App\Models\Attribute;
use App\Models\Category;

class AssignAttributeToCategoryAction
{
    /**
     * Assign an attribute to a category.
     */
    public function execute(Category $category, Attribute $attribute): void
    {
        if ($category->attributes()->where('attributes.id', $attribute->id)->exists()) {
            return; // Already assigned
        }

        $category->attributes()->attach($attribute->id);
    }

    /**
     * Assign multiple attributes to a category.
     */
    public function executeMultiple(Category $category, array $attributeIds): void
    {
        $category->attributes()->syncWithoutDetaching($attributeIds);
    }
}
