<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
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
        private UserProfileRepository $profileRepository,
    ) {
    }

    /**
     * Create a new user.
     */
    public function create(UserDTO $dto, ?array $profileData = null, ?array $roles = null): User
    {
        return DB::transaction(function () use ($dto, $profileData, $roles) {
            // Hash password if provided
            if ($dto->password) {
                $dto->password = Hash::make($dto->password);
            }

            // Create user
            $user = $this->userRepository->createFromDTO($dto);

            // Assign roles if provided
            if ($roles && !empty($roles)) {
                $user->syncRoles($roles);
            } else {
                // Assign default role
                $user->assignRole('user');
            }

            // Create profile if data provided
            if ($profileData) {
                $profileDto = UserProfileDTO::fromArray(array_merge($profileData, ['user_id' => $user->id]));
                $this->profileRepository->createOrUpdateFromDTO($profileDto);
            }

            // Dispatch event
            event(new UserCreated($user));

            return $user->load(['roles', 'profile']);
        });
    }

    /**
     * Update user.
     */
    public function update(int $userId, UserDTO $dto, ?array $profileData = null): User
    {
        return DB::transaction(function () use ($userId, $dto, $profileData) {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // Protect super admin from modification
            if ($user->isSuperAdmin() && auth()->id() !== $user->id) {
                throw new \Exception("Cannot modify super admin user");
            }

            // Hash password if provided
            if ($dto->password) {
                $dto->password = Hash::make($dto->password);
            } else {
                // Remove password from DTO if not updating
                unset($dto->password);
            }

            // Update user
            $updatedUser = $this->userRepository->updateFromDTO($userId, $dto);

            // Update profile if data provided
            if ($profileData) {
                $profileDto = UserProfileDTO::fromArray(array_merge($profileData, ['user_id' => $userId]));
                $this->profileRepository->createOrUpdateFromDTO($profileDto);
            }

            // Dispatch event
            event(new UserUpdated($updatedUser));

            return $updatedUser->load(['roles', 'profile']);
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
        return $this->userRepository->find($userId)?->load(['roles', 'permissions', 'profile']);
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
