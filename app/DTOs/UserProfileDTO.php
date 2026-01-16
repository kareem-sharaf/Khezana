<?php

namespace App\DTOs;

class UserProfileDTO
{
    public function __construct(
        public int $userId,
        public ?string $city = null,
        public ?string $address = null,
        public ?string $avatar = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            city: $data['city'] ?? null,
            address: $data['address'] ?? null,
            avatar: $data['avatar'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'city' => $this->city,
            'address' => $this->address,
            'avatar' => $this->avatar,
        ], fn($value) => $value !== null);
    }
}
