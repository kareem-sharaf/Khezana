<?php

declare(strict_types=1);

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Action to reject an offer
 */
class RejectOfferAction
{
    /**
     * Reject an offer
     *
     * @param Offer $offer The offer to reject
     * @param User $user The user rejecting the offer (request owner)
     * @return Offer
     * @throws \Exception If offer cannot be rejected
     */
    public function execute(Offer $offer, User $user): Offer
    {
        // Only request owner can reject
        if ($offer->request->user_id !== $user->id) {
            throw new \Exception(__('offers.validation.only_request_owner_can_reject'));
        }

        // Cannot reject non-pending offers
        if (!$offer->isPending()) {
            throw new \Exception(__('offers.validation.cannot_reject_non_pending'));
        }

        return DB::transaction(function () use ($offer) {
            $offer->update(['status' => OfferStatus::REJECTED]);
            return $offer->fresh();
        });
    }
}
