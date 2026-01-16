<?php

namespace App\Repositories;

use App\DTOs\AdminActionLogDTO;
use App\Models\AdminActionLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminActionLogRepository extends BaseRepository
{
    public function __construct(AdminActionLog $model)
    {
        parent::__construct($model);
    }

    /**
     * Create log from DTO.
     */
    public function createFromDTO(AdminActionLogDTO $dto): AdminActionLog
    {
        return $this->create($dto->toArray());
    }

    /**
     * Get logs by admin ID.
     */
    public function getByAdminId(int $adminId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('admin_id', $adminId)
            ->with('admin')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get logs by target type and ID.
     */
    public function getByTarget(string $targetType, ?int $targetId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('target_type', $targetType);

        if ($targetId) {
            $query->where('target_id', $targetId);
        }

        return $query->with('admin')->latest()->paginate($perPage);
    }

    /**
     * Get logs by action type.
     */
    public function getByActionType(string $actionType, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byActionType($actionType)
            ->with('admin')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get recent logs.
     */
    public function getRecent(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->with('admin')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
