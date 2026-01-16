<?php

namespace App\DTOs;

class AdminActionLogDTO
{
    public function __construct(
        public int $adminId,
        public string $actionType,
        public string $targetType,
        public ?int $targetId = null,
        public ?string $notes = null,
        public ?array $oldValues = null,
        public ?array $newValues = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            adminId: $data['admin_id'],
            actionType: $data['action_type'],
            targetType: $data['target_type'],
            targetId: $data['target_id'] ?? null,
            notes: $data['notes'] ?? null,
            oldValues: $data['old_values'] ?? null,
            newValues: $data['new_values'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'admin_id' => $this->adminId,
            'action_type' => $this->actionType,
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'notes' => $this->notes,
            'old_values' => $this->oldValues,
            'new_values' => $this->newValues,
        ], fn($value) => $value !== null);
    }
}
