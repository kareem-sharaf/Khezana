<?php

declare(strict_types=1);

namespace App\Actions\Offer;

use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Support\Facades\DB;

/**
 * Action to update an existing offer
 */
class UpdateOfferAction
{
    public function __construct(
        private readonly OfferService $offerService
    ) {
    }

    /**
     * Update an offer
     *
     * @param Offer $offer The offer to update
     * @param array $data Updated data
     * @return Offer
     * @throws \Exception If validation fails or offer cannot be updated
     */
    public function execute(Offer $offer, array $data): Offer
    {
        // Cannot update non-pending offers
        if (!$offer->canBeUpdated()) {
            throw new \Exception(__('offers.validation.cannot_update_final'));
        }

        // Validate offer data
        $this->offerService->validateOfferData(array_merge($data, ['request_id' => $offer->request_id]));

        // Validate operation type rules
        $this->offerService->validateOperationTypeRules($data);

        // Ensure request can still receive offers
        $this->offerService->ensureRequestCanReceiveOffers($offer->request);

        return DB::transaction(function () use ($offer, $data) {
            // Update the offer
            $offer->update([
                'item_id' => $data['item_id'] ?? $offer->item_id,
                'operation_type' => $data['operation_type'] ?? $offer->operation_type->value,
                'price' => $data['price'] ?? $offer->price,
                'deposit_amount' => $data['deposit_amount'] ?? $offer->deposit_amount,
                'message' => $data['message'] ?? $offer->message,
            ]);

            return $offer->fresh(['request', 'user', 'item']);
        });
    }
}
