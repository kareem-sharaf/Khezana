<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Actions\Approval\ArchiveAction;
use App\Models\Request;
use App\Models\User;

/**
 * Action to archive a request
 */
class ArchiveRequestAction
{
    public function __construct(
        private readonly ArchiveAction $archiveAction
    ) {
    }

    /**
     * Archive a request
     *
     * @param Request $request The request to archive
     * @param User $reviewedBy The admin archiving the request
     * @return Request
     * @throws \Exception If request cannot be archived
     */
    public function execute(Request $request, User $reviewedBy): Request
    {
        $approval = $request->approval();

        if (!$approval) {
            throw new \Exception('Request does not have an approval record.');
        }

        $this->archiveAction->execute($approval, $reviewedBy);

        return $request->fresh();
    }
}
