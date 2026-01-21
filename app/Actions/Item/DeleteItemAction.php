<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Models\Item;
use App\Models\User;
use App\Services\AdminActionLogService;
use Illuminate\Support\Facades\DB;

/**
 * Professional Item Deletion Action
 *
 * Handles soft delete, archive, and hard delete with proper logging
 */
class DeleteItemAction
{
    public function __construct(
        private readonly AdminActionLogService $logService,
    ) {
    }

    /**
     * Soft delete an item (regular user or admin)
     *
     * @param Item $item The item to delete
     * @param User $user The user performing the deletion
     * @param string|null $reason Reason for deletion (required for admin)
     * @param bool $archive Whether to archive the item
     * @return bool
     * @throws \Exception If deletion fails
     */
    public function softDelete(Item $item, User $user, ?string $reason = null, bool $archive = false): bool
    {
        return DB::transaction(function () use ($item, $user, $reason, $archive) {
            $oldValues = $item->toArray();

            if ($archive) {
                // Archive and soft delete
                $item->update(['archived_at' => now()]);
                $item->delete();

                // Log admin action if admin is deleting
                if ($user->hasAnyRole(['admin', 'super_admin'])) {
                    $this->logService->logArchive(
                        $item,
                        $user->id,
                        $reason ?? 'Item archived by admin'
                    );
                }
            } else {
                // Soft delete only
                $item->delete();

                // Log admin action if admin is deleting
                if ($user->hasAnyRole(['admin', 'super_admin'])) {
                    $this->logService->logDelete(
                        $item,
                        $user->id,
                        $reason ?? 'Item deleted by admin'
                    );
                }
            }

            return true;
        });
    }

    /**
     * Hard delete an item (super admin only)
     *
     * WARNING: This is permanent and cannot be undone
     *
     * @param Item $item The item to permanently delete
     * @param User $user The super admin performing the deletion
     * @param string $reason Reason for hard deletion (required)
     * @return bool
     * @throws \Exception If deletion fails or user is not super admin
     */
    public function hardDelete(Item $item, User $user, string $reason): bool
    {
        if (!$user->hasRole('super_admin')) {
            throw new \Exception('Only super admin can perform hard delete.');
        }

        return DB::transaction(function () use ($item, $user, $reason) {
            $oldValues = $item->toArray();

            // Soft delete associated images (don't hard delete files)
            $item->images()->delete();

            // Soft delete associated attributes
            $item->itemAttributes()->delete();

            // Log before permanent deletion
            $this->logService->logHardDelete(
                $item,
                $user->id,
                $reason
            );

            // Permanently delete the item
            $item->forceDelete();

            return true;
        });
    }

    /**
     * Legacy method for backward compatibility
     *
     * @deprecated Use softDelete() or hardDelete() instead
     */
    public function execute(Item $item): bool
    {
        return DB::transaction(function () use ($item) {
            // Soft delete associated images
            $item->images()->delete();

            // Soft delete associated attributes
            $item->itemAttributes()->delete();

            // Soft delete the item
            return $item->delete();
        });
    }
}
