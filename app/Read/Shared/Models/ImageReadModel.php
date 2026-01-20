<?php

declare(strict_types=1);

namespace App\Read\Shared\Models;

use App\Models\ItemImage;

class ImageReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $path,
        public readonly bool $isPrimary,
        public readonly ?string $alt,
    ) {
    }

    public static function fromModel(ItemImage $image): self
    {
        return new self(
            id: $image->id,
            path: $image->path,
            isPrimary: $image->is_primary ?? false,
            alt: $image->alt ?? null,
        );
    }
}
