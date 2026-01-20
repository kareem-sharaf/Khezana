<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Offer Status Enum
 * 
 * Represents the lifecycle status of an offer:
 * - PENDING: Offer is waiting for request owner's response
 * - ACCEPTED: Offer has been accepted by request owner
 * - REJECTED: Offer has been rejected by request owner
 * - CANCELLED: Offer has been cancelled by offer owner
 */
enum OfferStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('offers.status.pending'),
            self::ACCEPTED => __('offers.status.accepted'),
            self::REJECTED => __('offers.status.rejected'),
            self::CANCELLED => __('offers.status.cancelled'),
        };
    }

    /**
     * Get badge color for Filament UI
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'gray',
        };
    }

    /**
     * Check if status is final (cannot be changed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REJECTED]);
    }

    /**
     * Check if offer can be updated
     */
    public function canBeUpdated(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if offer is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if offer is accepted
     */
    public function isAccepted(): bool
    {
        return $this === self::ACCEPTED;
    }

    /**
     * Check if offer is rejected
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    /**
     * Check if offer is cancelled
     */
    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    /**
     * Get options array for select fields
     */
    public static function options(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::ACCEPTED->value => self::ACCEPTED->label(),
            self::REJECTED->value => self::REJECTED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }
}
