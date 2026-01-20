<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    public function __construct(
        private readonly CategoryCacheService $categoryCacheService
    ) {
    }

    /**
     * Update a category.
     */
    public function execute(Category $category, array $data): Category
    {
        // Prevent setting category as its own parent
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            throw new \InvalidArgumentException('Category cannot be its own parent.');
        }

        // Prevent circular references
        if (isset($data['parent_id']) && $data['parent_id']) {
            $this->validateNoCircularReference($category, $data['parent_id']);
        }

        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique (except for current category)
        if (isset($data['slug'])) {
            $baseSlug = $data['slug'];
            $counter = 1;
            while (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                $data['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $category->update($data);
        
        $this->categoryCacheService->invalidateAll();
        $this->categoryCacheService->invalidateCategoryAttributes($category->id);

        return $category->fresh();
    }

    /**
     * Validate that setting parent_id won't create a circular reference.
     */
    private function validateNoCircularReference(Category $category, int $parentId): void
    {
        $parent = Category::find($parentId);

        if (!$parent) {
            return;
        }

        // Check if the new parent is a descendant of the current category
        $descendants = $category->descendants;
        if ($descendants->contains('id', $parentId)) {
            throw new \InvalidArgumentException('Cannot set parent: would create circular reference.');
        }
    }
}
