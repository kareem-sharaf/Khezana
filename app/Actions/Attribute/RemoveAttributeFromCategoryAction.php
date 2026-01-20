<?php

namespace App\Actions\Attribute;

use App\Models\Attribute;
use App\Models\Category;
use App\Services\Cache\CategoryCacheService;

class RemoveAttributeFromCategoryAction
{
    public function __construct(
        private readonly CategoryCacheService $categoryCacheService
    ) {
    }

    /**
     * Remove an attribute from a category.
     */
    public function execute(Category $category, Attribute $attribute): void
    {
        $category->attributes()->detach($attribute->id);
        
        $this->categoryCacheService->invalidateCategoryAttributes($category->id);
    }

    /**
     * Remove multiple attributes from a category.
     */
    public function executeMultiple(Category $category, array $attributeIds): void
    {
        $category->attributes()->detach($attributeIds);
        
        $this->categoryCacheService->invalidateCategoryAttributes($category->id);
    }
}
