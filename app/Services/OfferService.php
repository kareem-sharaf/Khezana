<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OfferStatus;
use App\Enums\OperationType;
use App\Enums\RequestStatus;
use App\Models\Offer;
use App\Models\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Offer Service
 * 
 * Handles business logic for offers:
 * - Validates offer rules
 * - Validates operation type rules
 * - Ensures request can receive offers
 * - Handles offer acceptance and rejection
 */
class OfferService
{
    /**
     * Validate offer data
     *
     * @param array $data Offer data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateOfferData(array $data): void
    {
        $rules = [
            'request_id' => 'required|exists:requests,id',
            'operation_type' => 'required|in:sell,rent,donate',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'item_id' => 'nullable|exists:items,id',
        ];

        Validator::make($data, $rules)->validate();
    }

    /**
     * Validate operation type rules
     *
     * @param array $data Offer data
     * @throws \Exception If validation fails
     */
    public function validateOperationTypeRules(array $data): void
    {
        $operationType = OperationType::from($data['operation_type']);

        switch ($operationType) {
            case OperationType::SELL:
                if (empty($data['price']) || $data['price'] <= 0) {
                    throw new \Exception(__('offers.validation.price_required_for_sell'));
                }
                break;

            case OperationType::RENT:
                if (empty($data['price']) || $data['price'] <= 0) {
                    throw new \Exception(__('offers.validation.price_required_for_rent'));
                }
                if (empty($data['deposit_amount']) || $data['deposit_amount'] <= 0) {
                    throw new \Exception(__('offers.validation.deposit_required_for_rent'));
                }
                break;

            case OperationType::DONATE:
                // No price required for donate
                break;
        }
    }

    /**
     * Ensure request can receive offers
     *
     * @param Request $request The request
     * @throws \Exception If request cannot receive offers
     */
    public function ensureRequestCanReceiveOffers(Request $request): void
    {
        // Request must be approved
        if (!$request->isApproved()) {
            throw new \Exception(__('offers.validation.request_not_approved'));
        }

        // Request must be open
        if (!$request->isOpen()) {
            throw new \Exception(__('offers.validation.request_not_open'));
        }

        // Request must not be fulfilled
        if ($request->isFulfilled()) {
            throw new \Exception(__('offers.validation.request_already_fulfilled'));
        }

        // Request must not be closed
        if ($request->isClosed()) {
            throw new \Exception(__('offers.validation.request_closed'));
        }
    }

    /**
     * Check if user already has an offer for this request
     *
     * @param Request $request The request
     * @param int $userId The user ID
     * @return bool
     */
    public function userHasOfferForRequest(Request $request, int $userId): bool
    {
        return Offer::where('request_id', $request->id)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Accept an offer
     *
     * @param Offer $offer The offer to accept
     * @return Offer
     * @throws \Exception If offer cannot be accepted
     */
    public function acceptOffer(Offer $offer): Offer
    {
        // Validate offer can be accepted
        if (!$offer->isPending()) {
            throw new \Exception(__('offers.validation.cannot_accept_non_pending'));
        }

        $request = $offer->request;

        // Ensure request can still receive offers
        $this->ensureRequestCanReceiveOffers($request);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($offer, $request) {
            // Accept the offer
            $offer->update(['status' => OfferStatus::ACCEPTED]);

            // Fulfill the request
            $request->update(['status' => RequestStatus::FULFILLED]);

            // Reject all other pending offers for this request
            $this->rejectOtherOffers($offer);

            return $offer->fresh();
        });
    }

    /**
     * Reject all other offers for the same request
     *
     * @param Offer $acceptedOffer The accepted offer
     * @return int Number of offers rejected
     */
    public function rejectOtherOffers(Offer $acceptedOffer): int
    {
        return Offer::where('request_id', $acceptedOffer->request_id)
            ->where('id', '!=', $acceptedOffer->id)
            ->where('status', OfferStatus::PENDING->value)
            ->update(['status' => OfferStatus::REJECTED->value]);
    }

    /**
     * Cancel an offer
     *
     * @param Offer $offer The offer to cancel
     * @return Offer
     * @throws \Exception If offer cannot be cancelled
     */
    public function cancelOffer(Offer $offer): Offer
    {
        if (!$offer->isPending()) {
            throw new \Exception(__('offers.validation.cannot_cancel_non_pending'));
        }

        $offer->update(['status' => OfferStatus::CANCELLED]);

        return $offer->fresh();
    }
}
