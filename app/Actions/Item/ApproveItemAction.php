<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\ApproveAction;
use App\Models\Item;
use App\Models\User;

/**
 * Action to approve an item
 */
class ApproveItemAction
{
    public function __construct(
        private readonly ApproveAction $approveAction
    ) {
    }

    /**
     * Approve an item
     *
     * @param Item $item The item to approve
     * @param User $reviewedBy The admin approving the item
     * @return Item
     * @throws \Exception If item cannot be approved
     */
    public function execute(Item $item, User $reviewedBy): Item
    {
        if (!$item->approval) {
            throw new \Exception('Item does not have an approval record.');
        }

        $this->approveAction->execute($item->approval, $reviewedBy);

        return $item->fresh();
    }
}
