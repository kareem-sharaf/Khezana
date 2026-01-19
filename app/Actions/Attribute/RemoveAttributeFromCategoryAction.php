<?php

namespace App\Actions\Attribute;

use App\Models\Attribute;
use App\Models\Category;

class RemoveAttributeFromCategoryAction
{
    /**
     * Remove an attribute from a category.
     */
    public function execute(Category $category, Attribute $attribute): void
    {
        $category->attributes()->detach($attribute->id);
    }

    /**
     * Remove multiple attributes from a category.
     */
    public function executeMultiple(Category $category, array $attributeIds): void
    {
        $category->attributes()->detach($attributeIds);
    }
}
