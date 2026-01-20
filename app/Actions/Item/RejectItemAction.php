<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\RejectAction;
use App\Models\Item;
use App\Models\User;

/**
 * Action to reject an item
 */
class RejectItemAction
{
    public function __construct(
        private readonly RejectAction $rejectAction
    ) {
    }

    /**
     * Reject an item
     *
     * @param Item $item The item to reject
     * @param User $reviewedBy The admin rejecting the item
     * @param string|null $rejectionReason The reason for rejection
     * @return Item
     * @throws \Exception If item cannot be rejected
     */
    public function execute(Item $item, User $reviewedBy, ?string $rejectionReason = null): Item
    {
        if (!$item->approval) {
            throw new \Exception('Item does not have an approval record.');
        }

        $this->rejectAction->execute($item->approval, $reviewedBy, $rejectionReason);

        return $item->fresh();
    }
}
