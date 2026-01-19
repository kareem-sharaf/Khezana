<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

/**
 * Policy for Approval model
 */
class ApprovalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view approvals list
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_approvals');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Approval $approval): bool
    {
        // User can view their own submissions, admins can view all
        if ($approval->submitted_by === $user->id) {
            return true;
        }

        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_approvals');
    }

    /**
     * Determine whether the user can create models.
     * 
     * Note: Users create approvals by submitting content, not directly
     */
    public function create(User $user): bool
    {
        // Any authenticated user can submit content for approval
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Approval $approval): bool
    {
        // Only admins can update approvals (approve/reject)
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_approvals');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Approval $approval): bool
    {
        // Only super admin can delete approvals
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can approve content.
     */
    public function approve(User $user, Approval $approval): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_approvals');
    }

    /**
     * Determine whether the user can reject content.
     */
    public function reject(User $user, Approval $approval): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_approvals');
    }

    /**
     * Determine whether the user can archive content.
     */
    public function archive(User $user, Approval $approval): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_approvals');
    }

    /**
     * Determine whether the user can submit content for approval.
     */
    public function submit(User $user, mixed $approvable): bool
    {
        // User can only submit their own content
        if (method_exists($approvable, 'user_id') && $approvable->user_id !== $user->id) {
            return false;
        }

        // Check if content has a user relationship
        if (method_exists($approvable, 'user') && $approvable->user?->id !== $user->id) {
            return false;
        }

        return true;
    }
}
