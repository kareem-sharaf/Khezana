<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\ApprovalStatus;
use App\Events\Approval\ContentArchived;
use App\Models\Approval;
use App\Models\User;

/**
 * Action to archive content
 */
class ArchiveAction
{
    /**
     * Archive content
     *
     * @param Approval $approval The approval record to archive
     * @param User $reviewedBy The admin archiving the content
     * @return Approval
     * @throws \Exception If approval cannot be archived
     */
    public function execute(Approval $approval, User $reviewedBy): Approval
    {
        // Validate state transition - can archive from any status except already archived
        if ($approval->status === ApprovalStatus::ARCHIVED) {
            throw new \Exception('Content is already archived.');
        }

        // Update approval
        $approval->update([
            'status' => ApprovalStatus::ARCHIVED,
            'reviewed_by' => $reviewedBy->id,
            'reviewed_at' => now(),
        ]);

        event(new ContentArchived($approval));

        return $approval->fresh();
    }
}
