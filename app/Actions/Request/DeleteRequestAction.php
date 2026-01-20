<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Models\Request;
use Illuminate\Support\Facades\DB;

/**
 * Action to delete a request
 */
class DeleteRequestAction
{
    /**
     * Delete a request
     *
     * @param Request $request The request to delete
     * @return bool
     * @throws \Exception If deletion fails
     */
    public function execute(Request $request): bool
    {
        return DB::transaction(function () use ($request) {
            // Delete associated attributes
            $request->itemAttributes()->delete();
            
            // Delete associated approval (if exists)
            $request->approvalRelation()->delete();
            
            // Delete the request
            return $request->delete();
        });
    }
}
