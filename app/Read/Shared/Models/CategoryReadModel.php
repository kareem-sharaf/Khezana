<?php

declare(strict_types=1);

namespace App\Read\Shared\Models;

use App\Models\Category;

class CategoryReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
    ) {
    }

    public static function fromModel(Category $category): self
    {
        return new self(
            id: $category->id,
            name: $category->name,
            slug: $category->slug,
            description: $category->description,
        );
    }
}
