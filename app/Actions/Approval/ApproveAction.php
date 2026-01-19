<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\ApprovalStatus;
use App\Events\Approval\ContentApproved;
use App\Models\Approval;
use App\Models\User;

/**
 * Action to approve content
 */
class ApproveAction
{
    /**
     * Approve content
     *
     * @param Approval $approval The approval record to approve
     * @param User $reviewedBy The admin approving the content
     * @return Approval
     * @throws \Exception If approval cannot be approved
     */
    public function execute(Approval $approval, User $reviewedBy): Approval
    {
        // Validate state transition
        if ($approval->status !== ApprovalStatus::PENDING) {
            throw new \Exception(
                sprintf('Cannot approve content with status: %s. Only pending content can be approved.', $approval->status->value)
            );
        }

        // Update approval
        $approval->update([
            'status' => ApprovalStatus::APPROVED,
            'reviewed_by' => $reviewedBy->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        event(new ContentApproved($approval));

        return $approval->fresh();
    }
}
