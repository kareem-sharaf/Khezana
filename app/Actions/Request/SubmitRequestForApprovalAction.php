<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Models\Request;
use App\Models\User;

/**
 * Action to submit a request for approval
 */
class SubmitRequestForApprovalAction
{
    public function __construct(
        private readonly SubmitForApprovalAction $submitForApprovalAction
    ) {
    }

    /**
     * Submit a request for approval
     *
     * @param Request $request The request to submit
     * @param User $user The user submitting the request
     * @return \App\Models\Approval
     * @throws \Exception If request cannot be submitted
     */
    public function execute(Request $request, User $user): \App\Models\Approval
    {
        // Only request owner can submit
        if ($request->user_id !== $user->id) {
            throw new \Exception('Only the request owner can submit for approval');
        }

        // Use the shared SubmitForApprovalAction
        return $this->submitForApprovalAction->execute($request, $user);
    }
}
