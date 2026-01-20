<?php

declare(strict_types=1);

namespace App\Actions\Offer;

use App\Models\Offer;
use App\Models\User;
use App\Services\OfferService;

/**
 * Action to accept an offer
 */
class AcceptOfferAction
{
    public function __construct(
        private readonly OfferService $offerService
    ) {
    }

    /**
     * Accept an offer
     *
     * @param Offer $offer The offer to accept
     * @param User $user The user accepting the offer (request owner)
     * @return Offer
     * @throws \Exception If offer cannot be accepted
     */
    public function execute(Offer $offer, User $user): Offer
    {
        // Only request owner can accept
        if ($offer->request->user_id !== $user->id) {
            throw new \Exception(__('offers.validation.only_request_owner_can_accept'));
        }

        // Use service to accept
        return $this->offerService->acceptOffer($offer);
    }
}
