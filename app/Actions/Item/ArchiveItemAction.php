<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Actions\Approval\ArchiveAction;
use App\Models\Item;
use App\Models\User;

/**
 * Action to archive an item
 */
class ArchiveItemAction
{
    public function __construct(
        private readonly ArchiveAction $archiveAction
    ) {
    }

    /**
     * Archive an item
     *
     * @param Item $item The item to archive
     * @param User $reviewedBy The admin archiving the item
     * @return Item
     * @throws \Exception If item cannot be archived
     */
    public function execute(Item $item, User $reviewedBy): Item
    {
        if (!$item->approval) {
            throw new \Exception('Item does not have an approval record.');
        }

        $this->archiveAction->execute($item->approval, $reviewedBy);

        return $item->fresh();
    }
}
