<?php

declare(strict_types=1);

namespace App\Actions\Offer;

use App\Models\Offer;
use App\Models\User;
use App\Services\OfferService;

/**
 * Action to cancel an offer
 */
class CancelOfferAction
{
    public function __construct(
        private readonly OfferService $offerService
    ) {
    }

    /**
     * Cancel an offer
     *
     * @param Offer $offer The offer to cancel
     * @param User $user The user cancelling the offer
     * @return Offer
     * @throws \Exception If offer cannot be cancelled
     */
    public function execute(Offer $offer, User $user): Offer
    {
        // Only offer owner can cancel
        if ($offer->user_id !== $user->id) {
            throw new \Exception(__('offers.validation.only_owner_can_cancel'));
        }

        // Use service to cancel
        return $this->offerService->cancelOffer($offer);
    }
}
