<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Contracts\Approvable;
use App\Enums\ApprovalStatus;
use App\Events\Approval\ContentSubmitted;
use App\Models\Approval;
use App\Models\User;

/**
 * Action to submit content for approval
 */
class SubmitForApprovalAction
{
    /**
     * Submit content for approval
     *
     * @param Approvable $approvable The model to submit for approval
     * @param User $submittedBy The user submitting the content
     * @return Approval
     * @throws \Exception If content is already submitted or cannot be submitted
     */
    public function execute(Approvable $approvable, User $submittedBy): Approval
    {
        // Check if already has an approval record
        $existingApproval = $approvable->approval();

        if ($existingApproval) {
            $approval = $existingApproval;

            // Allow resubmission if previously rejected or archived
            if ($approval->status === ApprovalStatus::REJECTED || $approval->status === ApprovalStatus::ARCHIVED) {
                // BR-008.1: Only owner can resubmit (super_admin exception)
                $owner = $approvable->getSubmitter();
                if ($owner && $submittedBy->id !== $owner->id) {
                    if (!$submittedBy->hasRole('super_admin')) {
                        throw new \Exception('Only the content owner can resubmit for approval.');
                    }
                }

                $approval->update([
                    'status' => ApprovalStatus::PENDING,
                    'submitted_by' => $submittedBy->id,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'resubmission_count' => ($approval->resubmission_count ?? 0) + 1,
                ]);

                event(new ContentSubmitted($approval));

                return $approval->fresh();
            }

            // If already pending, return existing
            if ($approval->status === ApprovalStatus::PENDING) {
                return $approval;
            }

            throw new \Exception('Content is already approved and cannot be resubmitted.');
        }

        // Create new approval record
        $approval = Approval::create([
            'approvable_type' => $approvable->getApprovalType(),
            'approvable_id' => $approvable->id,
            'status' => ApprovalStatus::PENDING,
            'submitted_by' => $submittedBy->id,
        ]);

        event(new ContentSubmitted($approval));

        return $approval;
    }
}
