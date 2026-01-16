<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $password = null,
        public string $status = 'active',
        public ?array $roles = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'] ?? null,
            status: $data['status'] ?? 'active',
            roles: $data['roles'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'status' => $this->status,
        ];
    }
}
