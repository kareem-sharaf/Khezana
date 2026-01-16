<?php

namespace App\Repositories;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find user by phone.
     */
    public function findByPhone(string $phone): ?User
    {
        return $this->model->where('phone', $phone)->first();
    }

    /**
     * Create user from DTO.
     */
    public function createFromDTO(UserDTO $dto): User
    {
        return $this->create($dto->toArray());
    }

    /**
     * Update user from DTO.
     */
    public function updateFromDTO(int $id, UserDTO $dto): ?User
    {
        return $this->update($id, $dto->toArray());
    }

    /**
     * Get users with roles.
     */
    public function withRoles(): Collection
    {
        return $this->model->with('roles')->get();
    }

    /**
     * Get active users.
     */
    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    /**
     * Paginate users with roles and permissions.
     */
    public function paginateWithRelations(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['roles', 'permissions', 'profile'])->paginate($perPage);
    }

    /**
     * Search users by name, email, or phone.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->with(['roles', 'profile'])
            ->paginate($perPage);
    }
}
