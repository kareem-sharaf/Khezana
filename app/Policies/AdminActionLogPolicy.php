<?php

namespace App\Policies;

use App\Models\AdminActionLog;
use App\Models\User;

class AdminActionLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_reports');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdminActionLog $adminActionLog): bool
    {
        // Users can view their own action logs, admins can view all
        return $user->id === $adminActionLog->admin_id 
            || $user->hasAnyRole(['super_admin', 'admin']) 
            || $user->can('view_reports');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Logs are created automatically, but allow admins
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdminActionLog $adminActionLog): bool
    {
        // Logs should not be editable, only super admin can modify
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdminActionLog $adminActionLog): bool
    {
        // Only super admin can delete logs
        return $user->isSuperAdmin();
    }
}
