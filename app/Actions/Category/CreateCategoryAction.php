<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    public function __construct(
        private readonly CategoryCacheService $categoryCacheService
    ) {
    }

    /**
     * Create a new category.
     */
    public function execute(array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        $category = Category::create($data);
        
        $this->categoryCacheService->invalidateAll();
        
        return $category;
    }
}
