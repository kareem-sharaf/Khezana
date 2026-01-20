<?php

declare(strict_types=1);

namespace App\Read\Shared\Models;

use App\Models\ItemAttribute;

class AttributeReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        public readonly mixed $value,
        public readonly string $formattedValue,
    ) {
    }

    public static function fromModel(ItemAttribute $itemAttribute): self
    {
        $attribute = $itemAttribute->attribute;
        $value = $itemAttribute->value;
        
        $formattedValue = match($attribute->type->value) {
            'number' => is_numeric($value) ? number_format((float) $value, 0) : $value,
            'select' => $value,
            default => $value,
        };

        return new self(
            id: $attribute->id,
            name: $attribute->name,
            type: $attribute->type->value,
            value: $value,
            formattedValue: $formattedValue,
        );
    }
}
