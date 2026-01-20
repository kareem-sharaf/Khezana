<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Actions\Approval\ApproveAction;
use App\Models\Request;
use App\Models\User;

/**
 * Action to approve a request
 */
class ApproveRequestAction
{
    public function __construct(
        private readonly ApproveAction $approveAction
    ) {
    }

    /**
     * Approve a request
     *
     * @param Request $request The request to approve
     * @param User $reviewedBy The admin approving the request
     * @return Request
     * @throws \Exception If request cannot be approved
     */
    public function execute(Request $request, User $reviewedBy): Request
    {
        $approval = $request->approval();

        if (!$approval) {
            throw new \Exception('Request does not have an approval record.');
        }

        $this->approveAction->execute($approval, $reviewedBy);

        return $request->fresh();
    }
}
