<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Models\Item;
use App\Models\User;

/**
 * Action to submit an item for approval
 */
class SubmitItemForApprovalAction
{
    public function __construct(
        private readonly SubmitForApprovalAction $submitForApprovalAction
    ) {
    }

    /**
     * Submit an item for approval
     *
     * @param Item $item The item to submit
     * @param User $user The user submitting the item
     * @return \App\Models\Approval
     * @throws \Exception If submission fails
     */
    public function execute(Item $item, User $user): \App\Models\Approval
    {
        return $this->submitForApprovalAction->execute($item, $user);
    }
}
