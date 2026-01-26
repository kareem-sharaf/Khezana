<?php

declare(strict_types=1);

namespace App\Read\Shared\Models;

use App\Models\Branch;

class BranchReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $city,
        public readonly ?string $address,
        public readonly ?string $phone,
        public readonly ?string $fullAddress,
    ) {
    }

    public static function fromModel(Branch $branch): self
    {
        return new self(
            id: $branch->id,
            name: $branch->name,
            code: $branch->code,
            city: $branch->city,
            address: $branch->address,
            phone: $branch->phone,
            fullAddress: $branch->full_address,
        );
    }
}
