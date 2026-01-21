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
     */
    public function delete(User $user, Request $request): bool
    {
        return $user->hasRole('super_admin');
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
