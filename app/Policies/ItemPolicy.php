<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

/**
 * Item Policy
 *
 * Authorization rules:
 * - User can manage only their own items
 * - Admin can view all items
 * - Admin cannot edit item content (only approve/reject)
 * - Super Admin can override everything
 */
class ItemPolicy
{
    /**
     * Determine if the user can view any items.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view items
    }

    /**
     * Determine if the user can view the item.
     */
    public function view(User $user, Item $item): bool
    {
        // User can view their own items or if item is approved
        return $user->id === $item->user_id || $item->isApproved() || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create items.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create items
    }

    /**
     * Determine if the user can update the item.
     */
    public function update(User $user, Item $item): bool
    {
        // User can update their own items
        if ($user->id === $item->user_id) {
            return true;
        }

        // Super admin can update any item
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Regular admin cannot edit item content
        return false;
    }

    /**
     * Determine if the user can delete the item.
     *
     * Rules:
     * - Regular users: Can delete own items only in specific states (Draft, Pending, Rejected, Available without active relationships)
     * - Admin: Can soft delete any item with reason (except items with active relationships)
     * - Super Admin: Can delete any item (soft or hard delete with restrictions)
     */
    public function delete(User $user, Item $item): bool
    {
        // Super admin always can delete
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin can delete (soft delete)
        if ($user->hasRole('admin')) {
            return app(\App\Services\ItemDeletionService::class)
                ->canAdminDelete($user, $item);
        }

        // Regular user can delete own items in specific conditions
        return app(\App\Services\ItemDeletionService::class)
            ->canUserDelete($user, $item);
    }

    /**
     * Determine if the user can hard delete the item (permanent deletion).
     * Only super admin can hard delete, and only under strict conditions.
     */
    public function hardDelete(User $user, Item $item): bool
    {
        if (!$user->hasRole('super_admin')) {
            return false;
        }

        return app(\App\Services\ItemDeletionService::class)
            ->canSuperAdminHardDelete($user, $item);
    }

    /**
     * Determine if the user can restore a soft-deleted item.
     */
    public function restore(User $user, Item $item): bool
    {
        // Only admin and super admin can restore
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function archive(User $user, Item $item): bool
    {
        if ($user->id === $item->user_id) {
            return true;
        }

        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Determine if the user can submit the item for approval.
     */
    public function submitForApproval(User $user, Item $item): bool
    {
        // Only item owner can submit for approval
        return $user->id === $item->user_id;
    }

    /**
     * Determine if the user can manage (approve/reject) the item.
     */
    public function manage(User $user, Item $item): bool
    {
        // Only admins can manage items (approve/reject)
        return $user->hasAnyRole(['admin', 'super_admin']);
    }
}
