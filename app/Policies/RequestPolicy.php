<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Request;
use App\Models\User;

/**
 * Request Policy
 *
 * Authorization rules:
 * - User can manage only their own requests
 * - Admin can view all requests
 * - Admin cannot edit request content (only approve/reject)
 * - Super Admin can override everything
 */
class RequestPolicy
{
    /**
     * Determine if the user can view any requests.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view requests
    }

    /**
     * Determine if the user can view the request.
     */
    public function view(User $user, Request $request): bool
    {
        // User can view their own requests or if request is approved
        return $user->id === $request->user_id || $request->isApproved() || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create requests.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create requests
    }

    /**
     * Determine if the user can update the request.
     */
    public function update(User $user, Request $request): bool
    {
        // Only request owner can update, or super admin
        return $user->id === $request->user_id || $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can delete the request.
     *
     * Rules:
     * - Regular users: Can delete own requests only in specific states (Pending, Rejected, Open without accepted offers)
     * - Admin: Can soft delete any request with reason (except requests with accepted offers)
     * - Super Admin: Can delete any request (soft or hard delete with restrictions)
     */
    public function delete(User $user, Request $request): bool
    {
        // Super admin always can delete
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin can delete (soft delete)
        if ($user->hasRole('admin')) {
            return app(\App\Services\RequestDeletionService::class)
                ->canAdminDelete($user, $request);
        }

        // Regular user can delete own requests in specific conditions
        return app(\App\Services\RequestDeletionService::class)
            ->canUserDelete($user, $request);
    }

    /**
     * Determine if the user can hard delete the request (permanent deletion).
     * Only super admin can hard delete, and only under strict conditions.
     */
    public function hardDelete(User $user, Request $request): bool
    {
        if (!$user->hasRole('super_admin')) {
            return false;
        }

        return app(\App\Services\RequestDeletionService::class)
            ->canSuperAdminHardDelete($user, $request);
    }

    /**
     * Determine if the user can restore a soft-deleted request.
     */
    public function restore(User $user, Request $request): bool
    {
        // Only admin and super admin can restore
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function archive(User $user, Request $request): bool
    {
        if ($user->id === $request->user_id) {
            return true;
        }

        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Determine if the user can submit the request for approval.
     */
    public function submitForApproval(User $user, Request $request): bool
    {
        // Only request owner can submit
        return $user->id === $request->user_id;
    }

    /**
     * Determine if the user can close the request.
     */
    public function close(User $user, Request $request): bool
    {
        // Only request owner can close
        return $user->id === $request->user_id;
    }

    /**
     * Determine if the user can manage (approve/reject) requests.
     */
    public function manage(User $user, Request $request): bool
    {
        // Only admins can approve/reject
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }
}
