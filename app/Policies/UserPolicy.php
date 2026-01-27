<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     * Uses 'view_users' permission instead of role check.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_users');
    }

    /**
     * Determine whether the user can view the model.
     * Users can view their own profile, others need permission.
     */
    public function view(User $user, User $model): bool
    {
        // Users can always view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Others need permission
        return $user->can('view_users');
    }

    /**
     * Determine whether the user can create models.
     * Requires 'create_users' permission.
     */
    public function create(User $user): bool
    {
        return $user->can('create_users');
    }

    /**
     * Determine whether the user can update the model.
     * Users can update their own profile, others need permission.
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

        // Need update_users permission
        return $user->can('update_users');
    }

    /**
     * Determine whether the user can delete the model.
     * Requires 'delete_users' permission.
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

        return $user->can('delete_users');
    }

    /**
     * Determine whether the user can restore the model.
     * Requires 'update_users' permission.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('update_users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only super admin can force delete, and not themselves.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Cannot force delete super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can manage roles for a user.
     * Requires 'manage_roles' permission or be super admin.
     */
    public function manageRoles(User $user, User|string|null $model = null): bool
    {
        // If checking for a specific model instance
        if ($model instanceof User) {
            // Cannot manage roles of super admin unless you are super admin
            if ($model->isSuperAdmin() && !$user->isSuperAdmin()) {
                return false;
            }

            // Cannot manage your own roles if you're removing super_admin role
            if ($user->id === $model->id && $user->isSuperAdmin()) {
                return false; // Cannot remove your own super_admin role
            }
        }

        return $user->can('manage_roles');
    }

    /**
     * Determine whether the user can manage branches.
     * Sellers can only manage their own branch.
     * Admins can manage all branches.
     */
    public function manageBranches(User $user, User|null $model = null): bool
    {
        // Super admin can manage all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admins can manage their own branch
        if ($user->isAdmin() && $model?->branch_id === $user->branch_id) {
            return true;
        }

        return $user->can('manage_branches');
    }
}
