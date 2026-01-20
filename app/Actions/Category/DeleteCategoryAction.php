<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Services\Cache\CategoryCacheService;

class DeleteCategoryAction
{
    public function __construct(
        private readonly CategoryCacheService $categoryCacheService
    ) {
    }

    /**
     * Delete a category.
     */
    public function execute(Category $category): bool
    {
        // Check if category has children
        if ($category->hasChildren()) {
            throw new \InvalidArgumentException('Cannot delete category with children. Please delete or move children first.');
        }

        $categoryId = $category->id;
        $deleted = $category->delete();
        
        if ($deleted) {
            $this->categoryCacheService->invalidateAll();
            $this->categoryCacheService->invalidateCategoryAttributes($categoryId);
        }

        return $deleted;
    }
}
