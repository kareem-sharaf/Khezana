<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\ApprovalStatus;
use App\Events\Approval\ContentRejected;
use App\Models\Approval;
use App\Models\User;
use App\Services\AdminActionLogService;

/**
 * Action to reject content
 */
class RejectAction
{
    public function __construct(
        private readonly AdminActionLogService $logService
    ) {
    }

    /**
     * Reject content
     *
     * @param Approval $approval The approval record to reject
     * @param User $reviewedBy The admin rejecting the content
     * @param string|null $rejectionReason The reason for rejection
     * @return Approval
     * @throws \Exception If approval cannot be rejected
     */
    public function execute(Approval $approval, User $reviewedBy, ?string $rejectionReason = null): Approval
    {
        if ($approval->status !== ApprovalStatus::PENDING) {
            throw new \Exception(
                sprintf('Cannot reject content with status: %s. Only pending content can be rejected.', $approval->status->value)
            );
        }

        if ($approval->status === ApprovalStatus::ARCHIVED) {
            throw new \Exception('Cannot reject archived content.');
        }

        $approval->update([
            'status' => ApprovalStatus::REJECTED,
            'reviewed_by' => $reviewedBy->id,
            'reviewed_at' => now(),
            'rejection_reason' => $rejectionReason,
        ]);

        $this->logService->logReject($approval->approvable, $reviewedBy->id, $rejectionReason);

        event(new ContentRejected($approval));

        return $approval->fresh();
    }
}
