<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Support\Str;

class CreateCategoryAction
{
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

        return Category::create($data);
    }
}
