<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Service to check if an item can be deleted
 *
 * Business Rules:
 * - Regular users can delete only their own items in specific states
 * - Items with active offers/requests cannot be deleted
 * - Admin can delete with reason (soft delete + archive)
 * - Super Admin has full deletion capabilities
 */
class ItemDeletionService
{
    /**
     * Check if a regular user can delete an item
     */
    public function canUserDelete(User $user, Item $item): bool
    {
        // Must be the owner
        if ($user->id !== $item->user_id) {
            return false;
        }

        // Check if item has active relationships that prevent deletion
        if ($this->hasActiveRelationships($item)) {
            return false;
        }

        // Check approval status
        $approvalStatus = $item->approvalRelation?->status;

        // Can delete if: Draft, Pending, Rejected
        if (in_array($approvalStatus, [
            ApprovalStatus::PENDING,
            ApprovalStatus::REJECTED,
        ])) {
            return true;
        }

        // Can delete if Approved + Available + No active relationships
        if ($approvalStatus === ApprovalStatus::APPROVED) {
            $isAvailable = $item->availability_status
                ? $item->availability_status === ItemAvailability::AVAILABLE
                : $item->is_available;

            if ($isAvailable && !$this->hasActiveRelationships($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if item has active relationships that prevent deletion
     */
    public function hasActiveRelationships(Item $item): bool
    {
        // Check if item is soft deleted (no need to check relationships if already deleted)
        if ($item->trashed()) {
            return false;
        }

        // Check for active offers (if Offer model exists)
        if (class_exists(\App\Models\Offer::class)) {
            $hasActiveOffers = \App\Models\Offer::where('item_id', $item->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();

            if ($hasActiveOffers) {
                return true;
            }
        }

        // TODO: Check for active rentals when rental system is implemented
        // TODO: Check for active transactions when payment system is implemented
        // TODO: Check for pending disputes when dispute system is implemented

        return false;
    }

    /**
     * Get reason why item cannot be deleted (for UI display)
     */
    public function getDeletionBlockReason(User $user, Item $item): ?string
    {
        if ($user->id !== $item->user_id) {
            return __('items.deletion.not_owner');
        }

        if ($this->hasActiveRelationships($item)) {
            return __('items.deletion.has_active_relationships');
        }

        $approvalStatus = $item->approvalRelation?->status;

        if ($approvalStatus === ApprovalStatus::APPROVED) {
            $isAvailable = $item->availability_status
                ? $item->availability_status === ItemAvailability::AVAILABLE
                : $item->is_available;

            if (!$isAvailable) {
                return __('items.deletion.not_available');
            }
        }

        return null;
    }

    /**
     * Check if admin can delete an item
     */
    public function canAdminDelete(User $admin, Item $item): bool
    {
        if (!$admin->hasAnyRole(['admin', 'super_admin'])) {
            return false;
        }

        // Admin cannot hard delete items with active relationships
        // Must use archive instead
        if ($this->hasActiveRelationships($item)) {
            return false;
        }

        return true;
    }

    /**
     * Check if super admin can hard delete an item
     */
    public function canSuperAdminHardDelete(User $admin, Item $item): bool
    {
        if (!$admin->hasRole('super_admin')) {
            return false;
        }

        // Must be soft deleted for at least 30 days
        if ($item->deleted_at && $item->deleted_at->diffInDays(now()) < 30) {
            return false;
        }

        // Must have no active relationships
        if ($this->hasActiveRelationships($item)) {
            return false;
        }

        return true;
    }
}
