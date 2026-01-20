<?php

namespace App\Actions\Attribute;

use App\Models\Attribute;
use App\Models\Category;
use App\Services\Cache\CategoryCacheService;

class AssignAttributeToCategoryAction
{
    public function __construct(
        private readonly CategoryCacheService $categoryCacheService
    ) {
    }

    /**
     * Assign an attribute to a category.
     */
    public function execute(Category $category, Attribute $attribute): void
    {
        if ($category->attributes()->where('attributes.id', $attribute->id)->exists()) {
            return; // Already assigned
        }

        $category->attributes()->attach($attribute->id);
        
        $this->categoryCacheService->invalidateCategoryAttributes($category->id);
    }

    /**
     * Assign multiple attributes to a category.
     */
    public function executeMultiple(Category $category, array $attributeIds): void
    {
        $category->attributes()->syncWithoutDetaching($attributeIds);
        
        $this->categoryCacheService->invalidateCategoryAttributes($category->id);
    }
}
