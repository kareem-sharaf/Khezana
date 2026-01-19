<?php

namespace App\Actions\Category;

use App\Models\Category;

class DeleteCategoryAction
{
    /**
     * Delete a category.
     */
    public function execute(Category $category): bool
    {
        // Check if category has children
        if ($category->hasChildren()) {
            throw new \InvalidArgumentException('Cannot delete category with children. Please delete or move children first.');
        }

        return $category->delete();
    }
}
