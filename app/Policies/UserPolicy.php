<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile, admins can view anyone
        return $user->id === $model->id || $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Cannot modify super admin unless you are the super admin
        if ($model->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Cannot force delete super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function manageRoles(User $user, User|string|null $model = null): bool
    {
        // If checking for a specific model instance, ensure we can manage roles for that user
        if ($model instanceof User) {
            // Cannot manage roles of super admin unless you are super admin
            if ($model->isSuperAdmin() && !$user->isSuperAdmin()) {
                return false;
            }
        }

        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_users');
    }
}
