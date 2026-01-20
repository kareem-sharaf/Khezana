<?php

declare(strict_types=1);

namespace App\Read\Shared\Models;

use App\Models\User;
use Carbon\Carbon;

class UserReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Carbon $createdAt,
        public readonly string $memberSinceFormatted,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            createdAt: $user->created_at,
            memberSinceFormatted: __('Member since :date', ['date' => $user->created_at->format('M Y')]),
        );
    }
}
