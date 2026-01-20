<?php

namespace App\Services;

use App\DTOs\AdminActionLogDTO;
use App\Repositories\AdminActionLogRepository;
use Illuminate\Database\Eloquent\Model;

class AdminActionLogService extends BaseService
{
    public function __construct(
        private AdminActionLogRepository $logRepository,
    ) {
    }

    /**
     * Log an admin action.
     */
    public function log(
        int $adminId,
        string $actionType,
        string $targetType,
        ?int $targetId = null,
        ?string $notes = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $dto = AdminActionLogDTO::fromArray([
            'admin_id' => $adminId,
            'action_type' => $actionType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'notes' => $notes,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        $this->logRepository->createFromDTO($dto);
    }

    /**
     * Log model creation.
     */
    public function logCreate(Model $model, int $adminId, ?string $notes = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'created',
            targetType: get_class($model),
            targetId: $model->id,
            notes: $notes,
            newValues: $model->toArray(),
        );
    }

    /**
     * Log model update.
     */
    public function logUpdate(Model $model, int $adminId, array $oldValues, ?string $notes = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'updated',
            targetType: get_class($model),
            targetId: $model->id,
            notes: $notes,
            oldValues: $oldValues,
            newValues: $model->getChanges(),
        );
    }

    /**
     * Log model deletion.
     */
    public function logDelete(Model $model, int $adminId, ?string $notes = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'deleted',
            targetType: get_class($model),
            targetId: $model->id,
            notes: $notes,
            oldValues: $model->toArray(),
        );
    }

    /**
     * Get logs by admin.
     */
    public function getByAdmin(int $adminId, int $perPage = 15)
    {
        return $this->logRepository->getByAdminId($adminId, $perPage);
    }

    /**
     * Get logs by target.
     */
    public function getByTarget(string $targetType, ?int $targetId = null, int $perPage = 15)
    {
        return $this->logRepository->getByTarget($targetType, $targetId, $perPage);
    }

    public function logApprove(Model $approvable, int $adminId, ?string $notes = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'approved',
            targetType: get_class($approvable),
            targetId: $approvable->id,
            notes: $notes,
            newValues: ['status' => 'approved'],
        );
    }

    public function logReject(Model $approvable, int $adminId, ?string $reason = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'rejected',
            targetType: get_class($approvable),
            targetId: $approvable->id,
            notes: $reason,
            newValues: ['status' => 'rejected', 'rejection_reason' => $reason],
        );
    }

    public function logArchive(Model $approvable, int $adminId, ?string $reason = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'archived',
            targetType: get_class($approvable),
            targetId: $approvable->id,
            notes: $reason,
            newValues: ['status' => 'archived', 'archive_reason' => $reason],
        );
    }

    public function logHardDelete(Model $model, int $adminId, ?string $notes = null): void
    {
        $this->log(
            adminId: $adminId,
            actionType: 'hard_deleted',
            targetType: get_class($model),
            targetId: $model->id,
            notes: $notes,
            oldValues: $model->toArray(),
        );
    }
}
