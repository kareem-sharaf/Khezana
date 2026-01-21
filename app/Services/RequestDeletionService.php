<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\RequestStatus;
use App\Models\Request as RequestModel;
use App\Models\User;

/**
 * Service to check if a request can be deleted
 *
 * Business Rules:
 * - Regular users can delete only their own requests in specific states
 * - Requests with accepted offers cannot be deleted
 * - Admin can delete with reason (soft delete + archive)
 * - Super Admin has full deletion capabilities
 */
class RequestDeletionService
{
    /**
     * Check if a regular user can delete a request
     */
    public function canUserDelete(User $user, RequestModel $request): bool
    {
        // Must be the owner
        if ($user->id !== $request->user_id) {
            return false;
        }

        // Check approval status
        $approvalStatus = $request->approvalRelation?->status;

        // Can delete if: Pending, Rejected (no Draft status for requests)
        if (in_array($approvalStatus, [
            ApprovalStatus::PENDING,
            ApprovalStatus::REJECTED,
        ])) {
            // Also check request status
            if (in_array($request->status, [
                RequestStatus::OPEN,
            ])) {
                // Check if request has accepted offers
                if ($this->hasAcceptedOffers($request)) {
                    return false;
                }
                return true;
            }
        }

        // Cannot delete if fulfilled, closed, or has accepted offers
        return false;
    }

    /**
     * Check if request has accepted offers
     */
    public function hasAcceptedOffers(RequestModel $request): bool
    {
        // Check if request is soft deleted
        if ($request->trashed()) {
            return false;
        }

        // Check for accepted offers
        return $request->offers()
            ->where('status', \App\Enums\OfferStatus::ACCEPTED->value)
            ->exists();
    }

    /**
     * Get reason why request cannot be deleted (for UI display)
     */
    public function getDeletionBlockReason(User $user, RequestModel $request): ?string
    {
        if ($user->id !== $request->user_id) {
            return __('requests.deletion.not_owner');
        }

        if ($this->hasAcceptedOffers($request)) {
            return __('requests.deletion.has_accepted_offers');
        }

        if ($request->status === RequestStatus::FULFILLED) {
            return __('requests.deletion.is_fulfilled');
        }

        if ($request->status === RequestStatus::CLOSED) {
            return __('requests.deletion.is_closed');
        }

        return null;
    }

    /**
     * Check if admin can delete a request
     */
    public function canAdminDelete(User $admin, RequestModel $request): bool
    {
        if (!$admin->hasAnyRole(['admin', 'super_admin'])) {
            return false;
        }

        // Admin cannot delete requests with accepted offers
        if ($this->hasAcceptedOffers($request)) {
            return false;
        }

        return true;
    }

    /**
     * Check if super admin can hard delete a request
     */
    public function canSuperAdminHardDelete(User $admin, RequestModel $request): bool
    {
        if (!$admin->hasRole('super_admin')) {
            return false;
        }

        // Must be soft deleted for at least 30 days
        if ($request->deleted_at && $request->deleted_at->diffInDays(now()) < 30) {
            return false;
        }

        // Must have no accepted offers
        if ($this->hasAcceptedOffers($request)) {
            return false;
        }

        return true;
    }
}
