<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Offer\AcceptOfferAction;
use App\Actions\Offer\CancelOfferAction;
use App\Actions\Offer\CreateOfferAction;
use App\Actions\Offer\RejectOfferAction;
use App\Actions\Offer\UpdateOfferAction;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Request as RequestModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Offer Controller
 * 
 * Handles user-facing offer operations
 * All business logic is delegated to Actions
 */
class OfferController extends Controller
{
    public function __construct(
        private readonly CreateOfferAction $createOfferAction,
        private readonly UpdateOfferAction $updateOfferAction,
        private readonly CancelOfferAction $cancelOfferAction,
        private readonly AcceptOfferAction $acceptOfferAction,
        private readonly RejectOfferAction $rejectOfferAction
    ) {
        // Middleware is applied in routes/web.php
    }

    /**
     * Show the form for creating a new offer
     */
    public function create(RequestModel $request): View|RedirectResponse
    {
        $this->authorize('create', Offer::class);

        if ((int) $request->user_id === (int) Auth::id()) {
            return redirect()
                ->route('requests.show', $request)
                ->with('error', __('offers.validation.owner_cannot_offer_self'));
        }

        // Get user's items for selection (only approved items)
        $userItems = Item::where('user_id', Auth::id())
            ->where('is_available', true)
            ->approved()
            ->with('category')
            ->get();

        return view('offers.create', compact('request', 'userItems'));
    }

    /**
     * Store a newly created offer
     */
    public function store(HttpRequest $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('create', Offer::class);

        $validated = $httpRequest->validate([
            'operation_type' => 'required|in:sell,rent,donate',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'item_id' => 'nullable|exists:items,id',
        ]);

        try {
            $this->createOfferAction->execute(
                $validated,
                $request,
                Auth::user()
            );
        } catch (\Exception $e) {
            return redirect()->route('requests.show', $request)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('requests.show', $request)
            ->with('success', __('offers.messages.created_successfully'));
    }

    /**
     * Show the form for editing the specified offer
     */
    public function edit(Offer $offer): View
    {
        $this->authorize('update', $offer);

        // Get user's items for selection (only approved items)
        $userItems = Item::where('user_id', Auth::id())
            ->where('is_available', true)
            ->approved()
            ->with('category')
            ->get();

        return view('offers.edit', compact('offer', 'userItems'));
    }

    /**
     * Update the specified offer
     */
    public function update(HttpRequest $httpRequest, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $validated = $httpRequest->validate([
            'operation_type' => 'required|in:sell,rent,donate',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'item_id' => 'nullable|exists:items,id',
        ]);

        $this->updateOfferAction->execute($offer, $validated);

        return redirect()->route('requests.show', $offer->request)
            ->with('success', __('offers.messages.updated_successfully'));
    }

    /**
     * Cancel the specified offer
     */
    public function cancel(Offer $offer): RedirectResponse
    {
        $this->authorize('cancel', $offer);

        $this->cancelOfferAction->execute($offer, Auth::user());

        return redirect()->route('requests.show', $offer->request)
            ->with('success', __('offers.messages.cancelled_successfully'));
    }

    /**
     * Accept the specified offer
     */
    public function accept(Offer $offer): RedirectResponse
    {
        $this->authorize('accept', $offer);

        $this->acceptOfferAction->execute($offer, Auth::user());

        return redirect()->route('requests.show', $offer->request)
            ->with('success', __('offers.messages.accepted_successfully'));
    }

    /**
     * Reject the specified offer
     */
    public function reject(Offer $offer): RedirectResponse
    {
        $this->authorize('reject', $offer);

        $this->rejectOfferAction->execute($offer, Auth::user());

        return redirect()->route('requests.show', $offer->request)
            ->with('success', __('offers.messages.rejected_successfully'));
    }
}
