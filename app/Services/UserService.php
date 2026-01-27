<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Events\UserDeleted;
use App\Models\User;
use App\Repositories\UserProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * Validate roles - only ONE role per user
     */
    private function validateRoles(array $roles): void
    {
        if (count($roles) !== 1) {
            throw new \InvalidArgumentException('User must have exactly one role.');
        }

        $validRoles = ['super_admin', 'admin', 'seller', 'user'];
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles)) {
                throw new \InvalidArgumentException("Invalid role: {$role}");
            }
        }
    }

    /**
     * Validate branch_id based on roles.
     * - seller MUST have branch_id
     * - admin/super_admin MUST NOT have branch_id
     * - user MUST NOT have branch_id
     */
    private function validateBranchForRoles(?int $branchId, array $roles): void
    {
        $isSeller = in_array('seller', $roles);
        $isAdmin = in_array('admin', $roles) || in_array('super_admin', $roles);
        $isUser = in_array('user', $roles);

        // Seller must have branch_id
        if ($isSeller && empty($branchId)) {
            throw new \InvalidArgumentException('Seller must have a branch assigned.');
        }

        // Admin/Super Admin cannot have branch_id
        if (($isAdmin || $isUser) && !empty($branchId)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s cannot have a branch assigned.',
                    $isAdmin ? 'Admin' : 'User'
                )
            );
        }
    }

    /**
     * Create a new user.
     */
    public function create(UserDTO $dto, ?array $roles = null): User
    {
        return DB::transaction(function () use ($dto, $roles) {
            // Determine roles (default to 'user')
            $rolesToAssign = $roles && !empty($roles) ? $roles : ['user'];

            // Validate roles - only ONE role per user
            $this->validateRoles($rolesToAssign);

            // Validate branch_id based on roles
            $this->validateBranchForRoles($dto->branch_id, $rolesToAssign);

            // Hash password if provided
            if ($dto->password) {
                $dto->password = Hash::make($dto->password);
            }

            // Create user
            $user = $this->userRepository->createFromDTO($dto);

            // Assign roles
            $user->syncRoles($rolesToAssign);

            // Dispatch event
            event(new UserCreated($user));

            return $user->load(['roles']);
        });
    }

    /**
     * Update user.
     */
    public function update(int $userId, UserDTO $dto, ?array $newRoles = null): User
    {
        return DB::transaction(function () use ($userId, $dto, $newRoles) {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // Protect super admin from modification by others
            if ($user->isSuperAdmin() && auth()->id() !== $user->id) {
                throw new \Exception("Cannot modify super admin user");
            }

            // Get current roles if not updating them
            $rolesToUse = $newRoles !== null ? $newRoles : $user->roles->pluck('name')->toArray();

            // Validate roles - only ONE role per user
            $this->validateRoles($rolesToUse);

            // Validate branch_id based on roles
            $this->validateBranchForRoles($dto->branch_id, $rolesToUse);

            // Hash password if provided
            if ($dto->password) {
                $dto->password = Hash::make($dto->password);
            } else {
                // Remove password from DTO if not updating
                unset($dto->password);
            }

            // Update user
            $updatedUser = $this->userRepository->updateFromDTO($userId, $dto);

            // Update roles if provided
            if ($newRoles !== null) {
                $updatedUser->syncRoles($newRoles);
            }

            // Dispatch event
            event(new UserUpdated($updatedUser));

            return $updatedUser->load(['roles', 'branch']);
        });
    }

    /**
     * Delete user.
     */
    public function delete(int $userId): bool
    {
        return DB::transaction(function () use ($userId) {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // Protect super admin from deletion
            if ($user->isSuperAdmin()) {
                throw new \Exception("Cannot delete super admin user");
            }

            // Dispatch event before deletion
            event(new UserDeleted($user));

            // Delete user (profile will be deleted via cascade)
            return $this->userRepository->delete($userId);
        });
    }

    /**
     * Assign roles to user.
     */
    public function assignRoles(int $userId, array $roles): User
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception("User not found");
        }

        // Protect super admin
        if ($user->isSuperAdmin() && !in_array('super_admin', $roles)) {
            throw new \Exception("Cannot remove super_admin role from super admin user");
        }

        $user->syncRoles($roles);

        return $user->load('roles');
    }

    /**
     * Get user with relations.
     */
    public function getWithRelations(int $userId): ?User
    {
        return $this->userRepository->find($userId)?->load(['roles', 'permissions']);
    }

    /**
     * Search users.
     */
    public function search(string $query, int $perPage = 15)
    {
        return $this->userRepository->search($query, $perPage);
    }

    /**
     * List users with pagination.
     */
    public function list(int $perPage = 15)
    {
        return $this->userRepository->paginateWithRelations($perPage);
    }
}
