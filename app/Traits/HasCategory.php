<?php

namespace App\Traits;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCategory
{
    /**
     * Get the category that owns this model.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if model belongs to a specific category.
     */
    public function belongsToCategory(Category|int $category): bool
    {
        $categoryId = $category instanceof Category ? $category->id : $category;
        return $this->category_id === $categoryId;
    }

    /**
     * Check if model belongs to category or any of its descendants.
     */
    public function belongsToCategoryOrDescendants(Category|int $category): bool
    {
        $categoryId = $category instanceof Category ? $category->id : $category;
        
        if ($this->category_id === $categoryId) {
            return true;
        }

        $category = $category instanceof Category ? $category : Category::find($categoryId);
        
        if (!$category) {
            return false;
        }

        $descendantIds = $category->descendants->pluck('id')->toArray();
        
        return in_array($this->category_id, $descendantIds);
    }
}
