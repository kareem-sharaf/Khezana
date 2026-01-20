<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

/**
 * Offer Policy
 * 
 * Authorization rules:
 * - User can view offers on requests they own or offers they made
 * - All authenticated users can create offers
 * - Only offer owner can update/cancel (if pending)
 * - Only request owner can accept/reject offers
 * - Admin can manage all offers
 */
class OfferPolicy
{
    /**
     * Determine if the user can view any offers.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view offers
    }

    /**
     * Determine if the user can view the offer.
     */
    public function view(User $user, Offer $offer): bool
    {
        // User can view if:
        // - They made the offer
        // - They own the request
        // - They are admin
        return $user->id === $offer->user_id 
            || $user->id === $offer->request->user_id 
            || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create offers.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create offers
    }

    /**
     * Determine if the user can update the offer.
     */
    public function update(User $user, Offer $offer): bool
    {
        // Only offer owner can update, and only if pending
        return $user->id === $offer->user_id 
            && $offer->canBeUpdated() 
            || $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can delete the offer.
     */
    public function delete(User $user, Offer $offer): bool
    {
        // Only offer owner or super admin can delete
        return $user->id === $offer->user_id || $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can cancel the offer.
     */
    public function cancel(User $user, Offer $offer): bool
    {
        // Only offer owner can cancel, and only if pending
        return $user->id === $offer->user_id && $offer->canBeUpdated();
    }

    /**
     * Determine if the user can accept the offer.
     */
    public function accept(User $user, Offer $offer): bool
    {
        // Only request owner can accept
        return $user->id === $offer->request->user_id;
    }

    /**
     * Determine if the user can reject the offer.
     */
    public function reject(User $user, Offer $offer): bool
    {
        // Only request owner can reject
        return $user->id === $offer->request->user_id;
    }

    /**
     * Determine if the user can manage (force reject) offers.
     */
    public function manage(User $user, Offer $offer): bool
    {
        // Only admins can manage offers
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }
}
