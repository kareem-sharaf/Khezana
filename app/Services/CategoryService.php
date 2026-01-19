<?php

namespace App\Services;

use App\Actions\Category\CreateCategoryAction;
use App\Actions\Category\DeleteCategoryAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService extends BaseService
{
    public function __construct(
        private CreateCategoryAction $createAction,
        private UpdateCategoryAction $updateAction,
        private DeleteCategoryAction $deleteAction
    ) {
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        return $this->createAction->execute($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data): Category
    {
        return $this->updateAction->execute($category, $data);
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): bool
    {
        return $this->deleteAction->execute($category);
    }

    /**
     * Get all root categories.
     */
    public function getRootCategories(): Collection
    {
        return Category::roots()->active()->with('children')->orderBy('name')->get();
    }

    /**
     * Get category tree (hierarchical structure).
     */
    public function getCategoryTree(?int $parentId = null): Collection
    {
        $query = Category::query();

        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        return $query->active()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get category with all ancestors.
     */
    public function getCategoryWithAncestors(Category $category): Category
    {
        return $category->load('parent');
    }

    /**
     * Get all categories as flat list with indentation for hierarchy.
     */
    public function getCategoriesFlat(?int $parentId = null, int $level = 0): Collection
    {
        $categories = Category::where('parent_id', $parentId)
            ->active()
            ->orderBy('name')
            ->get();

        $result = collect();

        foreach ($categories as $category) {
            $category->level = $level;
            $result->push($category);
            $result = $result->merge($this->getCategoriesFlat($category->id, $level + 1));
        }

        return $result;
    }
}
