<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Models\Request;
use App\Models\User;
use App\Services\AdminActionLogService;
use Illuminate\Support\Facades\DB;

/**
 * Professional Request Deletion Action
 *
 * Handles soft delete, archive, and hard delete with proper logging
 */
class DeleteRequestAction
{
    public function __construct(
        private readonly AdminActionLogService $logService,
    ) {
    }

    /**
     * Soft delete a request (regular user or admin)
     *
     * @param Request $request The request to delete
     * @param User $user The user performing the deletion
     * @param string|null $reason Reason for deletion (required for admin)
     * @param bool $archive Whether to archive the request
     * @return bool
     * @throws \Exception If deletion fails
     */
    public function softDelete(Request $request, User $user, ?string $reason = null, bool $archive = false): bool
    {
        return DB::transaction(function () use ($request, $user, $reason, $archive) {
            $oldValues = $request->toArray();

            if ($archive) {
                // Archive and soft delete
                $request->update(['archived_at' => now()]);
                $request->delete();

                // Log admin action if admin is deleting
                if ($user->hasAnyRole(['admin', 'super_admin'])) {
                    $this->logService->logArchive(
                        $request,
                        $user->id,
                        $reason ?? 'Request archived by admin'
                    );
                }
            } else {
                // Soft delete only
                // Don't delete approval relation - keep for audit trail
                $request->delete();

                // Log admin action if admin is deleting
                if ($user->hasAnyRole(['admin', 'super_admin'])) {
                    $this->logService->logDelete(
                        $request,
                        $user->id,
                        $reason ?? 'Request deleted by admin'
                    );
                }
            }

            return true;
        });
    }

    /**
     * Hard delete a request (super admin only)
     *
     * WARNING: This is permanent and cannot be undone
     *
     * @param Request $request The request to permanently delete
     * @param User $user The super admin performing the deletion
     * @param string $reason Reason for hard deletion (required)
     * @return bool
     * @throws \Exception If deletion fails or user is not super admin
     */
    public function hardDelete(Request $request, User $user, string $reason): bool
    {
        if (!$user->hasRole('super_admin')) {
            throw new \Exception('Only super admin can perform hard delete.');
        }

        return DB::transaction(function () use ($request, $user, $reason) {
            $oldValues = $request->toArray();

            // Soft delete associated attributes
            $request->itemAttributes()->delete();

            // Don't delete approval relation - keep for audit trail
            // $request->approvalRelation()->delete();

            // Log before permanent deletion
            $this->logService->logHardDelete(
                $request,
                $user->id,
                $reason
            );

            // Permanently delete the request
            $request->forceDelete();

            return true;
        });
    }

    /**
     * Legacy method for backward compatibility
     *
     * @deprecated Use softDelete() or hardDelete() instead
     */
    public function execute(Request $request): bool
    {
        return DB::transaction(function () use ($request) {
            // Soft delete associated attributes
            $request->itemAttributes()->delete();

            // Soft delete the request (keep approval for audit)
            return $request->delete();
        });
    }
}
