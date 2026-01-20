<?php

declare(strict_types=1);

namespace App\Actions\Item;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

/**
 * Action to delete an item
 */
class DeleteItemAction
{
    /**
     * Delete an item
     *
     * @param Item $item The item to delete
     * @return bool
     * @throws \Exception If deletion fails
     */
    public function execute(Item $item): bool
    {
        return DB::transaction(function () use ($item) {
            // Delete associated images
            $item->images()->delete();
            
            // Delete associated attributes
            $item->itemAttributes()->delete();
            
            // Delete the item
            return $item->delete();
        });
    }
}
