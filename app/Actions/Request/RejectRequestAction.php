<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Actions\Approval\RejectAction;
use App\Models\Request;
use App\Models\User;

/**
 * Action to reject a request
 */
class RejectRequestAction
{
    public function __construct(
        private readonly RejectAction $rejectAction
    ) {
    }

    /**
     * Reject a request
     *
     * @param Request $request The request to reject
     * @param User $reviewedBy The admin rejecting the request
     * @param string|null $rejectionReason The reason for rejection
     * @return Request
     * @throws \Exception If request cannot be rejected
     */
    public function execute(Request $request, User $reviewedBy, ?string $rejectionReason = null): Request
    {
        $approval = $request->approval();

        if (!$approval) {
            throw new \Exception('Request does not have an approval record.');
        }

        $this->rejectAction->execute($approval, $reviewedBy, $rejectionReason);

        return $request->fresh();
    }
}
