<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\ApprovalStatus;
use App\Events\Approval\ContentArchived;
use App\Models\Approval;
use App\Models\User;
use App\Services\AdminActionLogService;

/**
 * Action to archive content
 */
class ArchiveAction
{
    public function __construct(
        private readonly AdminActionLogService $logService
    ) {
    }

    /**
     * Archive content
     *
     * @param Approval $approval The approval record to archive
     * @param User $reviewedBy The admin archiving the content
     * @param string|null $reason Archive reason (required)
     * @return Approval
     * @throws \Exception If approval cannot be archived
     */
    public function execute(Approval $approval, User $reviewedBy, ?string $reason = null): Approval
    {
        if ($approval->status === ApprovalStatus::ARCHIVED) {
            throw new \Exception('Content is already archived.');
        }

        if (empty($reason)) {
            throw new \Exception('Archive reason is required.');
        }

        $approval->update([
            'status' => ApprovalStatus::ARCHIVED,
            'reviewed_by' => $reviewedBy->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $this->logService->logArchive($approval->approvable, $reviewedBy->id, $reason);

        event(new ContentArchived($approval));

        return $approval->fresh();
    }
}
