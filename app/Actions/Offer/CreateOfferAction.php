<?php

declare(strict_types=1);

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\Request;
use App\Models\User;
use App\Services\OfferService;
use Illuminate\Support\Facades\DB;

/**
 * Action to create a new offer
 */
class CreateOfferAction
{
    public function __construct(
        private readonly OfferService $offerService
    ) {
    }

    /**
     * Create a new offer
     *
     * @param array $data Offer data
     * @param Request $request The request to offer on
     * @param User $user The user making the offer
     * @return Offer
     * @throws \Exception If validation fails
     */
    public function execute(array $data, Request $request, User $user): Offer
    {
        // Validate offer data
        $this->offerService->validateOfferData($data);

        // Validate operation type rules
        $this->offerService->validateOperationTypeRules($data);

        // Request owner cannot offer on their own request
        if ((int) $request->user_id === (int) $user->id) {
            throw new \Exception(__('offers.validation.owner_cannot_offer_self'));
        }

        // Ensure request can receive offers
        $this->offerService->ensureRequestCanReceiveOffers($request);

        // Check if user already has an offer for this request
        if ($this->offerService->userHasOfferForRequest($request, $user->id)) {
            throw new \Exception(__('offers.validation.duplicate_offer'));
        }

        // Validate item if offer is linked to an item (BR-024)
        if (isset($data['item_id']) && $data['item_id']) {
            $item = \App\Models\Item::findOrFail($data['item_id']);
            $item->ensureCanReceiveOffers();
        }

        return DB::transaction(function () use ($data, $request, $user) {
            // Create the offer
            $offer = Offer::create([
                'request_id' => $request->id,
                'user_id' => $user->id,
                'item_id' => $data['item_id'] ?? null,
                'operation_type' => $data['operation_type'],
                'price' => $data['price'] ?? null,
                'deposit_amount' => $data['deposit_amount'] ?? null,
                'status' => OfferStatus::PENDING,
                'message' => $data['message'] ?? null,
            ]);

            return $offer->fresh(['request', 'user', 'item']);
        });
    }
}
